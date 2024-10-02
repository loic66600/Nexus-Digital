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
            slidesToShow: 3,
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


    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('search-form');
        const input = document.getElementById('search-input');
    
        if (form && input) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const query = input.value.trim();
    
                if (query !== '') {
                    form.submit();
                }
            });
        }
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

    document.addEventListener('DOMContentLoaded', function() {
        var flashMessages = document.getElementById('flash-messages');
        if (flashMessages && flashMessages.children.length > 0) {
            setTimeout(function() {
                flashMessages.style.transition = 'opacity 1s ease-out';
                flashMessages.style.opacity = '0';
                setTimeout(function() {
                    flashMessages.remove();
                }, 1000);
            }, 5000);
        }
    });

    
    function updateCountdown() {
        const now = new Date().getTime();
        const endTime = now + (10 * 24 * 60 * 60 * 1000); // 10 jours en millisecondes
        
        function calculate() {
            const currentTime = new Date().getTime();
            const difference = endTime - currentTime;
    
            const days = Math.floor(difference / (1000 * 60 * 60 * 24));
            const hours = Math.floor((difference % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((difference % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((difference % (1000 * 60)) / 1000);
    
            document.getElementById("days").innerText = days.toString().padStart(2, '0');
            document.getElementById("hours").innerText = hours.toString().padStart(2, '0');
            document.getElementById("minutes").innerText = minutes.toString().padStart(2, '0');
            document.getElementById("seconds").innerText = seconds.toString().padStart(2, '0');
    
            if (difference < 0) {
                updateCountdown(); // Redémarrer le compte à rebours
            }
        }
    
        calculate();
        setInterval(calculate, 1000);
    }
    
    updateCountdown();

})(jQuery);