<?php

declare(strict_types=1);

namespace Drupal\Tests\swiper_formatter\Functional\Update;

use Drupal\FunctionalTests\Update\UpdatePathTestBase;
use Drupal\swiper_formatter\Entity\SwiperFormatter;
use Drupal\swiper_formatter\SwiperFormatterInterface;
use PHPUnit\Framework\Attributes\CoversFunction;
use PHPUnit\Framework\Attributes\Group;

/**
 * Tests the zoom options post update hook.
 *
 * @see swiper_formatter_post_update_add_zoom_options()
 * @group swiper_formatter
 */
#[Group('swiper_formatter')]
#[CoversFunction('swiper_formatter_post_update_add_zoom_options')]
class AddZoomOptionsUpdateTest extends UpdatePathTestBase {

  /**
   * {@inheritdoc}
   */
  protected function setDatabaseDumpFiles(): void {
    $this->databaseDumpFiles = [
      // Custom dump with swiper_formatter 2.0 installed (pre-zoom update).
      __DIR__ . '/../../../fixtures/update/swiper_formatter-2.0-base.php.gz',
    ];
  }

  /**
   * Tests that zoom options are added to existing swipers.
   */
  public function testAddZoomOptions(): void {
    // Load the default swiper entity before the update.
    $swiper = SwiperFormatter::load('default');
    $this->assertInstanceOf(SwiperFormatterInterface::class, $swiper);

    $swiperOptions = $swiper->getSwiperOptions();
    // Assert that zoom settings don't exist before the update.
    $this->assertArrayNotHasKey('zoom', $swiperOptions);

    $this->runUpdates();

    // Reload the entity after the update.
    $swiper = SwiperFormatter::load('default');
    $swiperOptions = $swiper->getSwiperOptions();

    // Assert that zoom settings were added.
    $this->assertArrayHasKey('zoom', $swiperOptions);
    $this->assertIsArray($swiperOptions['zoom']);

    // Verify the default values.
    $this->assertFalse($swiperOptions['zoom']['enabled']);
    $this->assertFalse($swiperOptions['zoom']['limitToOriginalSize']);
    $this->assertSame(3, $swiperOptions['zoom']['maxRatio']);
    $this->assertSame(1, $swiperOptions['zoom']['minRatio']);
    $this->assertFalse($swiperOptions['zoom']['panOnMouseMove']);
    $this->assertTrue($swiperOptions['zoom']['toggle']);
  }

}
