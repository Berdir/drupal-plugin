<?php

/**
 * @file
 * Contains \Drupal\plugin_test_helper\Plugin\Field\FieldType\MockPluginCollectionItem.
 */

namespace Drupal\plugin_test_helper\Plugin\Field\FieldType;

use Drupal\plugin\Plugin\Field\FieldType\PluginCollectionItemBase;
use Drupal\plugin_test_helper\Plugin\PluginTestHelper\MockManager;

/**
 * Provides a plugin collection field item for testing.
 *
 * @FieldType(
 *   id = "plugin_test_helper_plugin_collection",
 *   label = @Translation("Plugin collection")
 * )
 */
class MockPluginCollectionItem extends PluginCollectionItemBase {

  /**
   * {@inheritdoc}
   */
  public function getPluginType() {
    /** @var \Drupal\plugin\PluginTypeManagerInterface $plugin_type_manager */
    $plugin_type_manager = \Drupal::service('plugin.plugin_type_manager');

    return $plugin_type_manager->getPluginType('plugin_test_helper.mock');
  }

}
