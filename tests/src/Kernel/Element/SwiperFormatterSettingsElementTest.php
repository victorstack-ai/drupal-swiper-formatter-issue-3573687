<?php

declare(strict_types=1);

namespace Drupal\Tests\swiper_formatter\Kernel\Element;

use Drupal\Core\Form\FormInterface;
use Drupal\Core\Form\FormState;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Tests\swiper_formatter\Kernel\SwiperFormatterKernelTestBase;
use PHPUnit\Framework\Attributes\Group;

/**
 * Tests the SwiperFormatterSettings Form API element.
 *
 * @group swiper_formatter
 */
#[Group('swiper_formatter')]
class SwiperFormatterSettingsElementTest extends SwiperFormatterKernelTestBase implements FormInterface {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'swiper_formatter_settings_test_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['settings'] = [
      '#type' => 'swiper_formatter_settings',
      '#default_value' => [
        'template' => 'regular_template',
        'type' => 'image',
        'name' => 'field_test',
        'settings' => [],
        'entity_type' => 'node',
        'swiper_access' => TRUE,
      ],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {}

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state): void {}

  /**
   * Tests that breakpoint templates are excluded from the template select.
   */
  public function testBreakpointTemplatesExcludedFromSelect(): void {
    $form_state = new FormState();
    $form_builder = $this->container->get('form_builder');
    $form = $form_builder->buildForm($this, $form_state);

    // Check that the template select field exists.
    $this->assertArrayHasKey('settings', $form);
    $this->assertArrayHasKey('template', $form['settings']);

    // Assert that only the regular template is in the options.
    $this->assertArrayHasKey('regular_template', $form['settings']['template']['#options']);
    $this->assertArrayNotHasKey('breakpoint_template', $form['settings']['template']['#options']);
    $this->assertEquals('Regular Template', $form['settings']['template']['#options']['regular_template']);
  }

}
