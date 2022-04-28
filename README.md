Fork from https://www.drupal.org/project/ws_views_field - a working Drupal 9-compatible version of the module.

# ws_views_field

This module provides a token approach to list WebformSubmission fields in views.
It is used to optimize the page load and performance in case you are using a large database with submissions and you want to list lots of submission fields.

When you have a large amount of submissions and you want to list several fields in view, this could kill your server.
There are a lot of JOINS created with the same webform_submission_data table.

You can use this module to display unlimited number of fields in a view, without creating a new JOIN in the backend.

The token fields can be added like: `[webform_machine_name:field_name]`

## Usage

To use it in a project, add the repository to `composer.json`:
```json
  {
    "type": "vcs",
    "url": "https://github.com/AronNovak/ws_views_field.git"
  },
```

Then:
```
composer require aronnovak/ws_views_field
```
