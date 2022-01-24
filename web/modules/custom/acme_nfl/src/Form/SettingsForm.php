<?php

namespace Drupal\acme_nfl\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * ACME Sports settings form.
 */
class SettingsForm extends ConfigFormBase {

    /** @var string Config settings */
    const CONFIG_SETTINGS = 'acme_nfl.settings';

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [static::CONFIG_SETTINGS];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'acme_nfl_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config(static::CONFIG_SETTINGS);

    $form['settings'] = array(
      '#type' => 'vertical_tabs',
      '#default_tab' => 'edit-general-tab',
    );
    $form['teams_api_tab'] = array(
      '#type' => 'details',
      '#title' => $this->t('Teams API'),
      '#group' => 'settings',
    );
    $form['teams_api_tab']['teams_api_endpoint_url'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Endpoint URL'),
      '#default_value' => $config->get('nfl_teams_api_endpoint'),
      '#required' => TRUE,
    );

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
      parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config(static::CONFIG_SETTINGS)
        ->set('nfl_teams_api_endpoint', $form_state->getValue('teams_api_endpoint_url'))
        ->save();

    parent::submitForm($form, $form_state);
  }
}
