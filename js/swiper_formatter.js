/**
 * @file
 * Init any instances of Swiper on the page.
 */

(function ($, Drupal, drupalSettings, once) {

  'use strict';

  Drupal.swiper_formatter = Drupal.swiper_formatter || {};

  Drupal.behaviors.nkToolsSwiper = {

    attach: function(context, settings) {
  
      var self = this;
  
      var swiper_formatter_settings = settings.swiper_formatter || null;

      if (swiper_formatter_settings && $.type(swiper_formatter_settings.swipers) !== 'undefined') { 

        var swipers = {};

        once('swiperFormaterInit', '.swiper-container', context).forEach(function(swiperContainer) {
	     
	      if (swiperContainer.id) {
            var swiperSettings = swiper_formatter_settings.swipers[swiperContainer.id];
            if (typeof swiperSettings === 'object' && typeof Swiper !== 'undefined') {
	          
              if (swiperSettings.pagination.type === 'progressbar') {
	            $(swiperContainer).addClass('progressbar');  
              }
              
	          swipers[swiperContainer.id] = new Swiper('#' + swiperContainer.id, swiperSettings);
                
              if (swipers[swiperContainer.id]) {
                
                // Swiper's slideChangeTransitionEnd event.
                swipers[swiperContainer.id].on('slideChangeTransitionEnd', function(e) {
                  self.showHidden(this);
                });
                
                // A custom links (anywhere on the page) that trigger swiper slides.
                self.registerTriggers(swipers[swiperContainer.id], $(context).find('.swiper-trigger'), context, settings); 
              }
	        }
	      }
	    });    
      }
    },

    showHidden: function(swiper) {
      if (swiper && swiper.slides.length > 0) {
        $.each(swiper.slides, function(index, slide) {
          if (index == swiper.activeIndex) {
            $(slide).find('.hidden').each(function(d, hidden) {
              $(hidden).removeClass('hidden');
            });
          }
        });
      }
    },

    registerTriggers: function(swiper, triggers, context, settings) {
      triggers.once('swiperSwipe').each(function() {  
        $(this).on('click', function(e) {
       
          $(context).find('.swiper-trigger').each(function(i, sibling) {
            $(sibling).removeClass('active');
          });
       
          setTimeout(function() {
            $(e.currentTarget).addClass('active');
          }, 1);

          var index = $(this).attr('data-index') ? parseInt($(this).attr('data-index')) - 1 : 0;
          swiper.slideTo(index);
          return false;
        });
      });   
    }
  };

})(jQuery, Drupal, drupalSettings, once);