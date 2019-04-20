<?php

namespace Drupal\locale_translation_context\Form\Translation;

use Drupal\locale\Form\TranslateEditForm;

/**
 * Defines a translation edit form.
 */
class ContextTranslateEditForm extends TranslateEditForm {

  /**
   * Builds a string search query and returns an array of string objects.
   *
   * @return \Drupal\locale\StringInterface[]
   *   Array of \Drupal\locale\StringInterface objects.
   */
  protected function translateFilterLoadStrings() {
    $filter_values = $this->translateFilterValues();

    // Language is sanitized to be one of the possible options in
    // translateFilterValues().
    $conditions = ['language' => $filter_values['langcode']];
    $options = [
      'pager limit' => 30,
      'translated' => TRUE,
      'untranslated' => TRUE,
    ];

    // Add translation status conditions and options.
    switch ($filter_values['translation']) {
      case 'translated':
        $conditions['translated'] = TRUE;
        if ($filter_values['customized'] != 'all') {
          $conditions['customized'] = $filter_values['customized'];
        }
        break;

      case 'untranslated':
        $conditions['translated'] = FALSE;
        break;

    }

    if (!empty($filter_values['string'])) {
      $options['filters']['source'] = $filter_values['string'];
      if ($options['translated']) {
        $options['filters']['translation'] = $filter_values['string'];
      }
    }

    if (!empty($filter_values['context'])) {
      $conditions['context'] = $filter_values['context'];
    }

    return $this->localeStorage->getTranslations($conditions, $options);
  }

}
