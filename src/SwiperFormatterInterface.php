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
  public const string DEFAULT_TEMPLATE = 'default';

  /**
   * Default Swiper's modules.
   *
   * @var array
   */
  public const array SWIPER_MODULES = [
    'navigation',
    'pagination',
    'scrollbar',
    'autoplay',
    'lazy',
  ];

  /**
   * Properties that can be assigned to a breakpoint template.
   *
   * @var array
   */
  public const array BREAKPOINT_OPTIONS = [
    'slidesPerView',
    'spaceBetween',
    'navigation',
    'pagination',
  ];

  /**
   * Returns an array with all the properties that are Swiper.js options.
   *
   * @param bool $check_breakpoint
   *   When true, check on breakpoint templates.
   *
   * @return array
   *   An array with swiper options, keyed by entity id.
   */
  public static function getSwipers(bool $check_breakpoint = FALSE): array;

  /**
   * Sets swiper_options property to deliver later to Swiper in js, as options.
   *
   * @param array $swiper_options
   *   Swiper template options to set to entity.
   *
   * @return self
   *   Object instance implementing this interface.
   */
  public function setSwiper(array $swiper_options = []): self;

}
