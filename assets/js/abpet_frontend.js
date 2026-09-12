//slick slider for related product
(function ($) {
    "use strict";
    $(document).ready(function () {
        var $relatedGrid = $('div.abpet_area .related_item_area .abpet_grid');
        $relatedGrid.slick({
            dots: false,
            arrows: false,
            infinite: true,
            centerMode: false,
            autoplay: true,
            autoplaySpeed: 4000,
            centerPadding: '0',
            slidesToShow: 3,
            slidesToScroll: 1,
            responsive: [
                {
                    breakpoint: 1000,
                    settings: {
                        slidesToShow: 3,
                        slidesToScroll: 1,
                        infinite: true,
                        dots: false,
                        centerMode: false
                    }
                },
                {
                    breakpoint: 700,
                    settings: {
                        slidesToShow: 2,
                        slidesToScroll: 1,
                        infinite: true,
                        dots: false,
                        centerMode: false
                    }
                },
                {
                    breakpoint: 500,
                    settings: {
                        slidesToShow: 1,
                        slidesToScroll: 1,
                        centerMode: false
                    }
                }
            ]
        });
        $('.abpet_area .related_item_area .related_prev').on('click', function () {
            $relatedGrid.slick('slickPrev');
        });
        $('.abpet_area .related_item_area .related_next').on('click', function () {
            $relatedGrid.slick('slickNext');
        });
    });
}(jQuery));

(function ($) {
    'use strict';
    $(document).on('click', '.abpet_schedule_toggle', function (event) {
        event.preventDefault();
        var $button = $(this);
        var $dropdown = $button.siblings('.abpet_schedule_dropdown');
        var isOpen = $button.attr('aria-expanded') === 'true';
        $('.abpet_schedule_toggle').not($button).attr('aria-expanded', 'false');
        $('.abpet_schedule_dropdown').not($dropdown).prop('hidden', true);
        $button.attr('aria-expanded', isOpen ? 'false' : 'true');
        $dropdown.prop('hidden', isOpen);
    });
    $(document).on('click', function (event) {
        if (!$(event.target).closest('.abpet_schedule_picker').length) {
            $('.abpet_schedule_toggle').attr('aria-expanded', 'false');
            $('.abpet_schedule_dropdown').prop('hidden', true);
        }
    });
    $(document).on('keydown', function (event) {
        if (event.key === 'Escape') {
            $('.abpet_schedule_toggle').attr('aria-expanded', 'false');
            $('.abpet_schedule_dropdown').prop('hidden', true);
        }
    });
}(jQuery));

(function ($) {
    'use strict';
    function initMapCanvases(scope) {
        if (typeof window.google === 'undefined' || typeof window.google.maps === 'undefined') return;
        $(scope).find('.abpet_map_canvas:not([data-gmap-ready])').each(function () {
            var canvas = $(this);
            canvas.attr('data-gmap-ready', '1');
            var lat = parseFloat(canvas.attr('data-map-lat'));
            var lng = parseFloat(canvas.attr('data-map-lng'));
            if (isNaN(lat) || isNaN(lng)) return;
            var label = canvas.attr('data-map-label') || '';
            var address = canvas.attr('data-map-address') || '';
            var map = new google.maps.Map(canvas[0], {
                center: { lat: lat, lng: lng },
                zoom: 15,
                mapTypeControl: false,
                streetViewControl: false,
                fullscreenControl: false
            });
            var marker = new google.maps.Marker({
                position: { lat: lat, lng: lng },
                map: map,
                title: label,
                animation: google.maps.Animation.DROP
            });
            if (address || label) {
                var content = document.createElement('div');
                if (label) {
                    var strong = document.createElement('strong');
                    strong.textContent = label;
                    content.appendChild(strong);
                }
                if (address) {
                    var line = document.createElement('div');
                    line.textContent = address;
                    content.appendChild(line);
                }
                var info = new google.maps.InfoWindow({ content: content });
                google.maps.event.addListener(marker, 'click', function () {
                    info.open(map, marker);
                });
            }
        });
    }
    window.abpet_gmap_frontend_init = function () {
        initMapCanvases(document);
    };
    $(function () {
        if (typeof window.google !== 'undefined' && typeof window.google.maps !== 'undefined') {
            initMapCanvases(document);
        }
    });
}(jQuery));