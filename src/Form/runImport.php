<?php

namespace Drupal\localgov_irish_service_catalogue\Form;

use Drupal\localgov_irish_service_catalogue\Commands\LocalGovISCImportCommands;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * @see \Drupal\Core\Form\FormBase
 */
class runImport extends FormBase {

  /**
   * Build the simple form.
   *
   * A build form method constructs an array that defines how markup and
   * other form elements are included in an HTML form.
   *
   * @param array $form
   *   Default form array structure.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   Object containing current form state.
   *
   * @return array
   *   The render array defining the elements of the form.
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form['description'] = [
      '#type' => 'item',
      '#markup' => $this->t('Import Data from Irish Service Catalogue.'),
    ];

     // Group submit handlers in an actions element with a key of "actions" so
    // that it gets styled correctly, and so that other modules may add actions
    // to the form. This is not required, but is convention.
    $form['actions'] = [
      '#type' => 'actions',
    ];

    // Add a submit button that handles the submission of the form.
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Import'),
    ];

    return $form;
  }

  /**
   * Getter method for Form ID.
   *
   * The form ID is used in implementations of hook_form_alter() to allow other
   * modules to alter the render array built by this form controller. It must be
   * unique site wide. It normally starts with the providing module's name.
   *
   * @return string
   *   The unique ID of the form defined by this class.
   */
  public function getFormId() {
    return 'run_data_import';
  }

  /**
   * Implements a form submit handler.
   *
   * The submitForm method is the default method called for any submit elements.
   *
   * @param array $form
   *   The render array of the currently built form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   Object describing the current state of the form.
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    $batch = [
      'title'            => $this->t('Importing Data ...'),
      'operations'       => [],
      'init_message'     => $this->t('Commencing'),
      'progress_message' => $this->t('Processed @current out of @total.'),
      'error_message'    => $this->t('An error occurred during processing'),
      'finished'         => '\Drupal\localgov_irish_service_catalogue\Batch\ServiceDataImport::ServiceDataImportFinished',
    ];

    $importer = new LocalGovISCImportCommands();
    $endpoints = $importer->getEndpoints();
    foreach($endpoints as $endpoint){
      $data = $importer->get_data_file($endpoint);
      $batch['operations'][] = [
        '\Drupal\localgov_irish_service_catalogue\Batch\ServiceDataImport::ServiceDataImportFetchFile',
        [$endpoint],
      ];
    }

    // List of migration_ids
    $migration_ids = [
      'localgov_isc_tax_user_type',
      'localgov_isc_tax_type',
      'localgov_isc_tax_category',
      'localgov_isc_tax_topic',
      'localgov_isc_content_service_landing',
      'localgov_isc_content_services',
      'localgov_isc_content_services_ga'
    ];

    // Run the migrations
    foreach($migration_ids as $migration_id){
      $batch['operations'][] = [
        '\Drupal\localgov_irish_service_catalogue\Batch\ServiceDataImport::ServiceDataImportRunMigration',
        [$migration_id],
      ];
    }

    batch_set($batch);
  }

}
