<?php

namespace Drupal\localgov_irish_service_catalogue\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configure Localgov Irish Service Catalogue settings for this site.
 */
class SettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'localgov_irish_service_catalogue_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['localgov_irish_service_catalogue.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['irish_council_id'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Irish Council ID'),
      '#default_value' => $this->config('localgov_irish_service_catalogue.settings')->get('irish_council_id'),
      '#description' => 
        '<p>Enter the ID for your council that the <a href="https://services.localgov.ie">Local Government Services</a> website here.</p>
        <ul>
          <li>Tipperary = <strong>27</strong></li>
          <li>Galway County = <strong>12</strong></li> 
          <li>Laois = <strong>16</strong></li>
        </ul>',
    ];
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('localgov_irish_service_catalogue.settings')
      ->set('irish_council_id', $form_state->getValue('irish_council_id'))
      ->save();
    parent::submitForm($form, $form_state);
  }

}
