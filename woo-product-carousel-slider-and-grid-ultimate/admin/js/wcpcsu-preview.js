/**
 * Live preview on the carousel edit screen.
 *
 * Sends the unsaved wcpscu[...] form values to POST /wcpcsu/v1/preview
 * (WCPCSU_Preview) and shows the returned frontend HTML in an iframe. The
 * iframe is built once with the frontend CSS/JS; later updates only swap the
 * markup and call wpcuCarousel.init() inside it. Nothing is saved.
 */
jQuery(function ($) {
    var config = window.wcpcsuPreview;
    var panel = document.querySelector('.wcpcsu-preview');
    var form = document.getElementById('post');

    if (!config || !panel || !form) {
        return;
    }

    var stage = panel.querySelector('.wcpcsu-preview__stage');
    var wrap = panel.querySelector('.wcpcsu-preview__frame-wrap');
    var frame = panel.querySelector('.wcpcsu-preview__frame');
    var status = panel.querySelector('.wcpcsu-preview__status');
    var postIdField = document.getElementById('post_ID');
    var pin = panel.querySelector('.wcpcsu-preview__pin');
    var box = panel.closest('.postbox');
    var pinQuery = window.matchMedia('(min-width: 783px) and (min-height: 700px)');

    var viewportWidth = 1280;
    var contentHeight = 300;
    var timer = null;
    var controller = null;
    var requestId = 0;
    var assetsKey = '';
    var frameReady = null;

    /**
     * Collect the wcpscu[...] fields into a nested object, like PHP builds $_POST['wcpscu'].
     *
     * @return {Object} Settings.
     */
    function collectSettings() {
        var settings = {};

        new FormData(form).forEach(function (value, key) {
            if (key.indexOf('wcpscu[') !== 0 || typeof value !== 'string') {
                return;
            }

            var path = key.slice(6).match(/\[[^\]]*\]/g).map(function (part) {
                return part.slice(1, -1);
            });
            var node = settings;

            path.forEach(function (part, i) {
                // "[]" appends, as in PHP.
                if (part === '') {
                    part = Array.isArray(node) ? node.length : Object.keys(node).length;
                }

                if (i === path.length - 1) {
                    node[part] = value;
                    return;
                }

                if (!node[part] || typeof node[part] !== 'object') {
                    node[part] = path[i + 1] === '' ? [] : {};
                }
                node = node[part];
            });
        });

        return settings;
    }

    function escapeAttr(value) {
        return String(value).replace(/&/g, '&amp;').replace(/"/g, '&quot;').replace(/</g, '&lt;');
    }

    function setStatus(message, isError) {
        status.textContent = message || '';
        status.classList.toggle('is-error', !!isError);
    }

    /**
     * Fit the iframe into the panel. The iframe keeps the device's real width
     * (so media queries and Swiper breakpoints match), and is scaled down when
     * the panel is narrower. Pinned, it also fits in 30% of the window's height,
     * so the settings below stay usable.
     */
    function layout() {
        var padding = parseFloat(getComputedStyle(stage).paddingLeft) + parseFloat(getComputedStyle(stage).paddingRight);
        var available = stage.clientWidth - padding;
        var scale = available && available < viewportWidth ? available / viewportWidth : 1;

        if (box && box.classList.contains('wcpcsu-preview-box--pinned') && pinQuery.matches) {
            scale = Math.min(scale, (window.innerHeight * 0.3) / contentHeight);
        }

        frame.style.width = viewportWidth + 'px';
        frame.style.height = contentHeight + 'px';
        frame.style.transform = scale < 1 ? 'scale(' + scale + ')' : '';
        wrap.style.width = Math.floor(viewportWidth * scale) + 'px';
        wrap.style.height = Math.ceil(contentHeight * scale) + 'px';
    }

    /**
     * Build the iframe document with the frontend assets.
     *
     * @param {Object} assets { styles: string[], scripts: string[] }
     * @return {Promise} Resolves when the iframe and its scripts have loaded.
     */
    function buildFrame(assets) {
        var head = (assets.styles || []).map(function (href) {
            return '<link rel="stylesheet" href="' + escapeAttr(href) + '">';
        }).join('');
        var scripts = (assets.scripts || []).map(function (src) {
            return '<script src="' + escapeAttr(src) + '"></scr' + 'ipt>';
        }).join('');

        return new Promise(function (resolve) {
            frame.onload = function () {
                var doc = frame.contentDocument;
                var win = frame.contentWindow;

                // Keep clicks inside the preview: no navigation, add to cart or form submits.
                doc.addEventListener('click', function (event) {
                    if (event.target.closest && event.target.closest('a, button, input[type="submit"]')) {
                        event.preventDefault();
                    }
                }, true);
                doc.addEventListener('submit', function (event) {
                    event.preventDefault();
                }, true);

                // Grow the iframe with its content (slides, images, header).
                new win.ResizeObserver(function () {
                    contentHeight = Math.max(120, Math.ceil(doc.body.getBoundingClientRect().height));
                    layout();
                }).observe(doc.body);

                resolve();
            };

            frame.srcdoc = '<!DOCTYPE html><html><head><meta charset="utf-8">' +
                '<meta name="viewport" content="width=device-width, initial-scale=1">' + head +
                // The iframe grows with its content, so it never needs (stale) scrollbars.
                '<style>html{overflow:hidden}html,body{margin:0;background:#fff}body{padding:24px;box-sizing:border-box;' +
                'font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,sans-serif;color:#272b41}</style>' +
                '</head><body><div id="wcpcsu-preview-root"></div>' + scripts + '</body></html>';
        });
    }

    /**
     * Swap the carousel markup inside the iframe and start it.
     *
     * @param {string} html Shortcode output.
     */
    function render(html) {
        var root = frame.contentDocument.getElementById('wcpcsu-preview-root');
        var api = frame.contentWindow.wpcuCarousel;

        root.querySelectorAll('.wpcu-carousel').forEach(function (el) {
            if (el.swiper) {
                el.swiper.destroy(true, true);
            }
        });

        root.innerHTML = html;

        if (api) {
            api.init(root);
        }
    }

    function refresh() {
        var id = ++requestId;

        if (controller) {
            controller.abort();
        }
        controller = new AbortController();

        panel.classList.add('is-loading');
        setStatus(config.i18n.updating);

        fetch(config.restUrl, {
            method: 'POST',
            credentials: 'same-origin',
            signal: controller.signal,
            headers: {
                'Content-Type': 'application/json',
                'X-WP-Nonce': config.nonce
            },
            body: JSON.stringify({
                post_id: postIdField ? parseInt(postIdField.value, 10) : 0,
                settings: collectSettings()
            })
        })
            .then(function (response) {
                return response.json().catch(function () {
                    return {};
                }).then(function (data) {
                    if (!response.ok) {
                        var error = new Error(data.message || response.statusText);
                        error.code = data.code;
                        throw error;
                    }
                    return data;
                });
            })
            .then(function (data) {
                // Rebuild the iframe only when the asset list changes (first load, new versions).
                var key = JSON.stringify(data.assets || {});
                if (key !== assetsKey || !frameReady) {
                    assetsKey = key;
                    frameReady = buildFrame(data.assets || {});
                }

                return frameReady.then(function () {
                    if (id === requestId) {
                        render(data.html || '');
                    }
                });
            })
            .then(function () {
                if (id === requestId) {
                    panel.classList.remove('is-loading');
                    setStatus('');
                }
            })
            .catch(function (error) {
                if (error.name === 'AbortError' || id !== requestId) {
                    return;
                }
                panel.classList.remove('is-loading');
                setStatus('rest_cookie_invalid_nonce' === error.code ? config.i18n.expired : config.i18n.error + ' ' + error.message, true);
            });
    }

    function schedule() {
        clearTimeout(timer);
        timer = setTimeout(refresh, 400);
    }

    function isSettingsField(target) {
        return target && target.name && target.name.indexOf('wcpscu[') === 0;
    }

    form.addEventListener('input', function (event) {
        if (isSettingsField(event.target)) {
            schedule();
        }
    });
    form.addEventListener('change', function (event) {
        if (isSettingsField(event.target)) {
            schedule();
        }
    });

    // Color pickers (wcpcs-admin.js) and the carousel/grid switch, which sets the radio without a change event.
    $(document).on('wcpcsu:preview', schedule);
    $(document).on('click', '.cmd-switch-carousel, .cmd-switch-grid', schedule);
    // selectWoo (WooCommerce's enhanced selects) reports changes as jQuery events only, which native listeners miss.
    $(form).on('change', 'select[name^="wcpscu["]', schedule);

    panel.querySelectorAll('.wcpcsu-preview__device').forEach(function (button) {
        button.addEventListener('click', function () {
            panel.querySelectorAll('.wcpcsu-preview__device').forEach(function (other) {
                other.classList.toggle('is-active', other === button);
                other.setAttribute('aria-pressed', other === button ? 'true' : 'false');
            });
            viewportWidth = parseInt(button.getAttribute('data-width'), 10) || 1280;
            layout();
        });
    });

    // The panel width changes with the window, the sidebar and collapsing the meta box.
    new ResizeObserver(layout).observe(stage);
    window.addEventListener('resize', layout);

    // "Keep in view": the preview box stays at the top while the settings scroll (wcpcsu-admin.css).
    // On by default; the choice is remembered in this browser.
    function setPinned(on) {
        box.classList.toggle('wcpcsu-preview-box--pinned', on);
        pin.setAttribute('aria-pressed', on ? 'true' : 'false');
        layout();
    }

    if (pin && box) {
        var pinned = true;
        try {
            pinned = window.localStorage.getItem('wcpcsuPreviewPinned') !== '0';
        } catch (e) {
            // Storage blocked: keep the default.
        }
        setPinned(pinned);
        pin.addEventListener('click', function () {
            var on = pin.getAttribute('aria-pressed') !== 'true';
            setPinned(on);
            try {
                window.localStorage.setItem('wcpcsuPreviewPinned', on ? '1' : '0');
            } catch (e) {
                // Not remembered.
            }
        });
    }

    layout();
    refresh();
});
