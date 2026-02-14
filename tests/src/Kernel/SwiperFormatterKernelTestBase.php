<?php

declare(strict_types=1);

namespace Drupal\Tests\swiper_formatter\Kernel;

use Drupal\KernelTests\KernelTestBase;
use Drupal\swiper_formatter\Entity\SwiperFormatter;

/**
 * Base class for swiper_formatter kernel tests.
 */
abstract class SwiperFormatterKernelTestBase extends KernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = ['swiper_formatter'];

  /**
   * Regular (non-breakpoint) template for testing.
   *
   * @var \Drupal\swiper_formatter\Entity\SwiperFormatter
   */
  protected SwiperFormatter $regularTemplate;

  /**
   * Breakpoint template for testing.
   *
   * @var \Drupal\swiper_formatter\Entity\SwiperFormatter
   */
  protected SwiperFormatter $breakpointTemplate;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->installConfig(['swiper_formatter']);
    $this->installEntitySchema('swiper_formatter');

    $this->createTestTemplates();
  }

  /**
   * Creates test templates for use in tests.
   */
  protected function createTestTemplates(): void {
    // Create a regular template (not a breakpoint).
    $this->regularTemplate = SwiperFormatter::create([
      'id' => 'regular_template',
      'label' => 'Regular Template',
      // @todo Remove description when https://www.drupal.org/project/swiper_formatter/issues/3559911 is fixed.
      'description' => 'A regular swiper template',
      'breakpoint' => FALSE,
    ]);
    $this->regularTemplate->save();

    // Create a breakpoint template.
    $this->breakpointTemplate = SwiperFormatter::create([
      'id' => 'breakpoint_template',
      'label' => 'Breakpoint Template',
      // @todo Remove description when https://www.drupal.org/project/swiper_formatter/issues/3559911 is fixed.
      'description' => 'A breakpoint template',
      'breakpoint' => TRUE,
    ]);
    $this->breakpointTemplate->save();
  }

}
