<?php

namespace Drupal\Tests\locale_translation_context\Functional;

use Drupal\Tests\BrowserTestBase;

/**
 * Tests the exportation of locale files with context filter.
 *
 * @group locale
 */
class LocaleContextTest extends BrowserTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = ['locale', 'locale_translation_context'];

  /**
   * A user able to create languages and export translations.
   *
   * @var \Drupal\user\Entity\User|false
   */
  protected $adminUser = NULL;

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    $this->adminUser = $this->drupalCreateUser([
      'administer languages',
      'translate interface',
      'access administration pages',
    ]);
    $this->drupalLogin($this->adminUser);

    // Import some known translations.
    // This will also automatically add the 'hu' language.
    // Create a temporary file.
    $name = \Drupal::service('file_system')->tempnam('temporary://', "po_") . '.po';
    // Place the getPoFile method return value into it.
    file_put_contents($name, $this->getPoFile());
    // Import this po file on Interface translation import page.
    $this->drupalPostForm('admin/config/regional/translate/import', [
      'langcode' => 'hu',
      'files[file]' => $name,
    ], t('Import'));
    // Delete the temporary file.
    \Drupal::service('file_system')->unlink($name);
  }

  /**
   * Test exportation of translations.
   */
  public function testExportTranslation() {

    // Get the Hungarian translations with 'Fruit' context.
    $this->drupalPostForm('admin/config/regional/translate/export', [
      'langcode' => 'hu',
      'context' => 'Fruit',
    ], t('Export'));

    // A new web-assert option for asserting the presence of elements with.
    $session = $this->assertSession();
    // Ensure we have a translation file.
    $session->responseContains('# Hungarian translation of Drupal');
    // Ensure our imported translations exist in the file.
    $session->responseContains('msgstr "Alma"');
    // Ensure our imported and selected context exists in the file.
    $session->responseContains('msgctxt "Fruit"');
    // Ensure our imported but not selected context doesn't exist in the file.
    $session->responseNotContains('msgctxt "Animal"');

    // Test the context export without translation.
    $this->drupalPostForm('admin/config/regional/translate/export', [
      'context' => 'Fruit',
    ], t('Export'));

    // A new web-assert option for asserting the presence of elements with.
    $session = $this->assertSession();
    // Ensure we have a translation file.
    $session->responseContains('# LANGUAGE translation of PROJECT');
    // Ensure translations do not exist in the file.
    $session->responseContains('msgstr ""');
    // Ensure our imported and selected context exists in the file.
    $session->responseContains('msgctxt "Fruit"');
    // Ensure our imported but not selected context doesn't exist in the file.
    $session->responseNotContains('msgctxt "Animal"');
  }

  /**
   * Test the User interface translations page with context.
   */
  public function testUserInterfaceTranslationPageWithContext() {
    // Get the Hungarian translations with 'Fruit' context.
    $this->drupalPostForm('/admin/config/regional/translate', [
      'string' => '',
      'translation' => 'all',
      'langcode' => 'hu',
      'context' => 'Fruit',
    ], t('Filter'));

    // A new web-assert option for asserting the presence of elements with.
    $session = $this->assertSession();
    // Ensure our imported translations appear in the list..
    $session->responseContains('Alma');
    // Ensure our imported and selected context appears in the list.
    $session->pageTextContains('In Context: Fruit');
    // Ensure our imported but not selected context doesn't appear in the list.
    $session->pageTextNotContains('In Context: Animal');
  }

  /**
   * Helper function that returns a proper .po file.
   */
  public function getPoFile() {
    return <<< EOF
msgid ""
msgstr ""
"Project-Id-Version: Drupal 8\\n"
"MIME-Version: 1.0\\n"
"Content-Type: text/plain; charset=UTF-8\\n"
"Content-Transfer-Encoding: 8bit\\n"
"Plural-Forms: nplurals=2; plural=(n > 1);\\n"

msgctxt "Fruit"
msgid "Pear"
msgstr "Körte"
msgctxt "Fruit"
msgid "Apple"
msgstr "Alma"
msgctxt "Animal"
msgid "Elephant"
msgstr "Elefánt"
EOF;
  }

}
