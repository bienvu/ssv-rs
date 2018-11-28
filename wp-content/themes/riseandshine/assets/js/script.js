/*jslint browser: true*/
/*global $, jQuery, Modernizr, enquire*/
(function (window, document, $) {
  "use strict";

  var $html = $('html'),
    mobileOnly = "screen and (max-width:47.9375em)", // 767px.
    mobileLandscape = "(min-width:30em)", // 480px.
    tablet = "(min-width:48em)"; // 768px.
  // Add  functionality here.

  // Handle submit function on contact form.
  document.addEventListener( 'wpcf7mailsent', function( event ) {
    if ( '5' == event.detail.contactFormId ) {
        window.location.href = window.location.protocol + '//' + window.location.hostname + '/thank-you-subscribe/';
    }else if('204' == event.detail.contactFormId){
        window.location.href = window.location.protocol + '//' + window.location.hostname + '/thank-you-contact/';
    }else if('210' == event.detail.contactFormId) {
        window.location.href = window.location.protocol + '//' + window.location.hostname + '/thank-you-request/';
    }
  }, false );

  $(document).ready(function() {
    // Table responsive
    var $table = $('table');
    if ($table.length && !$table.parent().hasClass('table-responsive')) {
      $table.not($table.find('table')).wrap('<div class="table-responsive"></div>');
    }

    // Remove attr title.
    $('a').removeAttr('title');

    // Add placeholder to quiz.
    $('.wpcf7-quiz').attr('placeholder', 'what is the first letter of rise?');

    // Faq Accordion.
    $('.box-faq__question').each(function() {
      $(this).on('click', function (e) {
        if ($(this).hasClass('is-show')) {
          $('.box-faq__question').removeClass('is-show');
          $('.box-faq__answer').slideUp();
          $(this).next().slideUp();
        } else {
          $('.box-faq__question').removeClass('is-show');
          $(this).addClass('is-show');
          $('.box-faq__answer').slideUp();
          $(this).next().slideDown();
        }
      });
    });

    //toggle class
    $('.menu-bars').click(function(event) {
      $('.menu-bars').toggleClass('active');

      if($('.header__body').hasClass('active')) {
        $('.header__body').removeClass('active');
        $('.header__body').slideUp('200', function() {
          $('.header').removeClass('active');
          $('body').removeClass('no-scroll');
        });
      } else {
        $('.header__body').addClass('active');
        $('.header__body').slideDown('200', function() {
          $('.header').addClass('active');
          $('body').addClass('no-scroll');
        });
      }

      $('.header').toggleClass('active');
    });

    // show menu
    $('.js-show-menu').click(function(event) {
      var that = $(this).parent();
      var sums = $('.js-show-menu').parent();

      if(that.hasClass('active')) {
        that.removeClass('active');
      } else {
        sums.removeClass('active');
        that.addClass('active');
      }
    });

    // detect click outside element
    $(window).click(function(e) {
      if($('.js-detect').has(e.target).length == 0 && !$('.js-detect').is(e.target)) {
        $('.js-detect').removeClass('active');
      } else {
         $('.js-detect').addClass('active');
      }
    });

    // banner slider
    $('.js-slide').slick({
      // fade: true,
      prevArrow: '<span class="slick-prev">prev</span>',
      nextArrow: '<span class="slick-next">next</span>',
      dots: true,
      adaptiveHeight: true,
      autoplay: false,
      autoplaySpeed: 3000,
      slidesToShow: 1,
      slidesToScroll: 1
    });

    // js show
    $('.js-show').click(function(){
      if($(window)[0].innerWidth > 1024) {
        if($(this).hasClass('active')) {
          $(this).removeClass('active');
        } else {
          $('.js-show').removeClass('active');
          $(this).addClass('active');
        }
      }

      if($(this).hasClass('is-focus')) {
        $(this).toggleClass('active');
      }

      if($(this).next('.show').hasClass('active')) {
        $('.rs-tabs__content, .js-show, .box-filter__child').removeClass('active');
        $(this).next('.show');
      } else {
        $('.rs-tabs__content, .js-show, .box-filter__child').removeClass('active');
        $('.js-show').next('.show')
        $(this).next('.show').addClass('active');
        $(this).addClass('active');
      }
    });

    // Click outside
    $(document).on('click', function(e) {
      var $item = $(".box-filter .form-item");

      if (!$item.is(e.target) && $item.has(e.target).length === 0) {
        $('.box-filter__child, .js-show').removeClass('active');
      }
    });

    $('.js-back').click(function(){
      $(this).parents('.show')[0].classList.remove('active');
    });

    $('.is-show').click(function(){
      $('.is-show').next('.show').removeClass('active');
      $(this).next('.show').addClass('active');
    });

    // JS Product
    $('.js-gallery').slick({
      slidesToShow: 1,
      slidesToScroll: 1,
      infinite: true,
      arrows: false,
      asNavFor: '.js-gallery-thumbnail',
      responsive: [{
        breakpoint: 768,
        settings: {
          arrows: true,
          dots: true,
          prevArrow: '<span class="slick-prev"></span>',
          nextArrow: '<span class="slick-next"></span>',
        }
      }]
    });

    // Js Product thumbnail.
    $('.js-gallery-thumbnail').slick({
      asNavFor: '.js-gallery',
      slidesToShow: 5,
      slidesToScroll: 1,
      infinite: true,
      vertical:true,
      focusOnSelect: true,
    });

    // JS also you might like.
    $('.js-product-like').slick({
      prevArrow: '<span class="slick-prev"></span>',
      nextArrow: '<span class="slick-next"></span>',
      slidesToShow: 1,
      slidesToScroll: 1,
      mobileFirst: true,
      rows: 2,
      slidesPerRow: 2,
      arrows: false,
      responsive: [
        {
          breakpoint: 767,
          settings: {
            slidesToScroll: 4,
            slidesToShow: 4,
            rows: 1,
            slidesPerRow: 1,
            arrows: true
          }
        }
      ]
    });

    // Arrow slider
    function bannerHeight() {
      if($( window ).width() < 768) {
        var $heightSlide = $('.banner .mobile-img').find('img').height();
        $('.banner__image').css('height', $heightSlide);
      }
    }

    function autoHeight(object, direct) {
      var $heightSlide = object.find(direct).height();
      object.find('.slick-arrow').css('top', $heightSlide/2);
    }

    // Resize
    $(window).resize(function () {
      bannerHeight();
    }).resize();

    $(window).load(function() {
      autoHeight($('.grid-products--width-slide'), 'img');
      autoHeight($('.js-slide'), '.banner__image');

      $(window).resize(function() {
        autoHeight($('.grid-products--width-slide'), 'img');
        autoHeight($('.js-slide'), '.banner__image');
      });
    });

    // matchHeight
    if(('.grid-products__title').length) {
      $('.grid-products__title').matchHeight();
    }

    // js-play-video
    var $jsPlayVideo = $('.js-play-video'),
        playVideo = function (e) {
      var $iframeVimeo = $(this).find('.vimeo-embed'),
          $iframeYoutube = $(this).find('.youtube-embed');
      $(this).addClass("play-video");
      if ($iframeVimeo.length) {
        var player = Froogaloop($iframeVimeo[0]);
        player.api('play');
      }
      if ($iframeYoutube.length) {
        $iframeYoutube[0].contentWindow.postMessage('{"event":"command","func":"' + 'playVideo' + '","args":""}', '*');
      }
    };
    if ($jsPlayVideo.length) {
      $jsPlayVideo.on('click', playVideo);
    }

    // js index
    ($('.js-index')).click(function(e) {
      var index = $(this).index();
      $('.js-index').removeClass('active');
      $(this).addClass('active');

      $('.is-index').each(function() {
        if($(this).index() == index) {
          $('.is-index').removeClass('active');
          $(this).addClass('active');
        }
      });
    });
    //form fixed
    $('.footer .request-form__title').click(function(){

      $('.footer .form-fixed').toggleClass('is-active');

    });
    $(window).click(function (e)
      {
          var container = $('.footer .form-fixed');
          if (!container.is(e.target)
            && container.has(e.target).length === 0)
          {
          container.removeClass('is-active');
      }
    })


    // Product zoom.
		var $easyzoom = $('.easyzoom').easyZoom();

    //scroll an element
    $('.js-scroll-down').click(function() {
      var $next = $(this).parent().parent().next().offset().top;
      $('html, body').animate({
        scrollTop: $next
      }, 'slow');
    });
  });

}(this, this.document, this.jQuery));
