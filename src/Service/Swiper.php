<?php

declare(strict_types=1);

namespace Drupal\swiper_formatter\Service;

use Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException;
use Drupal\Component\Plugin\Exception\PluginNotFoundException;
use Drupal\Component\Utility\Html;
use Drupal\Core\Entity\Display\EntityViewDisplayInterface;
use Drupal\Core\Entity\EntityDisplayRepositoryInterface;
use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\FieldableEntityInterface;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\Routing\RedirectDestinationInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\swiper_formatter\Entity\SwiperFormatter;
use Drupal\token\Token;

/**
 * Swiper base service.
 */
class Swiper implements SwiperInterface {

  use StringTranslationTrait;

  /**
   * Swiper formatter entity storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  public EntityStorageInterface $swiperFormatter;

  /**
   * Image style storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  public EntityStorageInterface $imageStyleStorage;

  /**
   * Constructs this base class.
   */
  public function __construct(
    public EntityFieldManagerInterface $entityFieldManager,
    public EntityTypeManagerInterface $entityTypeManager,
    protected EntityDisplayRepositoryInterface $entityDisplayRepository,
    protected LoggerChannelFactoryInterface $loggerFactory,
    protected AccountInterface $currentUser,
    protected Token $token,
    protected RedirectDestinationInterface $destination,
    protected MessengerInterface $messenger,
  ) {
    try {
      $this->swiperFormatter = $this->entityTypeManager->getStorage('swiper_formatter');
      $this->imageStyleStorage = $this->entityTypeManager->getStorage('image_style');
    }
    catch (InvalidPluginDefinitionException | PluginNotFoundException $e) {
      $this->loggerFactory->get('Swiper formatter')->error($e->getMessage());
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getDisplay(FieldableEntityInterface $entity, string $view_mode = 'default'): EntityViewDisplayInterface {
    return $this->entityDisplayRepository->getViewDisplay($entity->getEntityTypeId(), $entity->bundle(), $view_mode);
  }

  /**
   * {@inheritdoc}
   */
  public function getFieldDefinitions(FieldDefinitionInterface $field_definition): array {
    return $this->entityFieldManager->getFieldDefinitions($field_definition->getTargetEntityTypeId(), $field_definition->getTargetBundle()) ?: [];
  }

  /**
   * {@inheritdoc}
   */
  public function getViewModeOptions(string $target_type, string $target_bundle): array {
    return $this->entityDisplayRepository->getViewModeOptionsByBundle($target_type, $target_bundle);
  }

  /**
   * {@inheritdoc}
   */
  public function getImageStyle(string $image_style): EntityInterface|NULL {
    return $this->imageStyleStorage->load($image_style) ?? NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function getSwiper(string $swiper_id): EntityInterface|NULL {
    return $this->swiperFormatter->load($swiper_id) ?? NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function validateTemplates(): void {
    $options = SwiperFormatter::getSwiperTemplates();
    if (empty($options)) {
      if ($this->currentUser->hasPermission('administer swiper')) {
        $warning = $this->t('There is no Swiper templates created yet, create one first <a target="_blank" href=":url">here</a>.', [
          ':url' => '/admin/config/content/swiper_formatter/add',
        ]);
      }
      else {
        $warning = $this->t('There is no Swiper templates created yet, requires "administer swiper" permission.');
      }
      $this->messenger->addWarning($warning);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function processSettings(FieldDefinitionInterface $field_definition, array $settings): array {
    $element = [];
    $entity_type = $field_definition->getTargetEntityTypeId();
    $fields = $this->entityFieldManager->getFieldDefinitions($entity_type, $field_definition->getTargetBundle());
    $entity_fields = [];
    foreach ($fields as $field_name => $field) {
      $entity_fields[$field_name] = [
        'title' => $field->getLabel(),
        'storage' => $field->getFieldStorageDefinition(),
      ];
    }

    if (!empty($settings['template'])) {
      if ($swiper_entity = $this->swiperFormatter->load($settings['template'])) {
        /** @var \Drupal\swiper_formatter\SwiperFormatterInterface $swiper_entity */
        $settings += $swiper_entity->get('swiper_options');
      }
    }

    $element += [
      '#type' => 'swiper_formatter_settings',
      '#title' => $this->t('Swiper formatter settings'),
      '#default_value' => [
        'type' => $field_definition->getFieldStorageDefinition()->getType(),
        'name' => $field_definition->getFieldStorageDefinition()->getName(),
        'template' => $settings['template'],
        'settings' => $settings,
        'custom_link' => $settings['custom_link'] ?? NULL,
        'caption' => [
          'value' => $settings['caption'] ?? NULL,
          'entity_fields' => $entity_fields,
          'field_edit_url' => $settings['caption_field_edit_url'] ?? NULL,
        ],
        'entity_type' => $entity_type,
        'swiper_access' => $this->currentUser->hasPermission('administer swiper'),
        // Images specific.
        'title_field' => $field_definition->getSetting('title_field') ?? NULL,
        'alt_field' => $field_definition->getSetting('alt_field') ?? NULL,
      ],
    ];

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function processElements(FieldDefinitionInterface $field_definition, FieldableEntityInterface $entity, array $settings, array $output): array {
    $elements = [
      'settings' => $settings,
      'output' => $output,
    ];

    // Bail out if no elements to render.
    if (empty($output)) {
      return $elements;
    }

    $template = $settings['template'] ?? NULL;
    if (!$template) {
      $message = $this->t("<em>Swiper formatter is set for <strong>@field</strong></em> field but no Swiper template is set on field's display settings. Falling back to default view.", [
        '@field' => $field_definition->getLabel(),
      ]);
      $this->messenger->addWarning($message);
      return $elements;
    }

    $settings['field_type'] = $field_definition->getFieldStorageDefinition()->getType();
    $settings['field_name'] = $field_definition->getFieldStorageDefinition()->getName();

    if ($swiper_entity = $this->swiperFormatter->load($template)) {
      /** @var \Drupal\swiper_formatter\SwiperFormatterInterface $swiper_entity */
      $settings += $swiper_entity->get('swiper_options');
      $id = $this->elementId($entity);
      $settings['id'] = $id;
      $elements['settings'] = $settings;
    }
    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public function elementId(FieldableEntityInterface $entity, ?string $view_mode = NULL, ?string $delta = NULL): string {
    $id = 'default';
    // Has no view mode nor delta param.
    if (!$view_mode && is_null($delta)) {
      $id = $entity->getEntityTypeId() . '-' . $entity->bundle() . '-' . $entity->id();
    }
    elseif ($view_mode && $delta == NULL) {
      $id = $entity->getEntityTypeId() . '-' . $entity->bundle() . '-' . $entity->id() . '-' . $view_mode;
    }
    elseif (!$view_mode && $delta != NULL) {
      $id = $entity->getEntityTypeId() . '-' . $entity->bundle() . '-' . $entity->id() . '-' . $delta;
    }
    // Has both params.
    elseif ($view_mode && !is_null($delta)) {
      $id = $entity->getEntityTypeId() . '-' . $entity->bundle() . '-' . $entity->id() . '-' . $delta . '-' . $view_mode;
    }
    return Html::getUniqueId($id);
  }

  /**
   * {@inheritdoc}
   */
  public function renderSwiper(FieldableEntityInterface $entity, array $output, array $settings): array {
    $id = $settings['id'] ?? $this->elementId($entity);
    return [
      '#theme' => 'swiper_formatter',
      '#id' => $id,
      '#object' => $entity,
      '#content' => $output,
      '#settings' => $settings,
      '#attributes' => [
        'id' => $id,
        'class' => [
          'swiper-container',
        ],
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getCaption(array &$item, ?string $caption_field = NULL, ?FieldableEntityInterface $entity = NULL, int $delta = 0): void {
    $item['#caption'] = match ($caption_field) {
      'title' => isset($item['#item']) && $item['#item']->title ? $item['#item']->title : NULL,
      'alt' => isset($item['#item']) && $item['#item']->alt ? $item['#item']->alt : NULL,
      default => $entity && $entity->hasField($caption_field) ? ($entity->get($caption_field)->get($delta)->getValue() ?? NULL) : NULL,
    };
  }

  /**
   * {@inheritdoc}
   */
  public function tokenValue(string $markup, FieldableEntityInterface $entity): string {
    $token_data[$entity->getEntityTypeId()] = $entity;
    return $this->token->replace($markup, $token_data, ['clear' => TRUE]);
  }

  /**
   * {@inheritdoc}
   */
  public function getDestination(FieldDefinitionInterface $field_definition): array {

    $destination = substr($this->destination->get(), 0, (int) strpos($this->destination->get(), '?'));
    /* $entity_type = $field_definition->getTargetEntityTypeId(); */
    /* $route_name = 'entity.field_config.' . $entity_type . '_field_edit_form'; */
    /* $target_bundle = $field_definition->getTargetBundle() ?? NULL; */
    /* $field_name = $field_definition->getFieldStorageDefinition()->getName(); */
    /* $route_params = ['field_config' => $entity_type . '.' . $target_bundle . '.' . $field_name,];*/
    // A "famous bug" with paragraph vs. paragraphs entity type.
    /* $type = $entity_type == 'paragraph' ? 'paragraphs' : $entity_type; */
    /* $route_params[$type . '_type'] = $target_bundle; */
    /* $uri_options = ['fragment' => 'edit-settings-title-field','query' => ['destination' => $destination],]; */
    // Currently Views and Taxonomy fails, not having a parameter.
    /* $data = []; */
    /* if ($entity_type && $target_bundle) {$data['caption_field_edit_url'] = Url::fromRoute($route_name, $route_params, $uri_options)->toString();} */
    return [
      'destination' => $destination,
    ];
  }

  /**
   * Check if entity is "routable", e.g., node vs. paragraph.
   *
   * @param \Drupal\Core\Entity\FieldableEntityInterface $entity
   *   Content entity object.
   *
   * @return bool|string
   *   The path for entity link, or FALSE if it doesn't exist.
   *
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  protected function isRouted(FieldableEntityInterface $entity): bool|string {
    $entity_type_definition = $this->entityTypeManager->getDefinition($entity->getEntityTypeId());
    return $entity_type_definition->getLinkTemplate('canonical');
  }

}
