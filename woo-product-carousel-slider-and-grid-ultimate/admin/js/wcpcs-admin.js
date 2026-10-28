jQuery(document).ready(function ($) {
    $(".lcsp-tabs-menu a").click(function (event) {
        event.preventDefault();
        $(this).parent().addClass("current");
        $(this).parent().siblings().removeClass("current");
        var tab = $(this).attr("href");
        $(".lcsp-tab-content").not(tab).css("display", "none");
        $(tab).fadeIn();
    });

    //color picker
    // The color picker doesn't fire input/change, so tell the live preview directly.
    jQuery('.cpa-color-picker').wpColorPicker({
        change: function () {
            $(document).trigger('wcpcsu:preview');
        },
        clear: function () {
            $(document).trigger('wcpcsu:preview');
        }
    });


    // Carousel effect: fade shows one product, so data-wcpscu-effect hides the columns row (wcpcsu-admin.css).
    $('.wcpscu_carousel_effect').on('change', function () {
        $('#lcsp-tabs-container').attr('data-wcpscu-effect', $(this).val());
    });

    // Layout tiles: check the layout's radio and show the settings tab it uses (data-tab).
    // data-wcpscu-layout hides the rows the layout doesn't use (wcpcsu-admin.css). Pro tiles do nothing.
    $('.cmd-switch > div:not(.cmd-switch-pro)').on('click', function (e) {
        e.preventDefault();
        var $tile  = $(this);
        var isGrid = 'grid' === $tile.data('tab');

        $tile.addClass('active').siblings().removeClass('active');
        $('.wcpscu_radio_layout').prop('checked', false);
        $tile.find('.wcpscu_radio_layout').prop('checked', true);
        $('#lcsp-tabs-container').attr('data-wcpscu-layout', $tile.data('layout'));
        $("#tab2").css('display', isGrid ? 'none' : 'block');
        $("#tab3").css('display', isGrid ? 'block' : 'none');
        $("#wcpscu_total_pdt").html(isGrid ? "Products Per Page" : "Total Products to Display");
        // The radio is set without a change event; wcpcsu-preview.js only watches the carousel/grid tiles itself.
        $(document).trigger('wcpcsu:preview');
    });

    /* Replace all SVG images with inline SVG */
    document.querySelectorAll('img.svg_compile').forEach((el) => {
        const imgID = el.getAttribute('id');
        const imgClass = el.getAttribute('class');
        const imgURL = el.getAttribute('src');

        fetch(imgURL)
            .then(data => data.text())
            .then(response => {
                const parser = new DOMParser();
                const xmlDoc = parser.parseFromString(response, 'text/html');
                let svg = xmlDoc.querySelector('svg');

                if (typeof imgID !== 'undefined') {
                    svg.setAttribute('id', imgID);
                }

                if (typeof imgClass !== 'undefined') {
                    svg.setAttribute('class', imgClass + ' replaced-svg');
                }

                svg.removeAttribute('xmlns:a');

                el.parentNode.replaceChild(svg, el);
            })
    });



    $('.theme_1, .theme_2, .theme_3').hide();

    var $theme = $('.wcpscu_theme'); // get theme jQuery object

    var currentTheme = $theme.val(); // get current theme

    $('.' + currentTheme).show(); // show current theme
    $theme.on('change', function () {
        var $this = $(this);

        ('theme_1' == $this.val()) ? $('.theme_1').show(): $('.theme_1').hide();
        ('theme_2' == $this.val()) ? $('.theme_2').show(): $('.theme_2').hide();
        ('theme_3' == $this.val()) ? $('.theme_3').show(): $('.theme_3').hide();

    });

    $('.wcpscu_radio_layout').hide();

    // products type dependable: rows with data-products-type (space-separated types) show only for those types
    function toggleProductsTypeRows() {
        var type = $("input[name='wcpscu[products_type]']:checked").val() || 'latest';
        $('.wcpscu-products-type-depend').each(function () {
            $(this).toggle(String($(this).data('products-type')).split(' ').indexOf(type) !== -1);
        });
    }
    // category filter tabs: their settings rows also need "Category Filter Tabs" = yes
    function toggleFilterTabsRows() {
        if ($("input[name='wcpscu[filter_tabs]']:checked").val() !== 'yes') {
            $('.wcpscu-filter-tabs-depend').hide();
        }
    }

    toggleProductsTypeRows();
    toggleFilterTabsRows();
    $("input[name='wcpscu[products_type]']").on('change', function () {
        toggleProductsTypeRows();
        toggleFilterTabsRows();
    });
    $("input[name='wcpscu[filter_tabs]']").on('change', function () {
        toggleProductsTypeRows();
        toggleFilterTabsRows();
    });

    // tab categories: add from the select, drag to reorder, remove (same list UI as handpicked products).
    var $tabCategoriesList = $('.wcpscu-tab-categories__list');

    if ($.fn.sortable) {
        $tabCategoriesList.sortable({
            handle: '.wcpscu-handpicked__handle',
            axis: 'y',
            update: function () {
                $(document).trigger('wcpcsu:preview');
            }
        });
    }

    $('#wcpscu_filter_categories_add').on('change', function () {
        var $select = $(this);
        var slug = $select.val();

        if (!slug) {
            return;
        }

        var exists = $tabCategoriesList.find('input').filter(function () {
            return this.value === slug;
        }).length;

        if (!exists) {
            var $item = $('<li class="wcpscu-handpicked__item">' +
                '<span class="dashicons dashicons-menu wcpscu-handpicked__handle" aria-hidden="true"></span>' +
                '<span class="wcpscu-handpicked__name"></span>' +
                '<button type="button" class="wcpscu-handpicked__remove">&times;</button>' +
                '<input type="hidden" name="wcpscu[filter_categories][]">' +
                '</li>');
            $item.find('.wcpscu-handpicked__name').text($.trim($select.find('option:selected').text()));
            $item.find('.wcpscu-handpicked__remove').attr('aria-label', $tabCategoriesList.data('remove-label'));
            $item.find('input').val(slug);
            $tabCategoriesList.append($item);
            $(document).trigger('wcpcsu:preview');
        }

        $select.val('');
    });

    $tabCategoriesList.on('click', '.wcpscu-handpicked__remove', function () {
        $(this).closest('.wcpscu-handpicked__item').remove();
        $(document).trigger('wcpcsu:preview');
    });

    // handpicked products: add from WooCommerce's product search, drag to reorder, remove.
    // The hidden inputs change without input/change events, so tell the live preview.
    var $handpickedList = $('.wcpscu-handpicked__list').not('.wcpscu-tab-categories__list');

    function handpickedChanged() {
        $(document).trigger('wcpcsu:preview');
    }

    if ($.fn.sortable) {
        $handpickedList.sortable({
            handle: '.wcpscu-handpicked__handle',
            axis: 'y',
            update: handpickedChanged
        });
    }

    $('#wcpscu_handpicked_search').on('change', function () {
        var $select = $(this);
        var id = $select.val();

        if (!id) {
            return;
        }

        if (!$handpickedList.find('input[value="' + parseInt(id, 10) + '"]').length) {
            var $item = $('<li class="wcpscu-handpicked__item">' +
                '<span class="dashicons dashicons-menu wcpscu-handpicked__handle" aria-hidden="true"></span>' +
                '<span class="wcpscu-handpicked__name"></span>' +
                '<button type="button" class="wcpscu-handpicked__remove">&times;</button>' +
                '<input type="hidden" name="wcpscu[handpicked_products][]">' +
                '</li>');
            $item.find('.wcpscu-handpicked__name').text($select.find('option:selected').text());
            $item.find('.wcpscu-handpicked__remove').attr('aria-label', $handpickedList.data('remove-label'));
            $item.find('input').val(parseInt(id, 10));
            $handpickedList.append($item);
            handpickedChanged();
        }

        // Clear the search box for the next product (this change has no value, so it returns above).
        $select.val(null).trigger('change');
    });

    $handpickedList.on('click', '.wcpscu-handpicked__remove', function () {
        $(this).closest('.wcpscu-handpicked__item').remove();
        handpickedChanged();
    });

    // autoplay dependable
    if ($("input[name='wcpscu[A_play]']:checked").val() === 'yes') {
        $('.wpcu_auto_play_depend').show();
    } else {
        $('.wpcu_auto_play_depend').hide();
    }

    $('.wcpcu_auto_play').on('click', function () {
        if ($(this).val() === 'yes') {
            $('.wpcu_auto_play_depend').show();
        } else {
            $('.wpcu_auto_play_depend').hide();
        }
    });

    //navigation dependable
    if ($("input[name='wcpscu[nav_show]']:checked").val() === 'yes') {
        $('.wpcu_navigation_depend').show();
    } else {
        $('.wpcu_navigation_depend').hide();
    }

    $('.wcpcu_navigation').on('click', function () {
        if ($(this).val() === 'yes') {
            $('.wpcu_navigation_depend').show();
        } else {
            $('.wpcu_navigation_depend').hide();
        }
    });

    //caraousel pagination dependable
    if ($("input[name='wcpscu[carousel_pagination]']:checked").val() === 'yes') {
        $('.wpcu_carousel_pagination_depend').show();
    } else {
        $('.wpcu_carousel_pagination_depend').hide();
    }

    $('.wcpcu_carousel_pagination').on('click', function () {
        if ($(this).val() === 'yes') {
            $('.wpcu_carousel_pagination_depend').show();
        } else {
            $('.wpcu_carousel_pagination_depend').hide();
        }
    });
    // grid pagination type only matters when pagination is on
    function toggleGridPaginationRows() {
        $('.wcpscu-grid-pagination-depend').toggle($("input[name='wcpscu[grid_pagination]']:checked").val() === 'yes');
    }

    toggleGridPaginationRows();
    $("input[name='wcpscu[grid_pagination]']").on('change', toggleGridPaginationRows);


});