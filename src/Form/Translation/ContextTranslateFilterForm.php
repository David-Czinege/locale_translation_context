<?php

namespace Drupal\locale_translation_context\Form\Translation;

use Drupal\Core\Database\Database;
use Drupal\Core\Form\FormStateInterface;
use Drupal\locale\Form\TranslateFilterForm;

/**
 * Provides a filtered translation edit form.
 */
class ContextTranslateFilterForm extends TranslateFilterForm {

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildForm($form, $form_state);
    $css = '.locale-translate-filter-form .form-item-context{ float: left; margin-right: 1em; margin-bottom: 0; width: 15em; }';
    $form['#attached']['html_head'][] = [
      [
        '#tag' => 'style',
        '#value' => $css,
      ],
      'banner-css',
    ];

    $form['filters']['status']['context']['#empty_option'] = $this->t('All contexts');

    return $form;
  }

  /**
   * Lists locale translation filters that can be applied.
   */
  protected function translateFilters() {
    $filters = parent::translateFilters();

    $filters['context'] = [
      'title' => $this->t("Context"),
      'options' => locale_translation_context_get_context_options(),
      'default' => '',
      'description' => $this->t('Leave blank to include all contexts. The search is case sensitive.'),
    ];

    return $filters;
  }

  /**
   * Get the available context list.
   *
   * @return array
   *   Array of the available contexts.
   */
  protected function getContextOptions() {

    $context_options = [];

    $connection = Database::getConnection();
    $data = $connection->select('locales_source', 'ls');
    $data->fields('ls', ['context']);
    $data->isNotNull('context');
    $data->groupBy('context');
    $data->orderBy('context');
    $results = $data->execute();

    foreach ($results as $result) {
      if (!empty($result->context)) {
        $context_options[$result->context] = $result->context;
      }
    }

    return $context_options;
  }

}
