<?php

namespace Drupal\announcements\Entity;

use Drupal\Core\Config\Entity\ConfigEntityInterface;

/**
 * Provides an interface for defining Announcement type entities.
 */
interface AnnouncementTypeInterface extends ConfigEntityInterface {

  /**
   * Checks the dismissible property value.
   *
   * @return bool
   *   The dismissible value.
   */
  public function isDismissible(): ?bool;

  /**
   * Sets the dismissible property value.
   *
   * @param bool $dismissible
   *   The new value.
   */
  public function setDismissible(bool $dismissible);

  /**
   * Get the enabled plugin ids.
   *
   * @return array
   *   The condition plugin ids.
   */
  public function getEnabledConditions(): array;

  /**
   * Set the enabled plugin ids.
   *
   * @param array $enabled_conditions
   *   The condition plugin ids.
   */
  public function setEnabledConditions(array $enabled_conditions);

}
