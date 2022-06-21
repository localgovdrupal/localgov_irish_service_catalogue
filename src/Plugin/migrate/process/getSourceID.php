<?php
namespace Drupal\localgov_irish_service_catalogue\Plugin\migrate\process;

use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\Row;

/**
 * Provides a 'getSourceID' migrate process plugin.
 *
 * @MigrateProcessPlugin(
 *  id = "get_source_id"
 * )
 */
class getSourceID extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    if ($value) {
      // Execute the query.
      $database = \Drupal::database();
      $query = $database->query("SELECT sourceid1 FROM {migrate_map_localgov_isc_tax_topic}  WHERE destid1 = :id", [':id' => $value]);
      $result = $query->fetchAll();
      if($result){
        return $result[0]->sourceid1;
      }
    }
  }
}
