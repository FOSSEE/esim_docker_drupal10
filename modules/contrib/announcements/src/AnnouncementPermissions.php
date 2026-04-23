<?php

namespace Drupal\announcements;

use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\announcements\Entity\AnnouncementType;


/**
 * Provides dynamic permissions for Announcement of different types.
 *
 * @ingroup announcements
 */
class AnnouncementPermissions {

  use StringTranslationTrait;

  /**
   * Returns an array of node type permissions.
   *
   * @return array
   *   The Announcement by bundle permissions.
   *   @see \Drupal\user\PermissionHandlerInterface::getPermissions()
   */
  public function generatePermissions() {
    $perms = [];

    foreach (AnnouncementType::loadMultiple() as $type) {
      $perms += $this->buildPermissions($type);
    }

    return $perms;
  }

  /**
   * Returns a list of node permissions for a given node type.
   *
   * @param \Drupal\announcements\Entity\AnnouncementType $type
   *   The Announcement type.
   *
   * @return array
   *   An associative array of permission names and descriptions.
   */
  protected function buildPermissions(AnnouncementType $type) {
    $type_id = $type->id();
    $type_params = ['%type_name' => $type->label()];

    return [
      "create $type_id announcements_announcement" => [
        'title' => $this->t('Create new %type_name Announcements', $type_params),
      ],
      "edit own $type_id announcements_announcement" => [
        'title' => $this->t('Edit own %type_name Announcements', $type_params),
      ],
      "edit $type_id any announcements_announcement" => [
        'title' => $this->t('Edit any %type_name Announcements', $type_params),
      ],
      "delete $type_id own announcements_announcement" => [
        'title' => $this->t('Delete own %type_name Announcements', $type_params),
      ],
      "delete $type_id any announcements_announcement" => [
        'title' => $this->t('Delete any %type_name entities', $type_params),
      ],
      "view $type_id announcements_announcement revisions" => [
        'title' => $this->t('View %type_name Announcements revisions', $type_params),
        'description' => t('To view a revision, you also need permission to view the entity item.'),
      ],
      "revert $type_id announcements_announcement revisions" => [
        'title' => $this->t('Revert %type_name Announcements revisions', $type_params),
        'description' => t('To revert a revision, you also need permission to edit the entity item.'),
      ],
      "delete $type_id announcements_announcement revisions" => [
        'title' => $this->t('Delete %type_name Announcements revisions', $type_params),
        'description' => $this->t('To delete a revision, you also need permission to delete the entity item.'),
      ],
    ];
  }

}
