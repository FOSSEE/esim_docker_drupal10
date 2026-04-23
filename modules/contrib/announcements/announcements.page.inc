<?php

/**
 * @file
 * Contains announcements.page.inc.
 *
 * Page callback for Announcement entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Announcement templates.
 *
 * Default template: announcements.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_announcements_announcement(array &$variables) {
  // Fetch Announcement Entity Object.
  $announcement = $variables['elements']['#announcements_announcement'];
  $variables['announcement'] = $announcement;
  $variables['view_mode'] = $variables['elements']['#view_mode'];
  $variables['dismissible'] = $announcement->isDismissible();

  if (!empty($variables['elements']['#region'])) {
    $variables['region'] = $variables['elements']['#region'];
  }

  $style = $announcement->get('style')->entity;
  if ($style) {
    $variables['attributes']['class'][] = 'announcement-style--' . $style->id();
    if ($extra_classes = $style->getExtraClasses()) {
      $variables['attributes']['class'][] = $extra_classes;
    }
  }

  $variables['attributes']['data-announcement-id'] = $announcement->id();

  if ($variables['dismissible']) {
    $variables['attributes']['class'][] = 'announcement-dismissible';
    $variables['#attached']['library'][] = 'announcements/dismiss';
  }

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
