<?php

namespace Drupal\announcements\Routing;

use Drupal\Core\Routing\RouteSubscriberBase;
use Symfony\Component\Routing\RouteCollection;

/**
 * Listens to the dynamic route events.
 */
class RouteSubscriber extends RouteSubscriberBase {

  /**
   * {@inheritdoc}
   */
  protected function alterRoutes(RouteCollection $collection) {
    // Update Announcements List permission.
    if ($route = $collection->get('entity.announcements_announcement.collection')) {
      $route->setRequirement('_permission', 'access announcements_announcement overview');
    }
  }

}
