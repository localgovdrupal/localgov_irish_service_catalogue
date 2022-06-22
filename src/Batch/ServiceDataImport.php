<?php

namespace Drupal\localgov_irish_service_catalogue\Batch;

use Drupal\localgov_irish_service_catalogue\Commands\LocalGovISCImportCommands;
use Drupal\migrate_tools\MigrateExecutable;
use Drupal\migrate\Plugin\MigrationInterface;
use Drupal\migrate\MigrateMessage;

/**
 * Methods for running the Data import in a batch.
 *
 * @package Drupal\csvimport
 */
class ServiceDataImport {

  /**
   * Handle batch completion.
   *
   */
  public static function ServiceDataImportFinished($success, $results, $operations) {
    $messenger = \Drupal::messenger();
    $messenger->addMessage(t('The Data import has completed.'));
  }

  /**
   * Download Individual file
   */
  public static function ServiceDataImportFetchFile($endpoint, &$context) {

    // Show what's happening
    $context['message'] = t('Fetching Endpoint item @c.', ['@c' => $endpoint['key']]);

    $importer = new LocalGovISCImportCommands();
    $importer->get_data_file($endpoint);

  }

  /**
   * Process a single Migration.
   */
  public static function ServiceDataImportRunMigration($migration_id, &$context) {

    // Show what's happening
    $migration = \Drupal::service('plugin.manager.migration')->createInstance($migration_id);

    $context['message'] = t('Importing item @c.', ['@c' => $migration->label()]);

    localgov_irish_service_catalogue_run_migration($migration_id);

  }

}
