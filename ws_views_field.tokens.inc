<?php

/**
 * Implements hook_token_info().
 */
function ws_views_field_token_info() {
  $types = array();
  $tokens = array();

  // Get all existing webforms.
  $webforms = array();
  $entities = \Drupal::entityTypeManager()->getStorage('webform')->loadMultiple(NULL);
  foreach ($entities as $entity) {
    $webforms[$entity->id()] = $entity;
  }

  /**
   * @var $key
   * @var $webform \Drupal\webform\Entity\Webform
   */
  foreach ($webforms as $key => $webform) {
    $type = array(
      'name' => $webform->label(),
      'needs-data' => 'webform_submission',
    );

    $types[$key] = $type;

    $tokens_fields = array();

    // Get the fields used in this webform.
    $webform_elements = $webform->getElementsDecodedAndFlattened();

    $elements = array();
    foreach ($webform_elements as $element_id => $element) {
      if (!in_array($element['#type'], array('details', 'webform_flexbox', 'webform_actions'))) {
        if (isset($element['#title'])) {
          $elements[$element_id] = $element['#title'];

          if (empty($element['#title'])) {
            $element['#title'] = "[{$element_id}]";
          }

          $tokens_fields[$element_id] = array(
            'name' => $element['#title'],
          );
        }
      }
    }

    $tokens[$key] = $tokens_fields;
  }

  $return = array(
    'types' => $types,
    'tokens' => $tokens
  );

  return $return;
}
