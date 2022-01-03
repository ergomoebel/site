/**
 * main.js Leitner Theme
 */

define([
    'jquery',
    'jquery/ui'
], function ($) {
    'use strict';

    $(document).ready(function($) {
       $('.btn-configure').click(function(event) {
           if($(this).data('targetUrl')) {
             //console.log($(this).data('targetUrl'));
             window.location.href = $(this).data('targetUrl');
           }
       });
    });

    var setupProductMediaSticky = function() {
      var $productMedia = $('body.catalog-product-view .page-main .product.media');
      if($productMedia.length > 0) {
        /** Original-Wert
		*var headerOffset = 220;*/ 
        var headerOffset = 15;
        var stickyClass = 'sticky-product-media';
        
        var $parentColumn = $productMedia.parents('.column.main');
        var $productInfo = $('body.catalog-product-view .page-main .product-info-main');
        var startSticky = $productMedia.offset().top - headerOffset;
        var stopSticky = $productInfo.offset().top + $productInfo.height() - $productMedia.height() - headerOffset;
        /** Original-Wert
		*if(($(window).height() > ($productMedia.height())) && ($productInfo.height() > ($productMedia.height() * 1.3))) {*/
		if(($(window).height() > ($productMedia.height() * 0.8)) && ($productInfo.height() > ($productMedia.height() * 1.9))) {
          //console.log('product media stick on scroll activated');
          //console.log('start sticky: ' + startSticky);
          //console.log('stop sticky: ' + stopSticky);
          $(window).scroll(function() {
              if (window.pageYOffset >= startSticky) {
                if(!$productMedia.hasClass(stickyClass)) {
                  $productMedia.addClass(stickyClass);
                }
                $productMedia.css('width', $parentColumn.width() - $productInfo.width() - 38)
                if(window.pageYOffset > stopSticky) {
                  $productMedia.css('top', stopSticky  + headerOffset - window.pageYOffset);
                } else {
                  $productMedia.css('position', '');
                  $productMedia.css('top', headerOffset);
                }
              } else {
                if($productMedia.hasClass(stickyClass)) {
                  $productMedia.removeClass(stickyClass);
                  $productMedia.css('position', '');
                  $productMedia.css('top', '');
                  $productMedia.css('width', '');
                }
              }
          });
        } else {
          //console.log('product media stick on scroll not activated');
          //console.log('product media height: ' + $productMedia.height());
          //console.log('product info height: ' + $productInfo.height());
          //console.log('window height: ' + $(window).height());
        }
      }
    };
    
    $(document).ready(function($) {
      window.setTimeout(setupProductMediaSticky, 5000);
    });
    


    return;
});