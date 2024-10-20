<?php

declare(strict_types=1);

namespace Drupal\swiper_formatter\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\entity_reference_revisions\Plugin\Field\FieldFormatter\EntityReferenceRevisionsEntityFormatter;
use Drupal\swiper_formatter\Service\SwiperInterface;
use Drupal\swiper_formatter\SwiperFormatterTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Plugin implementation of the 'Swiper Formatter Paragraphs' formatter.
 *
 * @FieldFormatter(
 *   id = "swiper_formatter_paragraphs",
 *   label = @Translation("Swiper Paragraphs"),
 *   field_types = {
 *     "entity_reference_revisions"
 *   },
 *   quickedit = {
 *     "editor" = "form"
 *   }
 * )
 * @phpstan-consistent-constructor
 */
class SwiperParagraphs extends EntityReferenceRevisionsEntityFormatter {

  use SwiperFormatterTrait;

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
    return parent::defaultSettings() + SwiperInterface::DEFAULT_SETTINGS;
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
    }
    return $this->swiperBase->renderSwiper($entity, $data['output'], $data['settings']);
  }

}
