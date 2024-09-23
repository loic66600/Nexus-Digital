(function($) {
    "use strict"

    // Mobile Nav toggle
    $('.menu-toggle > a').on('click', function (e) {
        e.preventDefault();		
        $('#responsive-nav').toggleClass('active');
    });

    // Fix cart dropdown from closing
    $('.cart-dropdown').on('click', function (e) {		
        e.stopPropagation();
    });

    /////////////////////////////////////////

    // Products Slick
    $('.products-slick').each(function() {
        var $this = $(this),
            $nav = $this.attr('data-nav');

        $this.slick({
            slidesToShow: 4,
            slidesToScroll: 1,
            autoplay: true,
            infinite: true,
            speed: 300,
            dots: false,
            arrows: true,
            appendArrows: $nav ? $nav : false,
            responsive: [{
                breakpoint: 991,
                settings: {
                    slidesToShow: 2,
                    slidesToScroll: 1,
                }
            },
            {
                breakpoint: 480,
                settings: {
                    slidesToShow: 1,
                    slidesToScroll: 1,
                }
            },
            ]
        });
    });

    // Products Widget Slick
    $('.products-widget-slick').each(function() {
        var $this = $(this),
            $nav = $this.attr('data-nav');

        $this.slick({
            infinite: true,
            autoplay: true,
            speed: 300,
            dots: false,
            arrows: true,
            appendArrows: $nav ? $nav : false,
        });
    });

    /////////////////////////////////////////

    // Product Main img Slick
    $('#product-main-img').slick({
        infinite: true,
        speed: 300,
        dots: false,
        arrows: true,
        fade: true,
        asNavFor: '#product-imgs',
    });

    // Product imgs Slick
    $('#product-imgs').slick({
        slidesToShow: 3,
        slidesToScroll: 1,
        arrows: true,
        centerMode: true,
        focusOnSelect: true,
        centerPadding: 0,
        vertical: true,
        asNavFor: '#product-main-img',
        responsive: [{
            breakpoint: 991,
            settings: {
                vertical: false,
                arrows: false,
                dots: true,
            }
        },
        ]
    });

    // Product img zoom
    var zoomMainProduct = document.getElementById('product-main-img');
    if (zoomMainProduct) {
        $('#product-main-img .product-preview').zoom();
    }

    /////////////////////////////////////////

    // Input number
    $('.input-number').each(function() {
        var $this = $(this),
            $input = $this.find('input[type="number"]'),
            up = $this.find('.qty-up'),
            down = $this.find('.qty-down');

        down.on('click', function () {
            var value = parseInt($input.val()) - 1;
            value = value < 1 ? 1 : value;
            $input.val(value);
            $input.change();
            updatePriceSlider($this , value);
        });

        up.on('click', function () {
            var value = parseInt($input.val()) + 1;
            $input.val(value);
            $input.change();
            updatePriceSlider($this , value);
        });
    });

    // Get price inputs
    var priceInputMax = document.getElementById('price-max'),
        priceInputMin = document.getElementById('price-min');

    if (priceInputMax) {
        priceInputMax.addEventListener('change', function(){
            updatePriceSlider($(this).parent(), this.value);
        });
    }

    if (priceInputMin) {
        priceInputMin.addEventListener('change', function(){
            updatePriceSlider($(this).parent(), this.value);
        });
    }

    // function updatePriceSlider(elem , value) {
    //     if ( elem.hasClass('price-min') ) {
    //         console.log('min');
    //         priceSlider.noUiSlider.set([value, null]);
    //     } else if ( elem.hasClass('price-max')) {
    //         console.log('max');
    //         priceSlider.noUiSlider.set([null, value]);
    //     }
    // }

    // $(document).ready(function() {
    //     $('.section-tab-nav a').on('click', function(event) {
    //         event.preventDefault(); // Empêche le comportement par défaut du lien
    
    //         var hrefValue = $(this).attr('href');
            
    //         if (hrefValue.startsWith('/')) {
    //             var category = hrefValue.substring(1); // Enlève la barre oblique initiale
    //             console.log('Category selected:', category);
    
    //             // Redirige vers la page avec la catégorie sélectionnée
    //             window.location.href = '/' + encodeURIComponent(category);
    //         } else {
    //             console.error('Invalid href:', hrefValue);
    //         }
    //     });
    // });

    // $(document).ready(function() {
    //     $('.section-tab-nav a').on('click', function(event) {
    //         event.preventDefault(); // Empêche le comportement par défaut du lien
    
    //         var hrefValue = $(this).attr('href');
            
    //         if (hrefValue.startsWith('#')) {
    //             var category = hrefValue.substring(1); // Enlève le dièse initial
    //             console.log('Category selected:', category);
    
    //             // Redirige vers la section avec la catégorie sélectionnée
    //             $('html, body').animate({
    //                 scrollTop: $("#" + category).offset().top
    //             }, 1000);
    //         } else {
    //             console.error('Invalid href:', hrefValue);
    //         }
    //     });
    // });

//    // Price Slider
//    var priceSlider = document.getElementById('price-slider');
   
//    if (priceSlider) {
//        noUiSlider.create(priceSlider, {
//            start: [1, 999],
//            connect: true,
//            step: 1,
//            range: {
//                'min': 1,
//                'max': 999
//            }
//        });

//        priceSlider.noUiSlider.on('update', function(values, handle) {
//            var value = values[handle];
           
//            if(handle) { 
//               priceInputMax.value = value; 
//            } else { 
//               priceInputMin.value = value; 
//            }
//        });
    //    }
    document.addEventListener("DOMContentLoaded", function() {
        setTimeout(function() {
            var alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                alert.classList.add('hide');
                setTimeout(function() {
                    alert.style.display = 'none';
                }, 500); // Correspond à la durée de la transition CSS
            });
        }, 5000); // 5000 ms = 5 seconds
    });

})(jQuery);