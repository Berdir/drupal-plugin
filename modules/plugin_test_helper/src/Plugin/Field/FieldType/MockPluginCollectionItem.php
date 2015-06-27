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
  public function getPluginManager() {
    return new MockManager();
  }

}
