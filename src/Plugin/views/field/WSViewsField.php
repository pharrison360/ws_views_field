<?php

namespace Drupal\ws_views_field\Plugin\views\field;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Markup;
use Drupal\views\Plugin\views\field\FieldPluginBase;
use Drupal\views\ResultRow;

/**
 * Field handler to WS Field Token.
 *
 * @ingroup views_field_handlers
 *
 * @ViewsField("ws_views_field")
 */
class WSViewsField extends FieldPluginBase {

  /**
   * Empty query method.
   */
  public function query() {
    // Leave empty to avoid a query on this field.
  }

  /**
   * Define the available options.
   *
   * @return array
   *   Return defined options.
   */
  protected function defineOptions() {
    $options = parent::defineOptions();
    $options['token'] = ['default' => ''];

    return $options;
  }

  /**
   * Provide the options form.
   */
  public function buildOptionsForm(&$form, FormStateInterface $form_state) {
    $form['token'] = [
      '#title' => $this->t('Token'),
      '#type' => 'textarea',
      '#default_value' => $this->options['token'],
      '#description' => $this->t('Please enter one or more tokens from available list of tokens.'),
    ];

    // Available tokens.
    $form['ws_tokens'] = [
      '#type' => 'details',
      '#title' => $this->t('Webform Submissions Tokens'),
    ];

    // Add webform tokens.
    $webforms = $this->getWebforms();
    $form['ws_tokens']['tokens'] = \Drupal::service('token.tree_builder')
      ->buildRenderable(array_keys($webforms), ['recursion_limit' => 1]);

    // Display info on how to use tokens from webform_submission module.
    module_load_include('inc', 'webform', 'webform.tokens');
    $webform_submission_tokens = webform_token_info();

    $form['ws_tokens']['ws_use_token'] = [
      '#type' => 'details',
      '#title' => $this->t('Advanced use of tokens'),
    ];

    $description = $webform_submission_tokens['tokens']['webform_submission']['values']['description'];
    $form['ws_tokens']['ws_use_token']['info'] = [
      '#markup' => $description,
    ];

    parent::buildOptionsForm($form, $form_state);
  }

  /**
   * Render function.
   */
  public function render(ResultRow $values) {
    // Get all webform submission entities used in this view.
    $webfom_submission_entities = [];
    if (get_class($values->_entity) == 'Drupal\webform\Entity\WebformSubmission') {
      $webfom_submission_entities[$values->_entity->bundle()] = $values->_entity;
    }

    if (isset($values->_relationship_entities)) {
      foreach ($values->_relationship_entities as $entity) {
        if (get_class($entity) == 'Drupal\webform\Entity\WebformSubmission') {
          $webfom_submission_entities[$entity->bundle()] = $entity;
        }
      }
    }

    // Get the token.
    $token = $this->options['token'];
    $token_service = \Drupal::token();

    // Replace the tokens for each webform submissions.
    foreach ($webfom_submission_entities as $bundle => $webform_submission_entity) {
      // Create the real  webform submission.
      $token = str_replace($bundle . ':', "webform_submission:values:", $token);

      $token = $token_service->replace($token, [
        'webform_submission' => $webform_submission_entity,
      ]);
    }

    return Markup::create($token);
  }

  /**
   * Get existing webforms.
   *
   * @return \Drupal\webform\Entity\Webform[]
   *   All available webforms.
   */
  private function getWebforms() {
    // Get all existing webforms.
    $webforms = [];
    $entities = \Drupal::entityTypeManager()->getStorage('webform')->loadMultiple(NULL);
    foreach ($entities as $entity) {
      $webforms[$entity->id()] = $entity;
    }

    return $webforms;
  }

}
