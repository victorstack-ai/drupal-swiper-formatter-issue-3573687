<?php

declare(strict_types=1);

namespace Drupal\swiper_formatter;

use Drupal\Core\Config\Entity\ConfigEntityInterface;

/**
 * Provides an interface defining a Nk tools swiper entity type.
 */
interface SwiperFormatterInterface extends ConfigEntityInterface {

  /**
   * Returns an array with all of the properties that are Swiper.js options.
   */
  public static function getSwipers(): array;

  /**
   * Sets all the properties that are Swiper.js options into an array.
   */
  public function setSwiper(array $swiper_options = []): SwiperFormatterInterface;

}
