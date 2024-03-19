<?php

declare(strict_types=1);

namespace Drupal\swiper_formatter\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;

use Drupal\swiper_formatter\SwiperFormatterInterface;

/**
 * Defines the Nk tools swiper entity type.
 *
 * @ConfigEntityType(
 *   id = "swiper_formatter",
 *   label = @Translation("Swiper"),
 *   label_collection = @Translation("Swipers"),
 *   label_singular = @Translation("swiper"),
 *   label_plural = @Translation("swipers"),
 *   label_count = @PluralTranslation(
 *     singular = "@count swiper",
 *     plural = "@count swipers",
 *   ),
 *   handlers = {
 *     "list_builder" = "Drupal\swiper_formatter\SwiperFormatterListBuilder",
 *     "form" = {
 *       "add" = "Drupal\swiper_formatter\Form\SwiperFormatterForm",
 *       "edit" = "Drupal\swiper_formatter\Form\SwiperFormatterForm",
 *       "delete" = "Drupal\Core\Entity\EntityDeleteForm"
 *     }
 *   },
 *   config_prefix = "swiper_formatter",
 *   admin_permission = "administer swiper_formatter",
 *   links = {
 *     "collection" = "/admin/structure/swiper-formatter",
 *     "add-form" = "/admin/structure/swiper-formatter/add",
 *     "edit-form" = "/admin/structure/swiper-formatter/{swiper_formatter}",
 *     "delete-form" = "/admin/structure/swiper-formatter/{swiper_formatter}/delete"
 *   },
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   config_export = {
 *     "id",
 *     "label",
 *     "description",
 *     "status",
 *     "swiper_options"
 *   }
 * )
 */
class SwiperFormatter extends ConfigEntityBase implements SwiperFormatterInterface {

  /**
   * The swiper ID.
   *
   * @var string
   */
  protected string $id;

  /**
   * Swiper entity label.
   *
   * @var string
   */
  protected string $label;

  /**
   * Swiper entity description.
   *
   * @var string
   */
  protected string $description;

  /**
   * Swiper entity status.
   *
   * @var bool
   */
  protected $status;

  /**
   * A collection of all of the Swiper's properties into a single array.
   *
   * @var array
   */
  public array $swiper_options = [];

  /**
   * Swipers getter.
   *
   * @return array
   *   An array with swiper options, keyed by entity id.
   */
  public static function getSwipers(): array {
    $swiper_options = [];
    $swipers = static::loadMultiple();
    if (!empty($swipers)) {
      foreach ($swipers as $swiper_entity) {
        $swiper_entity_storage = $swiper_entity->load($swiper_entity->id());
        $swiper_options[$swiper_entity->id()] = [
          'id' => $swiper_entity->id(),
          'label' => $swiper_entity_storage->label(),
          'properties' => $swiper_entity_storage->toArray(),
        ];
      }
    }
    return $swiper_options;
  }

  /**
   * Swipers setter.
   *
   * @param array $swiper_options
   *   An array of options to assign as property.
   *
   * @return SwiperFormatter
   *   A refreshed instance of this class.
   */
  public function setSwiper(array $swiper_options = []): SwiperFormatter {
    $this->swiper_options = $swiper_options;
    return $this;
  }

  /**
   * Prepare #options for swiper template options form field.
   *
   * @return array
   *   An array with all available templates, keyed by id.
   */
  public static function getSwiperTemplates(): array {
    $templates = [];
    $swipers = static::getSwipers();
    if (!empty($swipers)) {
      foreach ($swipers as $id => $swiper) {
        $templates[$id] = $swiper['label'];
      }
    }
    return $templates;
  }

}
