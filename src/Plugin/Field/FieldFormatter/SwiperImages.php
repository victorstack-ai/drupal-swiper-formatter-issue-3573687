<?php

declare(strict_types=1);

namespace Drupal\swiper_formatter\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Url;
use Drupal\image\Plugin\Field\FieldFormatter\ImageFormatter;
use Drupal\swiper_formatter\Service\SwiperInterface;
use Drupal\swiper_formatter\SwiperFormatterTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Plugin implementation of the 'Swiper images' formatter.
 *
 * @FieldFormatter(
 *   id = "swiper_formatter_image",
 *   label = @Translation("Swiper images"),
 *   field_types = {
 *     "image"
 *   }
 * )
 */
class SwiperImages extends ImageFormatter {

  use SwiperFormatterTrait;

  /**
   * Swiper formatter base service.
   *
   * @var \Drupal\swiper_formatter\Service\SwiperInterface
   */
  protected SwiperInterface $swiperBase;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): self {
    $instance = parent::create($container, $configuration, $plugin_id, $plugin_definition);
    $instance->swiperBase = $container->get('swiper_formatter.base');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings(): array {
    return parent::defaultSettings() + SwiperInterface::DEFAULT_SETTINGS + [
      'custom_link' => NULL,
    ];
  }

  /**
   * {@inheritdoc}
   *
   * @phpstan-ignore-next-line
   */
  public function viewElements(FieldItemListInterface $items, $langcode): array {

    $output = parent::viewElements($items, $langcode);
    $entity = $items->getEntity();
    $data = $this->swiperBase->processElements($this->fieldDefinition, $entity, $this->getSettings(), $output);
    foreach ($data['output'] as &$item) {
      // Caption handling.
      $caption = $data['settings']['caption'] ?? NULL;
      $this->swiperBase->getCaption($item, $caption, $entity);

      // Lazy load.
      $lazy = $data['settings']['lazy']['enabled'] ?? NULL;
      if ($lazy) {
        $image_style_setting = $data['settings']['image_style'] ?? NULL;
        if ($image_style_setting) {
          if ($image_style = $this->swiperBase->getImageStyle($image_style_setting)) {
            $item['#background'] = $image_style->buildUrl($item['#item']->entity->getFileUri());
          }
        }
        else {
          $item['#background'] = $item['#item']->entity->createFileUrl();
        }
        // @todo Figure what are these.
        $data['settings']['preloadImages'] = FALSE;
        $data['settings']['watchSlidesProgress'] = TRUE;
      }

      // Image link implementation.
      if (isset($data['settings']['image_link'])) {
        if (isset($item['#url'])) {
          $item['#slide_url'] = $item['#url'] instanceof Url ? $item['#url']->toString() : $item['#url'];
        }
        else {
          if ($data['settings']['image_link'] == 'custom') {
            $item['#slide_url'] = $this->swiperBase->tokenValue($data['settings']['custom_link'], $entity);
          }
        }
      }
    }
    return $this->swiperBase->renderSwiper($entity, $data['output'], $data['settings']);
  }

}
