<?php

/**
 * @file
 * Post update functions for Swiper Formatter.
 */

declare(strict_types=1);

/**
 * Add keyboard control module to swiper formatters.
 */
function swiper_formatter_post_update_add_keyboard_control(): void {
  /** @var \Drupal\swiper_formatter\SwiperFormatterInterface[] $swipers */
  $swipers = \Drupal::entityTypeManager()->getStorage('swiper_formatter')->loadMultiple();
  foreach ($swipers as $swiper) {
    $swiper_options = $swiper->getSwiperOptions();
    // Only add the default options if the parent key doesn't already exist.
    if (isset($swiper_options['keyboard'])) {
      continue;
    }
    $swiper_options['keyboard'] = [
      'enabled' => FALSE,
      'onlyInViewport' => TRUE,
      'pageUpDown' => TRUE,
    ];
    $swiper->setSwiperOptions($swiper_options);
    $swiper->save();
  }
}

/**
 * Add zoom options to swiper formatters.
 */
function swiper_formatter_post_update_add_zoom_options(): void {
  /** @var \Drupal\swiper_formatter\SwiperFormatterInterface[] $swipers */
  $swipers = \Drupal::entityTypeManager()->getStorage('swiper_formatter')->loadMultiple();
  foreach ($swipers as $swiper) {
    $swiper_options = $swiper->getSwiperOptions();
    // Only add the default options if the parent key doesn't already exist.
    if (isset($swiper_options['zoom'])) {
      continue;
    }
    $swiper_options['zoom'] = [
      'enabled' => FALSE,
      'limitToOriginalSize' => FALSE,
      'maxRatio' => 3,
      'minRatio' => 1,
      'panOnMouseMove' => FALSE,
      'toggle' => TRUE,
    ];
    $swiper->setSwiperOptions($swiper_options);
    $swiper->save();
  }
}
