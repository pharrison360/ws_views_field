<?php


/**
 * Implements hook_views_data_alter().
 */
function ws_views_field_views_data_alter(array &$data) {
  $data['webform_submission']['ws_views_field'] = array(
    'title' => t('Token Field'),
    'field' => array(
      'title' => t('Token Field'),
      'help' => t('Add a webform submission field using a token.'),
      'id' => 'ws_views_field',
    ),
  );
}
