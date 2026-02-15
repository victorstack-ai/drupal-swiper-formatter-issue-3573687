<?php

declare(strict_types=1);

namespace Drupal\Tests\swiper_formatter\Functional\Update;

use Drupal\FunctionalTests\Update\UpdatePathTestBase;
use Drupal\swiper_formatter\Entity\SwiperFormatter;
use Drupal\swiper_formatter\SwiperFormatterInterface;
use PHPUnit\Framework\Attributes\CoversFunction;
use PHPUnit\Framework\Attributes\Group;

/**
 * Tests the keyboard control post update hook.
 *
 * @see swiper_formatter_post_update_add_keyboard_control()
 * @group swiper_formatter
 */
#[Group('swiper_formatter')]
#[CoversFunction('swiper_formatter_post_update_add_keyboard_control')]
class AddKeyboardControlUpdateTest extends UpdatePathTestBase {

  /**
   * {@inheritdoc}
   */
  protected function setDatabaseDumpFiles(): void {
    $this->databaseDumpFiles = [
      // Custom dump with swiper_formatter 2.0 installed (pre-keyboard update).
      __DIR__ . '/../../../fixtures/update/swiper_formatter-2.0-base.php.gz',
    ];
  }

  /**
   * Tests that keyboard control options are added to existing swipers.
   */
  public function testAddKeyboardControl(): void {
    // Load the default swiper entity before the update.
    $swiper = SwiperFormatter::load('default');
    $this->assertInstanceOf(SwiperFormatterInterface::class, $swiper);

    $swiperOptions = $swiper->getSwiperOptions();
    // Assert that keyboard settings don't exist before the update.
    $this->assertArrayNotHasKey('keyboard', $swiperOptions);

    $this->runUpdates();

    // Reload the entity after the update.
    $swiper = SwiperFormatter::load('default');
    $swiperOptions = $swiper->getSwiperOptions();

    // Assert that keyboard settings were added.
    $this->assertArrayHasKey('keyboard', $swiperOptions);
    $this->assertIsArray($swiperOptions['keyboard']);

    // Verify the default values.
    $this->assertFalse($swiperOptions['keyboard']['enabled']);
    $this->assertTrue($swiperOptions['keyboard']['onlyInViewport']);
    $this->assertTrue($swiperOptions['keyboard']['pageUpDown']);
  }

}
