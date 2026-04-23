<?php

namespace Drupal\announcements;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
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
class AnnouncementStorage extends SqlContentEntityStorage implements AnnouncementStorageInterface {

  /**
   * {@inheritdoc}
   */
  public function revisionIds(AnnouncementInterface $entity) {
    return $this->database->query(
      'SELECT vid FROM {announcements_revision} WHERE id=:id ORDER BY vid',
      [':id' => $entity->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function userRevisionIds(AccountInterface $account) {
    return $this->database->query(
      'SELECT vid FROM {announcements_field_revision} WHERE uid = :uid ORDER BY vid',
      [':uid' => $account->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function countDefaultLanguageRevisions(AnnouncementInterface $entity) {
    return $this->database->query('SELECT COUNT(*) FROM {announcements_field_revision} WHERE id = :id AND default_langcode = 1', [':id' => $entity->id()])
      ->fetchField();
  }

  /**
   * {@inheritdoc}
   */
  public function clearRevisionsLanguage(LanguageInterface $language) {
    return $this->database->update('announcements_revision')
      ->fields(['langcode' => LanguageInterface::LANGCODE_NOT_SPECIFIED])
      ->condition('langcode', $language->getId())
      ->execute();
  }

  public function loadActiveForRegion($region) {
    // Build a query to fetch the entity IDs.
    $entity_query = $this
      ->getQuery();
    $entity_query
      ->accessCheck(TRUE);
    $this
      ->buildPropertyQuery($entity_query, [
        'status' => TRUE,
        'region' => $region,
      ]);
    $result = $entity_query
      ->execute();

    $entities = $result ? $this->loadMultiple($result) : [];

    $entities = array_filter($entities, function ($entity) {
      return $entity->access('view');
    });

    return $entities;
  }

}
