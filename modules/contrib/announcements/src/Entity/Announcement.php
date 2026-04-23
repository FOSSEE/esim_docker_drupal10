<?php

namespace Drupal\announcements\Entity;

use Drupal\Core\Cache\Cache;
use Drupal\Core\Condition\ConditionManager;
use Drupal\Core\Entity\EditorialContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityPublishedTrait;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\RevisionableInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\user\UserInterface;

/**
 * Defines the Announcement.
 *
 * @ingroup announcements
 *
 * @ContentEntityType(
 *   id = "announcements_announcement",
 *   label = @Translation("Announcement"),
 *   label_collection = @Translation("Announcements"),
 *   bundle_label = @Translation("Announcement type"),
 *   handlers = {
 *     "storage" = "Drupal\announcements\AnnouncementStorage",
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\announcements\AnnouncementListBuilder",
 *     "views_data" = "Drupal\announcements\Entity\AnnouncementViewsData",
 *     "translation" = "Drupal\announcements\AnnouncementTranslationHandler",
 *
 *     "form" = {
 *       "default" = "Drupal\announcements\Form\AnnouncementForm",
 *       "add" = "Drupal\announcements\Form\AnnouncementForm",
 *       "edit" = "Drupal\announcements\Form\AnnouncementForm",
 *       "delete" = "Drupal\announcements\Form\AnnouncementDeleteForm",
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\announcements\AnnouncementHtmlRouteProvider",
 *     },
 *     "access" = "Drupal\announcements\AnnouncementAccessControlHandler",
 *   },
 *   base_table = "announcements",
 *   data_table = "announcements_field_data",
 *   revision_table = "announcements_revision",
 *   revision_data_table = "announcements_field_revision",
 *   translatable = TRUE,
 *   permission_granularity = "bundle",
 *   admin_permission = "administer announcement entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "revision" = "vid",
 *     "bundle" = "type",
 *     "label" = "title",
 *     "uuid" = "uuid",
 *     "uid" = "user_id",
 *     "langcode" = "langcode",
 *     "published" = "status",
 *   },
 *   revision_metadata_keys = {
 *     "revision_user" = "revision_user",
 *     "revision_created" = "revision_created",
 *     "revision_log_message" = "revision_log_message",
 *   },
 *   links = {
 *     "canonical" = "/announcements/{announcements_announcement}",
 *     "add-page" = "/announcements/add",
 *     "add-form" = "/announcements/add/{announcements_type}",
 *     "edit-form" = "/announcements/{announcements_announcement}/edit",
 *     "delete-form" = "/announcements/{announcements_announcement}/delete",
 *     "version-history" = "/announcements/{announcements_announcement}/revisions",
 *     "revision" = "/announcements/{announcements_announcement}/revisions/{announcements_revision}/view",
 *     "revision_revert" = "/announcements/{announcements_announcement}/revisions/{announcements_revision}/revert",
 *     "revision_delete" = "/announcements/{announcements_announcement}/revisions/{announcements_revision}/delete",
 *     "translation_revert" = "/announcements/{announcements_announcement}/revisions/{announcements_revision}/revert/{langcode}",
 *     "collection" = "/announcements",
 *   },
 *   bundle_entity_type = "announcements_type",
 *   field_ui_base_route = "entity.announcements_type.edit_form"
 * )
 */
class Announcement extends EditorialContentEntityBase implements AnnouncementInterface {

  use EntityChangedTrait;
  use EntityPublishedTrait;

  /**
   * {@inheritdoc}
   */
  public static function preCreate(EntityStorageInterface $storage_controller, array &$values) {
    parent::preCreate($storage_controller, $values);
    $values += [
      'user_id' => \Drupal::currentUser()->id(),
    ];
  }

  /**
   * {@inheritdoc}
   */
  protected function urlRouteParameters($rel) {
    $uri_route_parameters = parent::urlRouteParameters($rel);

    if ($rel === 'revision_revert' && $this instanceof RevisionableInterface) {
      $uri_route_parameters[$this->getEntityTypeId() . '_revision'] = $this->getRevisionId();
    }
    elseif ($rel === 'revision_delete' && $this instanceof RevisionableInterface) {
      $uri_route_parameters[$this->getEntityTypeId() . '_revision'] = $this->getRevisionId();
    }

    return $uri_route_parameters;
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheTagsToInvalidate() {
    $tags = parent::getCacheTagsToInvalidate();
    /** @var \Drupal\announcements\Entity\RegionInterface[] $regions */
    $regions = $this->get('region')->referencedEntities();
    foreach ($regions as $region) {
      $tags[] = $region->getEntityTypeId() . ':' . $region->id();
    }
    return $tags;
  }

  /**
   * Invalidates an entity's cache tags upon save.
   *
   * @param bool $update
   *   TRUE if the entity has been updated, or FALSE if it has been inserted.
   */
  protected function invalidateTagsOnSave($update) {
    parent::invalidateTagsOnSave($update);

    if (!$update) {
      Cache::invalidateTags($this->getCacheTagsToInvalidate());
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheTags() {
    $tags = parent::getCacheTags();

    /** @var \Drupal\announcements\Entity\StyleInterface[] $styles */
    $styles = $this->get('style')->referencedEntities();
    foreach ($styles as $style) {
      $tags[] = $style->getEntityTypeId() . ':' . $style->id();
    }

    return $tags;
  }

  /**
   * {@inheritdoc}
   */
  public function preSave(EntityStorageInterface $storage) {
    parent::preSave($storage);

    foreach (array_keys($this->getTranslationLanguages()) as $langcode) {
      $translation = $this->getTranslation($langcode);

      // If no owner has been set explicitly, make the anonymous user the owner.
      if (!$translation->getOwner()) {
        $translation->setOwnerId(0);
      }
    }

    // If no revision author has been set explicitly,
    // make the announcements owner the revision author.
    if (!$this->getRevisionUser()) {
      $this->setRevisionUserId($this->getOwnerId());
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getTitle() {
    return $this->get('title')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setTitle($name) {
    $this->set('title', $name);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getCreatedTime() {
    return $this->get('created')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setCreatedTime($timestamp) {
    $this->set('created', $timestamp);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwner() {
    return $this->get('user_id')->entity;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwnerId() {
    return $this->get('user_id')->target_id;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwnerId($uid) {
    $this->set('user_id', $uid);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwner(UserInterface $account) {
    $this->set('user_id', $account->id());
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    // Add the published field.
    $fields += static::publishedBaseFieldDefinitions($entity_type);

    $fields['title'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Title'))
      ->setDescription(t('The title of the Announcement.'))
      ->setRevisionable(TRUE)
      ->setSettings([
        'max_length' => 254,
        'text_processing' => 0,
      ])
      ->setDefaultValue('')
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'string',
        'weight' => 0,
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('view', TRUE)
      ->setDisplayConfigurable('form', TRUE)
      ->setRequired(TRUE);

    $fields['user_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Authored by'))
      ->setDescription(t('The user ID of author of the Announcement.'))
      ->setRevisionable(TRUE)
      ->setSetting('target_type', 'user')
      ->setSetting('handler', 'default')
      ->setTranslatable(TRUE)
      ->setDisplayConfigurable('view', TRUE)
      ->setDisplayConfigurable('form', TRUE);

    $fields['style'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Style'))
      ->setRequired(TRUE)
      ->setDescription(t('The Announcement display style.'))
      ->setRevisionable(TRUE)
      ->setSetting('target_type', 'announcements_style')
      ->setSetting('handler', 'default')
      ->setTranslatable(TRUE)
      ->setDisplayOptions('form', [
        'type' => 'options_select',
        'weight' => 1,
      ])
      ->setDisplayConfigurable('view', TRUE)
      ->setDisplayConfigurable('form', TRUE);

    $fields['body'] = BaseFieldDefinition::create('text_with_summary')
      ->setLabel(t('Body'))
      ->setDescription(t('The Announcement Body.'))
      ->setTranslatable(TRUE)
      ->setRevisionable(TRUE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'text_default',
        'weight' => 2,
      ])
      ->setDisplayOptions('form', [
        'type' => 'text_textarea_with_summary',
        'weight' => 2,
      ])
      ->setDisplayConfigurable('view', TRUE)
      ->setDisplayConfigurable('form', TRUE);

    $fields['region'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Region'))
      ->setDescription(t('The Announcement Regions.'))
      ->setRevisionable(TRUE)
      ->setSetting('target_type', 'announcements_region')
      ->setSetting('handler', 'default')
      ->setTranslatable(TRUE)
      ->setDisplayOptions('form', [
        'type' => 'options_buttons',
        'weight' => 3,
      ])
      ->setCardinality(-1)
      ->setDisplayConfigurable('view', TRUE)
      ->setDisplayConfigurable('form', TRUE);

    $fields['status']
      ->setDescription(t('A boolean indicating whether the Announcement is published.'))
      ->setDisplayOptions('form', [
        'type' => 'boolean_checkbox',
        'weight' => 4,
      ]);

    $condition_manager = \Drupal::service('plugin.manager.condition');
    $definitions = $condition_manager->getDefinitions();
    $enabled_plugins = [];
    foreach (array_keys($definitions) as $condition_id) {
      $enabled_plugins[$condition_id] = TRUE;
    }

    $fields['visibility'] = BaseFieldDefinition::create('condition_field')
      ->setLabel(t('Visibility'))
      ->setDescription(t('Visibility of the announcement.'))
      ->setRevisionable(TRUE)
      ->setSetting('handler', 'default')
      ->setSetting('enabled_plugins', $enabled_plugins)
      ->setTranslatable(TRUE)
      ->setDisplayOptions('form', [
        'type' => 'condition_field_default',
        'weight' => 5,
      ])
      ->setDisplayConfigurable('view', FALSE)
      ->setDisplayConfigurable('form', TRUE);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that the entity was created.'));

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the entity was last edited.'));

    $fields['revision_translation_affected'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Revision translation affected'))
      ->setDescription(t('Indicates if the last edit of a translation belongs to current revision.'))
      ->setReadOnly(TRUE)
      ->setRevisionable(TRUE)
      ->setTranslatable(TRUE);

    return $fields;
  }

  /**
   * {@inheritdoc}
   */
  public static function bundleFieldDefinitions(EntityTypeInterface $entity_type, $bundle, array $base_field_definitions) {
    $fields = [];

    /** @var \Drupal\announcements\Entity\AnnouncementTypeInterface $announcement_type */
    $announcement_type = AnnouncementType::load($bundle);
    $fields['visibility'] = clone $base_field_definitions['visibility'];
    $fields['visibility']->setSetting('enabled_plugins', $announcement_type->getEnabledConditions());

    return $fields;
  }

  /**
   * {@inheritdoc}
   */
  public function isDismissible(): bool {
    return $this->type->entity->isDismissible();
  }

  /**
   * {@inheritdoc}
   */
  public function getVisibilityConditions(): array {
    /** @var FieldItemListInterface $visibility_field */
    $visibility_field = $this->get('visibility');

    if (!$visibility_field->isEmpty()) {
     $conditions_config = $visibility_field->first()->getValue();
     return $conditions_config['conditions'] ?? [];

    }

    return [];
  }

  protected static function getConditionManager(): ConditionManager {
    return \Drupal::service('plugin.manager.condition');
  }

  /**
   * {@inheritdoc}
   */
  public function getVisibilityConditionPlugins(): array {
    $condition_configs = $this->getVisibilityConditions();
    $conditions = [];
    $conditions_manager = $this->getConditionManager();
    foreach ($condition_configs as $condition_id => $condition_config) {
      /** @var \Drupal\Core\Condition\ConditionInterface $condition */
      $conditions[] = $conditions_manager->createInstance($condition_id, $condition_config);
    }
    return $conditions;
  }

  /**
   * {@inheritdoc}
   */
  public function hasVisibilityConditions(): bool {
    $conditions = $this->getVisibilityConditions();
    return !empty($conditions);
  }

}
