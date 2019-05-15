This module adds context filtering capabilities to the Drupal core's user
translation interface page.

## Impacted pages

**Configuration > Regional and Language > User interface translation**

Allows to filter the translated strings by context.

**Configuration > Regional and Language > User interface translation > Export**

Allow to export strings for a specific context.

## Use case

Using contexts when writing custom code is a good practice. It allows to clearly
separate your custom strings from the core and contrib modules strings, and to
be able to translate the same string differently if the context of the word is
not the same.

Example of a translation string using context:
```php
t("This is my custom string", [], ['context' => 'custom_project']);
```

Unfortunately, the Drupal core does not provide any functionalities in the admin
interface to make use of the strings translation contexts. This module adds
basic functionalities to manipulate contexts.
