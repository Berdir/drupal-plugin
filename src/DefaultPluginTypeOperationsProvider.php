<?php

/**
 * @file
 * Contains \Drupal\plugin\DefaultPluginTypeOperationsProvider.
 */

namespace Drupal\plugin;

use Drupal\Core\Url;
use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Provides default operations for plugin types.
 */
class DefaultPluginTypeOperationsProvider implements PluginTypeOperationsProviderInterface {

  use StringTranslationTrait;

  /**
   * {@inheritdoc}
   */
  public function getOperations($plugin_type_id) {
    $operations['list'] = [
      'title' => $this->t('View plugins'),
      'url' => new Url('plugin.plugin.list', [
        'plugin_type_id' => $plugin_type_id,
      ]),
    ];

    return $operations;
  }

}
