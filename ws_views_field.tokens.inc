<?php

/**
 * @file
 * Tokens file.
 */

/**
 * Implements hook_token_info().
 */
function ws_views_field_token_info() {
  $types = [];
  $tokens = [];

  // Get all existing webforms.
  $webforms = [];
  $entities = \Drupal::entityTypeManager()
    ->getStorage('webform')
    ->loadMultiple(NULL);

  foreach ($entities as $entity) {
    $webforms[$entity->id()] = $entity;
  }

  /** @var \Drupal\webform\WebformInterface $webform */
  foreach ($webforms as $key => $webform) {
    $type = [
      'name' => $webform->label(),
      'needs-data' => 'webform_submission',
    ];

    $types[$key] = $type;

    $tokens_fields = [];

    // Get the fields used in this webform.
    $webform_elements = $webform->getElementsDecodedAndFlattened();

    $skip_types = ['details', 'webform_flexbox', 'webform_actions'];
    foreach ($webform_elements as $element_id => $element) {
      if (!in_array($element['#type'], $skip_types)) {
        if (isset($element['#title'])) {
          if (empty($element['#title'])) {
            $element['#title'] = "[{$element_id}]";
          }

          $tokens_fields[$element_id] = [
            'name' => $element['#title'],
          ];
        }
      }
    }

    $tokens[$key] = $tokens_fields;
  }

  return [
    'types' => $types,
    'tokens' => $tokens,
  ];
}
