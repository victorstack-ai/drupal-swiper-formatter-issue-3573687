<?php

declare(strict_types=1);

namespace Drupal\swiper_formatter;

use Drupal\Core\Config\Entity\ConfigEntityInterface;

/**
 * Provides an interface defining a Nk tools swiper entity type.
 */
interface SwiperFormatterInterface extends ConfigEntityInterface {

  /**
   * The machine name of the default swiper entity/template.
   *
   * @var string
   */
  public const DEFAULT_TEMPLATE = 'default';

  /**
   * Returns an array with all properties that are Swiper options.
   *
   * @return array
   *   An array with all available Swiper entities/templates keyed by entity id.
   */
  public static function getSwipers(): array;

  /**
   * Sets swiper_options property to deliver later to Swiper in js, as options.
   *
   * @param array $swiper_options
   *   An array of options to assign to property.
   *
   * @return self
   *   The actual class.
   */
  public function setSwiper(array $swiper_options = []): self;

}
