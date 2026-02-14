<?php

declare(strict_types=1);

namespace Drupal\Tests\swiper_formatter\Kernel;

use Drupal\swiper_formatter\Entity\SwiperFormatter;
use PHPUnit\Framework\Attributes\Group;

/**
 * Tests that breakpoint templates are filtered from formatter options.
 *
 * @group swiper_formatter
 */
#[Group("swiper_formatter")]
class BreakpointTemplateFilterTest extends SwiperFormatterKernelTestBase {

  /**
   * Tests that breakpoint templates are excluded when filter is enabled.
   */
  public function testBreakpointTemplatesAreFiltered(): void {

    // Test with filtering enabled (should only return regular template).
    $filtered_templates = SwiperFormatter::getSwiperTemplates(TRUE);
    $this->assertArrayHasKey('regular_template', $filtered_templates);
    $this->assertArrayNotHasKey('breakpoint_template', $filtered_templates);
    $this->assertEquals('Regular Template', $filtered_templates['regular_template']);

    // Test with filtering disabled (should return both templates).
    $all_templates = SwiperFormatter::getSwiperTemplates();
    $this->assertArrayHasKey('regular_template', $all_templates);
    $this->assertArrayHasKey('breakpoint_template', $all_templates);
    $this->assertEquals('Regular Template', $all_templates['regular_template']);
    $this->assertEquals('Breakpoint Template', $all_templates['breakpoint_template']);
  }

}
