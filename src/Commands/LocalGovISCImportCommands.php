<?php

namespace Drupal\localgov_irish_service_catalogue\Commands;

use Drush\Commands\DrushCommands;
use Drupal\Core\File\FileSystemInterface;

/**
 * A Drush commandfile.
 *
 * In addition to this file, you need a drush.services.yml
 * in root of your module, and a composer.json file that provides the name
 * of the services file to use.
 *
 * See these files for an example of injecting Drupal services:
 *   - http://cgit.drupalcode.org/devel/tree/src/Commands/DevelCommands.php
 *   - http://cgit.drupalcode.org/devel/tree/drush.services.yml
 */
class LocalgovISCImportCommands extends DrushCommands {

    /**
   * Update source files from Legacy Web server
   *
   *
   * @command lgisc:get-data
   * @aliases lgiscgd
   */
  public function getItems() {

    $endpoints = $this->getEndpoints();
    foreach($endpoints as $endpoint){
      $data = $this->get_data_file($endpoint);
    }

  }
  /**
   * Generated Endpoint Defintions
   *
   */
  public function getEndpoints() {
    $localAuthority = \Drupal::config('localgov_irish_service_catalogue.settings')->get('irish_council_id');
    $endpoints = [
     [
        'key' =>  'getServices-en',
        'url' => 'https://services.localgov.ie/DesktopModules/Inventise.LGMA.Public/V1/API/Public/GetServices',
        'json' => [
          "LocalAuthority" => (int)$localAuthority,
          "Language" => (int)"1"
        ]
      ],
     [
        'key' =>  'getServices-ga',
        'url' => 'https://services.localgov.ie/DesktopModules/Inventise.LGMA.Public/V1/API/Public/GetServices',
        'json' => [
          "LocalAuthority" => (int)$localAuthority,
          "Language" => (int)"2"
        ]
      ],
      [
        'key' =>  'getTopics-en',
        'url' => 'https://services.localgov.ie/DesktopModules/Inventise.LGMA.API/V1/API/Search/GetTopics',
        'json' => [
          "LocalAuthority" => (int)$localAuthority,
          "Language" => (int)"1"
        ]
      ],
      [
        'key' =>  'getSubTopics-en',
        'url' => 'https://services.localgov.ie/DesktopModules/Inventise.LGMA.API/V1/API/Search/GetSubTopics',
        'json' => [
          "LocalAuthority" => (int)$localAuthority,
          "Language" => (int)"1"
        ]
      ],
      [
        'key' =>  'getTypes-en',
        'url' => 'https://services.localgov.ie/DesktopModules/Inventise.LGMA.API/V1/API/Search/GetTypes',
        'json' => [
          "LocalAuthority" => (int)$localAuthority,
          "Language" => (int)"1"
        ]
      ],
      [
        'key' =>  'getUserTypes-en',
        'url' => 'https://services.localgov.ie/DesktopModules/Inventise.LGMA.API/V1/API/Search/GetUserTypes',
        'json' => [
          "LocalAuthority" => (int)$localAuthority,
          "Language" => (int)"1"
        ]
      ],
    ];
    return $endpoints;
  }

  public function get_data_file($endpoint) {

    $url = $endpoint['url'];

    $options['verify'] = FALSE;
    // $options['debug'] = true;
    $options['verbose'] = true;
    $options['timeout'] = 60;
    $options['headers']['Content-Type'] = 'application/json';
    $options['json'] = $endpoint['json'];

    $client = \Drupal::httpClient();
    try {
      $request = $client->get($url, $options);
      if (empty($request)) {
        throw new Exception('No response at ' . $url . '.');
      }
    }
    catch (RequestException $e) {
      throw new Exception('Error message: ' . $e->getMessage() . ' at ' . $url . '.');
    }

    $data = $request->getBody()->getContents();
    $dir = 'migrate/source/isc';
    $option = \Drupal\Core\File\FileSystemInterface::CREATE_DIRECTORY;
    $path = "public://$dir";
    \Drupal::service('file_system')->prepareDirectory($path, $option);
    $option = FileSystemInterface::EXISTS_REPLACE;
    $key = $endpoint['key'];
    $file = \Drupal::service('file_system')->saveData($data, "public://$dir/$key.json", $option);
    return;
  }

}
