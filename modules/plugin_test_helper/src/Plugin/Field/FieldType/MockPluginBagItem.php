<?php

/**
 * @file
 * Contains \Drupal\plugin_test_helper\Plugin\Field\FieldType\MockPluginBagItem.
 */

namespace Drupal\plugin_test_helper\Plugin\Field\FieldType;

use Drupal\plugin\Plugin\Field\FieldType\PluginBagItemBase;
use Drupal\plugin_test_helper\Plugin\PluginTestHelper\MockManager;

/**
 * Provides a plugin bag field item for testing.
 *
 * @FieldType(
 *   id = "plugin_test_helper_plugin_bag",
 *   label = @Translation("Plugin bag")
 * )
 */
class MockPluginBagItem extends PluginBagItemBase {

  /**
   * {@inheritdoc}
   */
  public function getPluginManager() {
    return new MockManager();
  }

}
