<?php

namespace Drupal\announcements\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBundleBase;

/**
 * Defines the Announcement type entity.
 *
 * @ConfigEntityType(
 *   id = "announcements_type",
 *   label = @Translation("Announcement type"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\announcements\AnnouncementTypeListBuilder",
 *     "form" = {
 *       "add" = "Drupal\announcements\Form\AnnouncementTypeForm",
 *       "edit" = "Drupal\announcements\Form\AnnouncementTypeForm",
 *       "delete" = "Drupal\announcements\Form\AnnouncementTypeDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\announcements\AnnouncementTypeHtmlRouteProvider",
 *     },
 *   },
 *   config_prefix = "announcements_type",
 *   admin_permission = "administer site configuration",
 *   bundle_of = "announcements_announcement",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   config_export = {
 *     "id",
 *     "uuid",
 *     "dismissible",
 *     "enabled_conditions",
 *     "label"
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/announcements/announcements_type/{announcements_type}",
 *     "add-form" = "/admin/structure/announcements/announcements_type/add",
 *     "edit-form" = "/admin/structure/announcements/announcements_type/{announcements_type}/edit",
 *     "delete-form" = "/admin/structure/announcements/announcements_type/{announcements_type}/delete",
 *     "collection" = "/admin/structure/announcements/announcements_type"
 *   }
 * )
 */
class AnnouncementType extends ConfigEntityBundleBase implements AnnouncementTypeInterface {

  /**
   * The Announcement type ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The Announcement type label.
   *
   * @var string
   */
  protected $label;

  /**
   * The Announcement dismissible status.
   *
   * @var bool
   */
  protected $dismissible = FALSE;

  /**
   * Enabled conditions plugin ids.
   *
   * @var bool
   */
  protected $enabled_conditions = [];

  /**
   * {@inheritdoc}
   */
  public function isDismissible(): ?bool {
    return $this->dismissible;
  }

  /**
   * {@inheritdoc}
   */
  public function setDismissible(bool $dismissible) {
    $this->dismissible = $dismissible;
  }

  /**
   * {@inheritdoc}
   */
  public function getEnabledConditions(): array {
    return $this->enabled_conditions;
  }

  /**
   * {@inheritdoc}
   */
  public function setEnabledConditions(array $enabled_conditions) {
    $this->enabled_conditions = $enabled_conditions;
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheTagsToInvalidate() {
    $tags = parent::getCacheTagsToInvalidate();
    $tags[] = $this->entityTypeId . ':' . $this->id();
    return $tags;
  }

}
