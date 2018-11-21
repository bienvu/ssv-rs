/*jslint browser: true*/
/*global $, jQuery, Modernizr, enquire*/
(function (window, document, $) {
  "use strict";

  var $html = $('html'),
    mobileOnly = "screen and (max-width:47.9375em)", // 767px.
    mobileLandscape = "(min-width:30em)", // 480px.
    tablet = "(min-width:48em)"; // 768px.
  // Add  functionality here.

  $(document).ready(function() {
    // Table responsive
    var $table = $('table');
    if ($table.length && !$table.parent().hasClass('table-responsive')) {
      $table.not($table.find('table')).wrap('<div class="table-responsive"></div>');
    }

    // Remove attr title.
    $('a').removeAttr('title');

    // console.log($('.box-icon__title').matchHeight());

    // Add placeholder to quiz.
    $('.wpcf7-quiz').attr('placeholder', 'text here');

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
    });;
    // show menu
    $('.js-show-menu').click(function(event) {
      var that = $(this).parent().parent();
      var sums = $('.js-show-menu').parent().parent();

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
    // slick
    $('.js-slide').slick({
      fade: true,
      prevArrow: '<span class="slick-prev">prev</span>',
      nextArrow: '<span class="slick-next">next</span>',
      dots: true,
      adaptiveHeight: true
    });

    // js show
    $('.js-show').click(function(){
      $(this).toggleClass('active');
      $(this).next('.show').toggleClass('active');
    });

    $('.js-back').click(function(){
      $(this).parents('.show')[0].classList.remove('active');
    });
  });

}(this, this.document, this.jQuery));
