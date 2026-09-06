//slick slider for related product
(function ($) {
    "use strict";
    $(document).ready(function () {
        $('div.abpet_area .related_item_area .abpet_grid').slick({
            dots: false,
            arrows: true,
            prevArrow: '.related_prev',
            nextArrow: '.related_next',
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