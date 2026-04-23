<?php

namespace Drupal\announcements\Controller;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Url;
use Drupal\announcements\Entity\AnnouncementInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class AnnouncementController.
 *
 *  Returns responses for Announcement routes.
 */
class AnnouncementController extends ControllerBase implements ContainerInjectionInterface {

  /**
   * The date formatter.
   *
   * @var \Drupal\Core\Datetime\DateFormatter
   */
  protected $dateFormatter;

  /**
   * The renderer.
   *
   * @var \Drupal\Core\Render\Renderer
   */
  protected $renderer;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    $instance = parent::create($container);
    $instance->dateFormatter = $container->get('date.formatter');
    $instance->renderer = $container->get('renderer');
    return $instance;
  }

  /**
   * Displays a Announcement revision.
   *
   * @param int $announcements_revision
   *   The Announcement revision ID.
   *
   * @return array
   *   An array suitable for drupal_render().
   */
  public function revisionShow($announcements_revision) {
    $announcements = $this->entityTypeManager()->getStorage('announcements')
      ->loadRevision($announcements_revision);
    $view_builder = $this->entityTypeManager()->getViewBuilder('announcements');

    return $view_builder->view($announcements);
  }

  /**
   * Page title callback for a Announcement revision.
   *
   * @param int $announcements_revision
   *   The Announcement revision ID.
   *
   * @return string
   *   The page title.
   */
  public function revisionPageTitle($announcements_revision) {
    $announcements = $this->entityTypeManager()->getStorage('announcements')
      ->loadRevision($announcements_revision);
    return $this->t('Revision of %title from %date', [
      '%title' => $announcements->label(),
      '%date' => $this->dateFormatter->format($announcements->getRevisionCreationTime()),
    ]);
  }

  /**
   * Generates an overview table of older revisions of a Announcement.
   *
   * @param \Drupal\announcements\Entity\AnnouncementInterface $announcements
   *   A Announcement object.
   *
   * @return array
   *   An array as expected by drupal_render().
   */
  public function revisionOverview(AnnouncementInterface $announcements) {
    $account = $this->currentUser();
    $announcements_storage = $this->entityTypeManager()->getStorage('announcements');

    $langcode = $announcements->language()->getId();
    $langname = $announcements->language()->getName();
    $languages = $announcements->getTranslationLanguages();
    $has_translations = (count($languages) > 1);
    $build['#title'] = $has_translations ? $this->t('@langname revisions for %title', ['@langname' => $langname, '%title' => $announcements->label()]) : $this->t('Revisions for %title', ['%title' => $announcements->label()]);

    $header = [$this->t('Revision'), $this->t('Operations')];
    $revert_permission = (($account->hasPermission("revert all announcements_announcement revisions") || $account->hasPermission('administer announcements_announcement entities')));
    $delete_permission = (($account->hasPermission("delete all announcements_announcement revisions") || $account->hasPermission('administer announcements_announcement entities')));

    $rows = [];

    $vids = $announcements_storage->revisionIds($announcements);

    $latest_revision = TRUE;

    foreach (array_reverse($vids) as $vid) {
      /** @var \Drupal\announcements\AnnouncementInterface $revision */
      $revision = $announcements_storage->loadRevision($vid);
      // Only show revisions that are affected by the language that is being
      // displayed.
      if ($revision->hasTranslation($langcode) && $revision->getTranslation($langcode)->isRevisionTranslationAffected()) {
        $username = [
          '#theme' => 'username',
          '#account' => $revision->getRevisionUser(),
        ];

        // Use revision link to link to revisions that are not active.
        $date = $this->dateFormatter->format($revision->getRevisionCreationTime(), 'short');
        if ($vid != $announcements->getRevisionId()) {
          $link = $this->l($date, new Url('entity.announcements.revision', [
            'announcements' => $announcements->id(),
            'announcements_revision' => $vid,
          ]));
        }
        else {
          $link = $announcements->toLink($date)->toString();
        }

        $row = [];
        $column = [
          'data' => [
            '#type' => 'inline_template',
            '#template' => '{% trans %}{{ date }} by {{ username }}{% endtrans %}{% if message %}<p class="revision-log">{{ message }}</p>{% endif %}',
            '#context' => [
              'date' => $link,
              'username' => $this->renderer->renderInIsolation($username),
              'message' => [
                '#markup' => $revision->getRevisionLogMessage(),
                '#allowed_tags' => Xss::getHtmlTagList(),
              ],
            ],
          ],
        ];
        $row[] = $column;

        if ($latest_revision) {
          $row[] = [
            'data' => [
              '#prefix' => '<em>',
              '#markup' => $this->t('Current revision'),
              '#suffix' => '</em>',
            ],
          ];
          foreach ($row as &$current) {
            $current['class'] = ['revision-current'];
          }
          $latest_revision = FALSE;
        }
        else {
          $links = [];
          if ($revert_permission) {
            $links['revert'] = [
              'title' => $this->t('Revert'),
              'url' => $has_translations ?
              Url::fromRoute('entity.announcements.translation_revert', [
                'announcements' => $announcements->id(),
                'announcements_revision' => $vid,
                'langcode' => $langcode,
              ]) :
              Url::fromRoute('entity.announcements.revision_revert', [
                'announcements' => $announcements->id(),
                'announcements_revision' => $vid,
              ]),
            ];
          }

          if ($delete_permission) {
            $links['delete'] = [
              'title' => $this->t('Delete'),
              'url' => Url::fromRoute('entity.announcements.revision_delete', [
                'announcements' => $announcements->id(),
                'announcements_revision' => $vid,
              ]),
            ];
          }

          $row[] = [
            'data' => [
              '#type' => 'operations',
              '#links' => $links,
            ],
          ];
        }

        $rows[] = $row;
      }
    }

    $build['announcements_revisions_table'] = [
      '#theme' => 'table',
      '#rows' => $rows,
      '#header' => $header,
    ];

    return $build;
  }

}
