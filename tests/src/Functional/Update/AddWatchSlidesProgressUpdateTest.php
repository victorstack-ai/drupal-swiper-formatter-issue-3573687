<?php

declare(strict_types=1);

namespace Drupal\Tests\swiper_formatter\Functional\Update;

use Drupal\FunctionalTests\Update\UpdatePathTestBase;
use Drupal\swiper_formatter\Entity\SwiperFormatter;
use Drupal\swiper_formatter\SwiperFormatterInterface;
use PHPUnit\Framework\Attributes\CoversFunction;
use PHPUnit\Framework\Attributes\Group;

/**
 * Tests the watchSlidesProgress post update hook.
 *
 * @see swiper_formatter_post_update_add_watch_slides_progress_option()
 * @group swiper_formatter
 */
#[Group('swiper_formatter')]
#[CoversFunction('swiper_formatter_post_update_add_watch_slides_progress_option')]
class AddWatchSlidesProgressUpdateTest extends UpdatePathTestBase {

  /**
   * {@inheritdoc}
   */
  protected function setDatabaseDumpFiles(): void {
    $this->databaseDumpFiles = [
      // Custom dump with swiper_formatter 2.0 installed (pre-update).
      __DIR__ . '/../../../fixtures/update/swiper_formatter-2.0-base.php.gz',
    ];
  }

  /**
   * Tests that watchSlidesProgress option is added to existing swipers.
   */
  public function testAddWatchSlidesProgressOption(): void {
    // Load the default swiper entity before the update.
    $swiper = SwiperFormatter::load('default');
    $this->assertInstanceOf(SwiperFormatterInterface::class, $swiper);

    $swiper_options = $swiper->getSwiperOptions();
    // Assert that option doesn't exist before the update.
    $this->assertArrayNotHasKey('watchSlidesProgress', $swiper_options);

    $this->runUpdates();

    // Reload the entity after the update.
    $swiper = SwiperFormatter::load('default');
    $swiper_options = $swiper->getSwiperOptions();

    // Assert that option was added with default value.
    $this->assertArrayHasKey('watchSlidesProgress', $swiper_options);
    $this->assertFalse($swiper_options['watchSlidesProgress']);
  }

}
