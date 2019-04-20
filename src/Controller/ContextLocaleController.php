<?php

namespace Drupal\locale_translation_context\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Return response for manual check translations.
 */
class ContextLocaleController extends ControllerBase {

  /**
   * Shows the string search screen.
   *
   * @return array
   *   The render array for the string search screen.
   */
  public function translatePage() {
    return [
      'filter' => $this->formBuilder()->getForm('Drupal\locale_translation_context\Form\Translation\ContextTranslateFilterForm'),
      'form' => $this->formBuilder()->getForm('Drupal\locale_translation_context\Form\Translation\ContextTranslateEditForm'),
    ];
  }

}
