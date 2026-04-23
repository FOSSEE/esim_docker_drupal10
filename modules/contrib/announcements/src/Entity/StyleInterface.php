<?php

namespace Drupal\announcements\Entity;

use Drupal\Core\Config\Entity\ConfigEntityInterface;

/**
 * Provides an interface for defining Announcement Style entities.
 */
interface StyleInterface extends ConfigEntityInterface {

  /**
   * Get extra css classes for announcements.
   *
   * @return string
   *   Extra css classes to be added to announcements.
   */
  public function getExtraClasses(): string;
}
