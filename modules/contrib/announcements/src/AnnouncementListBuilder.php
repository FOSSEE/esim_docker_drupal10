<?php

namespace Drupal\announcements;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Entity\EntityTypeInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Defines a class to build a listing of Announcement entities.
 *
 * @ingroup announcements
 */
class AnnouncementListBuilder extends EntityListBuilder {

  /**
   * The condition plugin manager.
   *
   * @var \Drupal\Core\Condition\ConditionManager
   */
  protected $conditionManager;

  /**
   * {@inheritdoc}
   */
  public static function createInstance(ContainerInterface $container, EntityTypeInterface $entity_type) {
    $instance = parent::createInstance($container, $entity_type);
    $instance->conditionManager = $container->get('plugin.manager.condition');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['id'] = $this->t('ID');
    $header['name'] = $this->t('Name');
    $header['status'] = $this->t('Status');
    $header['style'] = $this->t('Style');
    $header['region'] = $this->t('Region');
    $header['visibility'] = $this->t('Visibility');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /** @var \Drupal\announcements\Entity\Announcement $entity */
    $row['id'] = $entity->id();
    $row['name'] = $entity->toLink(NULL, 'edit-form');
    $row['status'] = $entity->isPublished() ? $this->t('Published') : $this->t('Unpublished');

    $row['style'] = $entity->get('style')->first()->entity->label();

    $regions = $entity->get('region')->referencedEntities();
    $row['region'] = [
      'data' => [
        '#theme' => 'item_list',
        '#list_type' => 'ul',
        '#items' => array_map(function ($region) {
          return $region->label();
        }, $regions),
      ],
    ];

    $conditions = $entity->getVisibilityConditionPlugins();
    foreach ($conditions as $condition) {
      $visibility_summary[] = $condition->summary();
    }

    $row['visibility'] = [
      'data' => [
        '#theme' => 'item_list',
        '#list_type' => 'ul',
        '#items' => $visibility_summary ?: $this->t('Always Visible'),
      ],
    ];
    return $row + parent::buildRow($entity);
  }

}
