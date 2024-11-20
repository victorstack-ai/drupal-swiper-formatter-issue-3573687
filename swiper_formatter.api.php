<?php

/**
 * @file
 * Hooks related to Swiper Formatter module.
 */

/**
 * @addtogroup hooks
 * @{
 */

/**
 * Alter a swiper formatter settings.
 *
 * @param string $id
 *   Swiper formatter id.
 * @param array $settings
 *   The swiper formatter settings.
 *
 * @see Drupal\swiper_formatter\Service\Swiper::renderSwiper()
 */
function hook_swiper_formatter_settings_alter($id, array &$settings): void {
  // Alter swiper formatter settings array.
  $settings['slidesPerView'] = 'auto';
}

/**
 * @} End of "addtogroup hooks".
 */
