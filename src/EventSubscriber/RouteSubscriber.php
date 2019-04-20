<?php

namespace Drupal\locale_translation_context\EventSubscriber;

use Drupal\Core\Routing\RouteSubscriberBase;
use Symfony\Component\Routing\RouteCollection;

/**
 * Listens to the dynamic route events.
 */
class RouteSubscriber extends RouteSubscriberBase {

  /**
   * {@inheritdoc}
   */
  public function alterRoutes(RouteCollection $collection) {
    // Alter the User interface translation page's route.
    if ($route = $collection->get('locale.translate_page')) {
      // Alter the controller.
      $route->setDefault('_controller', '\Drupal\locale_translation_context\Controller\ContextLocaleController::translatePage');
    }
  }

}
