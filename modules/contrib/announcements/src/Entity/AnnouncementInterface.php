<?php

namespace Drupal\announcements\Entity;

use Drupal\Core\Condition\ConditionInterface;
use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\Core\Entity\EntityPublishedInterface;
use Drupal\Core\Entity\RevisionLogInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Announcement entities.
 *
 * @ingroup announcements
 */
interface AnnouncementInterface extends ContentEntityInterface, RevisionLogInterface, EntityChangedInterface, EntityPublishedInterface, EntityOwnerInterface {

  /**
   * Add get/set methods for your configuration properties here.
   */

  /**
   * Gets the Announcement name.
   *
   * @return string
   *   Name of the Announcement.
   */
  public function getTitle();

  /**
   * Sets the Announcement name.
   *
   * @param string $name
   *   The Announcement name.
   *
   * @return \Drupal\announcements\Entity\AnnouncementInterface
   *   The called Announcement entity.
   */
  public function setTitle($name);

  /**
   * Gets the Announcement creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Announcement.
   */
  public function getCreatedTime();

  /**
   * Sets the Announcement creation timestamp.
   *
   * @param int $timestamp
   *   The Announcement creation timestamp.
   *
   * @return \Drupal\announcements\Entity\AnnouncementInterface
   *   The called Announcement entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Gets the Announcement revision creation timestamp.
   *
   * @return int
   *   The UNIX timestamp of when this revision was created.
   */
  public function getRevisionCreationTime();

  /**
   * Sets the Announcement revision creation timestamp.
   *
   * @param int $timestamp
   *   The UNIX timestamp of when this revision was created.
   *
   * @return \Drupal\announcements\Entity\AnnouncementInterface
   *   The called Announcement entity.
   */
  public function setRevisionCreationTime($timestamp);

  /**
   * Gets the Announcement revision author.
   *
   * @return \Drupal\user\UserInterface
   *   The user entity for the revision author.
   */
  public function getRevisionUser();

  /**
   * Sets the Announcement revision author.
   *
   * @param int $uid
   *   The user ID of the revision author.
   *
   * @return \Drupal\announcements\Entity\AnnouncementInterface
   *   The called Announcement entity.
   */
  public function setRevisionUserId($uid);

  /**
   * Checks the dismissible property value.
   *
   * @return boolean
   *   The dismissible value.
   */
  public function isDismissible(): bool;

  /**
   * Extracts the visibility conditions values from the visiblity field.
   *
   * @return array
   *   Array of condition plugin configuration.
   */
  public function getVisibilityConditions(): array;

  /**
   * Load condition plugin instances.
   *
   * @return ConditionInterface[]
   *   The condition plugin instances.
   */
  public function getVisibilityConditionPlugins(): array;

  /**
   * Check if there are conditions.
   *
   * @return bool
   *   True if there are conditions set in the visibility field. False otherise.
   */
  public function hasVisibilityConditions(): bool;

}
