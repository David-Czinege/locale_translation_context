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
    $filters = [];

    // Get all languages, except English.
    $this->languageManager->reset();
    $languages = $this->languageManager->getLanguages();
    $language_options = [];
    foreach ($languages as $langcode => $language) {
      if (locale_is_translatable($langcode)) {
        $language_options[$langcode] = $language->getName();
      }
    }

    // Pick the current interface language code for the filter.
    $default_langcode = $this->languageManager->getCurrentLanguage()->getId();
    if (!isset($language_options[$default_langcode])) {
      $available_langcodes = array_keys($language_options);
      $default_langcode = array_shift($available_langcodes);
    }

    $filters['string'] = [
      'title' => $this->t('String contains'),
      'description' => $this->t('Leave blank to show all strings. The search is case sensitive.'),
      'default' => '',
    ];

    $filters['langcode'] = [
      'title' => $this->t('Translation language'),
      'options' => $language_options,
      'default' => $default_langcode,
    ];

    $filters['translation'] = [
      'title' => $this->t('Search in'),
      'options' => [
        'all' => $this->t('Both translated and untranslated strings'),
        'translated' => $this->t('Only translated strings'),
        'untranslated' => $this->t('Only untranslated strings'),
      ],
      'default' => 'all',
    ];

    $filters['customized'] = [
      'title' => $this->t('Translation type'),
      'options' => [
        'all' => $this->t('All'),
        LOCALE_NOT_CUSTOMIZED => $this->t('Non-customized translation'),
        LOCALE_CUSTOMIZED => $this->t('Customized translation'),
      ],
      'states' => [
        'visible' => [
          ':input[name=translation]' => ['value' => 'translated'],
        ],
      ],
      'default' => 'all',
    ];

    $filters['context'] = [
      'title' => $this->t("Context"),
      'options' => $this->getContextOptions(),
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
