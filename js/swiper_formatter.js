/**
 * @file
 * Init any instances of Swiper on the page.
 */

(function (Drupal, once) {

  'use strict';

  Drupal.swiper_formatter = Drupal.swiper_formatter || {};

   /**
    * Drupal.behaviors implementation for Swiper formatter.
    *
    * Register and initialise all Swiper instances on the page.
    *
    */
   Drupal.behaviors.nkToolsSwiper = {

    attach: function(context, settings) {

      const self = this;

      const swiper_formatter_settings = settings.swiper_formatter || null;

      if (swiper_formatter_settings && typeof swiper_formatter_settings.swipers !== 'undefined') {

        let swipers = {};

        once('swiperFormatterInit', '.swiper-container', context).forEach(function(swiperContainer) {

	        if (swiperContainer.id) {
            const swiperSettings = swiper_formatter_settings.swipers[swiperContainer.id];
            if (typeof swiperSettings === 'object' && typeof Swiper !== 'undefined') {
	
              if (swiperSettings.pagination.type === 'progressbar') {
	              swiperContainer.classList.add('progressbar');
              }

	            swipers[swiperContainer.id] = new Swiper('#' + swiperContainer.id, swiperSettings);

              if (swipers[swiperContainer.id]) {

                // A custom links (anywhere on the page) that trigger swiper slides.
                const triggers = context.querySelectorAll('.swiper-trigger');
                if (triggers) {
                  self.registerTriggers(swipers[swiperContainer.id], Array.from(triggers));
                }
              }
	          }
	        }
	      });    
      }
    },

    /**
     * Run sliding from anywhere, with some markup attributes defined.
     *
     * @param swiper
     *  Current Swiper object.
     * @param triggers
     *  Array with trigger elements/objects.
     * @code
     *  <ul>
     *    <li><a class="swiper-trigger" data-index="2" href="#">Go to slide 2</a></li>
     *    <li><a class="swiper-trigger is-active" data-index="4" href="#">Go to slide 4</a></li>
     *  </ul>
     * @endcode
     */
    registerTriggers: function(swiper, triggers) {
      triggers.forEach(function(trigger) {
        trigger.addEventListener('click', (e) => {

          const target = e.currentTarget || e.target;
          // Take care of siblings' active class.
          if (target.parentNode.siblings().length) {
              //.parent().siblings().length) {
            target.parentNode.siblings().forEach((i, sibling) => {
              sibling.querySelector('.swiper-trigger').classList.remove('active');
            });
          }

          target.classList.add('active');

          const index = target.getAttribute('data-index') ? parseInt(target.getAttribute('data-index')) - 1 : 0;
          swiper.slideTo(index);
          return false;
        });
      });   
    }
  };

})(Drupal, once);