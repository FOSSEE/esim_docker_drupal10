<?php

namespace Drupal\announcements;

use Drupal\Core\Entity\ContentEntityStorageInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\announcements\Entity\AnnouncementInterface;

/**
 * Defines the storage handler class for Announcement entities.
 *
 * This extends the base storage class, adding required special handling for
 * Announcement entities.
 *
 * @ingroup announcements
 */
interface AnnouncementStorageInterface extends ContentEntityStorageInterface {

  /**
   * Gets a list of Announcement revision IDs for a specific Announcement.
   *
   * @param \Drupal\announcements\Entity\AnnouncementInterface $entity
   *   The Announcement entity.
   *
   * @return int[]
   *   Announcement revision IDs (in ascending order).
   */
  public function revisionIds(AnnouncementInterface $entity);

  /**
   * Gets a list of revision IDs having a given user as Announcement author.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user entity.
   *
   * @return int[]
   *   Announcement revision IDs (in ascending order).
   */
  public function userRevisionIds(AccountInterface $account);

  /**
   * Counts the number of revisions in the default language.
   *
   * @param \Drupal\announcements\Entity\AnnouncementInterface $entity
   *   The Announcement entity.
   *
   * @return int
   *   The number of revisions in the default language.
   */
  public function countDefaultLanguageRevisions(AnnouncementInterface $entity);

  /**
   * Unsets the language for all Announcement with the given language.
   *
   * @param \Drupal\Core\Language\LanguageInterface $language
   *   The language object.
   */
  public function clearRevisionsLanguage(LanguageInterface $language);

}
