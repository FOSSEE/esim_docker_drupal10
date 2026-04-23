<?php

namespace Drupal\announcements\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;

/**
 * Defines the Announcement Style entity.
 *
 * @ConfigEntityType(
 *   id = "announcements_style",
 *   label = @Translation("Announcement Style"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\announcements\StyleListBuilder",
 *     "form" = {
 *       "add" = "Drupal\announcements\Form\StyleForm",
 *       "edit" = "Drupal\announcements\Form\StyleForm",
 *       "delete" = "Drupal\announcements\Form\StyleDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\announcements\StyleHtmlRouteProvider",
 *     },
 *   },
 *   config_prefix = "announcements_style",
 *   admin_permission = "administer site configuration",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   config_export = {
 *     "id",
 *     "uuid",
 *     "label",
 *     "extra_classes"
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/announcements/announcements_style/{announcements_style}",
 *     "add-form" = "/admin/structure/announcements/announcements_style/add",
 *     "edit-form" = "/admin/structure/announcements/announcements_style/{announcements_style}/edit",
 *     "delete-form" = "/admin/structure/announcements/announcements_style/{announcements_style}/delete",
 *     "collection" = "/admin/structure/announcements/announcements_style"
 *   }
 * )
 */
class Style extends ConfigEntityBase implements StyleInterface {

  /**
   * The Style ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The Style Label.
   *
   * @var string
   */
  protected $label;

  /**
   * The Extra classes for rendered html.
   *
   * @var string
   */
  protected $extra_classes = '';

  /**
   * {@inheritdoc}
   */
  public function getExtraClasses(): string {
    return $this->extra_classes;
  }

}
