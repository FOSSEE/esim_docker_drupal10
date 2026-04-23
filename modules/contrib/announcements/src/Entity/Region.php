<?php

namespace Drupal\announcements\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;

/**
 * Defines the Announcement Region entity.
 *
 * @ConfigEntityType(
 *   id = "announcements_region",
 *   label = @Translation("Announcement Region"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\announcements\RegionListBuilder",
 *     "form" = {
 *       "add" = "Drupal\announcements\Form\RegionForm",
 *       "edit" = "Drupal\announcements\Form\RegionForm",
 *       "delete" = "Drupal\announcements\Form\RegionDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\announcements\RegionHtmlRouteProvider",
 *     },
 *   },
 *   config_prefix = "announcements_region",
 *   admin_permission = "administer site configuration",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   config_export = {
 *     "id",
 *     "uuid",
 *     "label"
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/announcements/announcements_region/{announcements_region}",
 *     "add-form" = "/admin/structure/announcements/announcements_region/add",
 *     "edit-form" = "/admin/structure/announcements/announcements_region/{announcements_region}/edit",
 *     "delete-form" = "/admin/structure/announcements/announcements_region/{announcements_region}/delete",
 *     "collection" = "/admin/structure/announcements/announcements_region"
 *   }
 * )
 */
class Region extends ConfigEntityBase implements RegionInterface {

  /**
   * The Announcement Region ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The Announcement Region label.
   *
   * @var string
   */
  protected $label;

}
