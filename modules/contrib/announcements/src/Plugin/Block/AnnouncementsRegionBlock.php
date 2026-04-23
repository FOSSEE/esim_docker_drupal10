<?php

namespace Drupal\announcements\Plugin\Block;

use Drupal\announcements\Entity\Region;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a 'AnnouncementsRegionBlock' block.
 *
 * @Block(
 *  id = "announcements_region_block",
 *  admin_label = @Translation("Announcements Region"),
 *  category = @Translation("Announcements"),
 *  deriver = "Drupal\announcements\Plugin\Derivative\AnnouncementRegions"
 * )
 */
class AnnouncementsRegionBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The announcement entity storage handler.
   *
   * @var \Drupal\announcements\AnnouncementStorageInterface
   */
  protected $announcementStorage;

  /**
   * The announcement view builder handler.
   *
   * @var \Drupal\Core\Entity\EntityViewBuilderInterface
   */
  protected $announcementViewBuilder;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    $instance = new static($configuration, $plugin_id, $plugin_definition);

    $entity_type_manager = $container->get('entity_type.manager');

    $instance->announcementStorage = $entity_type_manager->getStorage('announcements_announcement');
    $instance->announcementViewBuilder = $entity_type_manager->getViewBuilder('announcements_announcement');

    return $instance;
  }

  /**
   * Get the region id.
   *
   * @return string|null
   *   The region id.
   */
  public function getRegionId() {
    $definition = $this->getPluginDefinition();

    return $definition['announcements_region'] ?? NULL;
  }

  /**
   * Get the region as an entity.
   *
   * @return \Drupal\announcements\Entity\RegionInterface|null
   *   The region entity.
   */
  public function getRegion() {
    static $region = NULL;
    if (!$region) {
      $region_id = $this->getRegionId();
      if ($region_id) {
        $region = Region::load($region_id);
      }
    }
    return $region;
  }

  /**
   * Get the block's configured display mode.
   *
   * @return string
   *   The announcements display mode.
   */
  public function getAnnoucementDisplayMode() {
    $definition = $this->getPluginDefinition();

    return $definition['announcement_display_mode'] ?? 'default';
  }

  /**
   * Get the block's announcements.
   *
   * @return \Drupal\announcements\Entity\AnnouncementInterface[]
   *   The announcements entities.
   */
  public function getAnnouncements() {
    static $announcements = NULL;
    if (is_null($announcements)) {
      $announcements = $this->announcementStorage->loadActiveForRegion($this->getRegionId());
    }
    return $announcements;
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $announcements = $this->getAnnouncements();

    $build = [];
    $display_mode = $this->getAnnoucementDisplayMode();
    foreach ($announcements as $announcement) {
      $build['announcements-' . $announcement->id()] = $this->announcementViewBuilder->view($announcement, $display_mode);
      $build['announcements-' . $announcement->id()]['#region'] = $this->getRegionId();
    }

    return $build;
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheTags() {
    $tags = parent::getCacheTags();
    $region = $this->getRegion();
    $tags[] = $region->getEntityTypeId() . ':' . $region->id();

    $announcements = $this->getAnnouncements();
    foreach ($announcements as $announcement) {
      $tags[] = $announcement->getEntityTypeId() . ':' . $announcement->id();
    }

    return $tags;
  }

}
