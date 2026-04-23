<?php

/** 
 * @file
 * Contains \Drupal\announcements\Plugin\Derivative\AnnouncementRegions.php.
 */
namespace Drupal\announcements\Plugin\Derivative;

use Drupal\Component\Plugin\Derivative\DeriverBase;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Plugin\Discovery\ContainerDeriverInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides block plugin definitions for all announcement regions.
 *
 * @see \Drupal\announcements\Plugin\Block\AnnouncementRegions
 */
class AnnouncementRegions extends DeriverBase implements ContainerDeriverInterface
{

  /**
   * The announcement region storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $regionStorage;  

  /**
   * Creates a new NodeBlock.
   *
   * @param \Drupal\Core\Entity\EntityStorageInterface $region_storage
   *   The announcement region storage.
   */
  public function __construct(EntityStorageInterface $region_storage) 
  {
    $this->regionStorage = $region_storage;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, $base_plugin_id) 
  {
    return new static(
      $container->get('entity_type.manager')->getStorage('announcements_region')
    );
  }
  /**
   * {@inheritdoc}
   */
  public function getDerivativeDefinitions($base_plugin_definition) 
  {
    $regions = $this->regionStorage->loadMultiple();
    foreach ($regions as $region) {
      $this->derivatives[$region->id()] = $base_plugin_definition;
      $this->derivatives[$region->id()]['admin_label'] = $base_plugin_definition['admin_label'] . ' ' . $region->label();
      $this->derivatives[$region->id()]['announcements_region'] = $region->id();
    }
    return $this->derivatives;
  }

}
