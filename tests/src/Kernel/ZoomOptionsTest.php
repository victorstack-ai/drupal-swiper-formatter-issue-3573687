<?php

declare(strict_types=1);

namespace Drupal\Tests\swiper_formatter\Kernel;

use Drupal\swiper_formatter\Entity\SwiperFormatter;
use PHPUnit\Framework\Attributes\Group;

/**
 * Tests zoom options functionality.
 *
 * @group swiper_formatter
 */
#[Group('swiper_formatter')]
class ZoomOptionsTest extends SwiperFormatterKernelTestBase {

  /**
   * Tests that default swiper has correct zoom defaults.
   */
  public function testDefaultZoomOptions(): void {
    // Load the default swiper installed from config.
    $swiper = SwiperFormatter::load('default');
    $this->assertNotNull($swiper);

    $options = $swiper->getSwiperOptions();
    $this->assertArrayHasKey('zoom', $options);
    $this->assertIsArray($options['zoom']);

    // Verify all default zoom values match config/install.
    $this->assertFalse($options['zoom']['enabled']);
    $this->assertFalse($options['zoom']['limitToOriginalSize']);
    $this->assertSame(3, $options['zoom']['maxRatio']);
    $this->assertSame(1, $options['zoom']['minRatio']);
    $this->assertFalse($options['zoom']['panOnMouseMove']);
    $this->assertTrue($options['zoom']['toggle']);
  }

  /**
   * Tests that zoom options can be set and persisted.
   */
  public function testZoomOptionsCanBeSetAndPersisted(): void {
    $swiper = SwiperFormatter::create([
      'id' => 'test_zoom',
      'label' => 'Test Zoom Template',
      'description' => 'Testing zoom options',
    ]);

    // Set custom zoom options.
    $options = $swiper->getSwiperOptions();
    $options['zoom'] = [
      'enabled' => TRUE,
      'limitToOriginalSize' => TRUE,
      'maxRatio' => 5,
      'minRatio' => 2,
      'panOnMouseMove' => TRUE,
      'toggle' => FALSE,
    ];
    $swiper->setSwiperOptions($options);
    $swiper->save();

    // Reload and verify persistence.
    $swiper = SwiperFormatter::load('test_zoom');
    $options = $swiper->getSwiperOptions();

    $this->assertTrue($options['zoom']['enabled']);
    $this->assertTrue($options['zoom']['limitToOriginalSize']);
    $this->assertSame(5, $options['zoom']['maxRatio']);
    $this->assertSame(2, $options['zoom']['minRatio']);
    $this->assertTrue($options['zoom']['panOnMouseMove']);
    $this->assertFalse($options['zoom']['toggle']);
  }

}
