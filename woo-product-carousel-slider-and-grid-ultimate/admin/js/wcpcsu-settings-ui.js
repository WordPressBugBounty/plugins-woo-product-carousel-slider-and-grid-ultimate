/**
 * Settings screen layout: a section menu inside each settings tab, and help tooltips.
 *
 * It only arranges the existing rows. Every field stays in the form with its name and
 * value, so saving and the live preview are unchanged. Without this script the
 * sections are shown one after another with their headings.
 *
 * Byte-identical in Free and Pro: keep the two copies in sync.
 */
jQuery(function ($) {
    var container = document.getElementById('lcsp-tabs-container');
    var i18n = window.wcpcsuSettingsUi || {};
    var STORE = 'wcpcsuSettingsSection';
    // Descriptions up to this length stay under the field; longer ones move into a tooltip.
    var SHORT_HELP = 60;
    var groups = [];

    if (!container) {
        return;
    }

    function readStore() {
        try {
            return JSON.parse(window.localStorage.getItem(STORE)) || {};
        } catch (e) {
            return {};
        }
    }

    function writeStore(key, value) {
        try {
            var store = readStore();
            store[key] = value;
            window.localStorage.setItem(STORE, JSON.stringify(store));
        } catch (e) {
            // Private mode or blocked storage: the first section opens next time.
        }
    }

    // Rows hidden by a layout, a theme or a Yes/No switch (CSS or inline style).
    function isShown(row) {
        return window.getComputedStyle(row).display !== 'none';
    }

    /**
     * Section menu for one tab: one button per <section class="wcpscu-section">.
     */
    function initSections(group, index) {
        var tab = group.closest('.lcsp-tab-content');
        var key = tab && tab.id ? tab.id : 'tab-' + index;
        var sections = Array.prototype.filter.call(group.children, function (child) {
            return child.classList.contains('wcpscu-section');
        });
        var nav = document.createElement('div');
        var active = 0;

        if (sections.length < 2) {
            return;
        }

        nav.className = 'wcpscu-sections__nav';
        nav.setAttribute('role', 'tablist');
        nav.setAttribute('aria-orientation', 'vertical');
        if (i18n.sections) {
            nav.setAttribute('aria-label', i18n.sections);
        }

        var buttons = sections.map(function (section) {
            var title = section.querySelector('.wcpscu-section__title');
            var id = 'wcpscu-section-' + key + '-' + section.getAttribute('data-wcpscu-section');
            var button = document.createElement('button');

            section.id = id;
            section.setAttribute('role', 'tabpanel');
            section.setAttribute('aria-labelledby', id + '-tab');
            button.type = 'button';
            button.id = id + '-tab';
            button.className = 'wcpscu-sections__link';
            button.setAttribute('role', 'tab');
            button.setAttribute('aria-controls', id);
            button.textContent = title ? title.textContent : '';
            nav.appendChild(button);

            return button;
        });

        // After a click in the menu, bring the section's top into view below the pinned preview.
        function reveal() {
            var pinned = document.querySelector('.wcpcsu-preview-box--pinned');
            var bar = document.getElementById('wpadminbar');
            var offset = Math.max(pinned && getComputedStyle(pinned).position === 'sticky' ? pinned.getBoundingClientRect().bottom : 0, bar ? bar.offsetHeight : 0) + 12;
            var top = group.getBoundingClientRect().top;

            if (top < offset) {
                window.scrollBy(0, top - offset);
            }
        }

        function select(next, focus) {
            active = next;
            sections.forEach(function (section, i) {
                var on = i === next;
                section.classList.toggle('is-active', on);
                buttons[i].setAttribute('aria-selected', on ? 'true' : 'false');
                buttons[i].tabIndex = on ? 0 : -1;
            });
            if (focus) {
                buttons[next].focus();
            }
            writeStore(key, sections[next].getAttribute('data-wcpscu-section'));
        }

        // A section whose rows the current layout/theme all hide gets no menu entry.
        function refresh() {
            var firstShown = -1;

            sections.forEach(function (section, i) {
                var rows = section.querySelectorAll('.cmb-row');
                var shown = !rows.length || Array.prototype.some.call(rows, isShown);
                buttons[i].hidden = !shown;
                if (shown && firstShown < 0) {
                    firstShown = i;
                }
            });
            if (buttons[active].hidden && firstShown >= 0) {
                select(firstShown, false);
            }
        }

        nav.addEventListener('click', function (event) {
            var index = buttons.indexOf(event.target.closest('.wcpscu-sections__link'));
            if (index >= 0) {
                select(index, false);
                reveal();
            }
        });

        nav.addEventListener('keydown', function (event) {
            var shown = buttons.filter(function (button) {
                return !button.hidden;
            });
            var at = shown.indexOf(document.activeElement);
            var keys = {
                ArrowDown: at + 1,
                ArrowRight: at + 1,
                ArrowUp: at - 1,
                ArrowLeft: at - 1,
                Home: 0,
                End: shown.length - 1
            };

            if (at < 0 || !(event.key in keys)) {
                return;
            }
            event.preventDefault();
            select(buttons.indexOf(shown[(keys[event.key] + shown.length) % shown.length]), true);
            reveal();
        });

        group.insertBefore(nav, group.firstChild);
        group.classList.add('wcpscu-sections--ready');

        var stored = readStore()[key];
        var start = sections.map(function (section) {
            return section.getAttribute('data-wcpscu-section');
        }).indexOf(stored);
        select(start >= 0 ? start : 0, false);
        refresh();
        groups.push(refresh);
    }

    /**
     * Move long descriptions of a row into a help tooltip next to its label. The description
     * elements themselves move, so scripts and CSS that show or hide them keep working.
     */
    function initHelp(row, index) {
        var td = row.querySelector(':scope > .cmb-td');
        var th = row.querySelector(':scope > .cmb-th');
        var label = th && th.querySelector('label');
        var notes = td ? Array.prototype.filter.call(td.children, function (child) {
            return child.matches('p.cmb2-metabox-description');
        }) : [];
        var length = notes.reduce(function (sum, note) {
            return sum + note.textContent.trim().length;
        }, 0);

        if (!label || !label.textContent.trim() || length <= SHORT_HELP) {
            return;
        }

        var help = document.createElement('div');
        var toggle = document.createElement('button');
        var text = document.createElement('div');

        help.className = 'wcpscu-help';
        toggle.type = 'button';
        toggle.className = 'wcpscu-help__toggle';
        toggle.setAttribute('aria-expanded', 'false');
        toggle.setAttribute('aria-describedby', 'wcpscu-help-' + index);
        toggle.innerHTML = '<span aria-hidden="true">?</span>';
        if (i18n.help) {
            toggle.setAttribute('aria-label', i18n.help);
        }
        text.className = 'wcpscu-help__text';
        text.id = 'wcpscu-help-' + index;
        text.setAttribute('role', 'tooltip');
        notes.forEach(function (note) {
            text.appendChild(note);
        });
        help.appendChild(toggle);
        help.appendChild(text);
        label.after(help);
    }

    function closeHelp(except) {
        container.querySelectorAll('.wcpscu-help.is-open').forEach(function (help) {
            if (help !== except) {
                help.classList.remove('is-open');
                help.querySelector('.wcpscu-help__toggle').setAttribute('aria-expanded', 'false');
            }
        });
    }

    /**
     * Products Type: the fields some types keep inside their radio item (category, timeframe,
     * IDs, …) move into one box under the list, so the radios form an even grid. The scripts
     * that show the selected type's fields find them by id and class, wherever they are.
     */
    function initProductTypeFields(list) {
        var box = document.createElement('div');
        box.className = 'wcpscu-products-type-fields';

        Array.prototype.forEach.call(list.children, function (item) {
            var radio = item.querySelector(':scope > input[type="radio"]');
            Array.prototype.slice.call(item.children).forEach(function (child) {
                var own = child === radio || (radio && child.tagName === 'LABEL' && child.htmlFor === radio.id);
                if (!own) {
                    box.appendChild(child);
                }
            });
        });
        if (box.children.length) {
            list.after(box);
        }
    }

    container.querySelectorAll('.wcpscu-products-types .cmb-td > .cmb2-radio-list').forEach(initProductTypeFields);
    container.querySelectorAll('.cmb-row').forEach(initHelp);
    container.querySelectorAll('.wcpscu-sections').forEach(initSections);

    // A click opens a tooltip for touch screens and keeps it open; Escape or a click elsewhere closes it.
    document.addEventListener('click', function (event) {
        var toggle = event.target.closest && event.target.closest('.wcpscu-help__toggle');
        var help = toggle && toggle.parentNode;

        closeHelp(help);
        if (help) {
            var open = !help.classList.contains('is-open');
            help.classList.toggle('is-open', open);
            toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
        }
    });
    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            closeHelp(null);
        }
    });

    // Layout tiles, switches and the theme select show and hide rows: update the menus after their own handlers.
    function refreshAll() {
        window.setTimeout(function () {
            groups.forEach(function (refresh) {
                refresh();
            });
        }, 0);
    }
    $(container).on('click change', refreshAll);
    new MutationObserver(refreshAll).observe(container, { attributes: true });
});
