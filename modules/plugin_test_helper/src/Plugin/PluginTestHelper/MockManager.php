<?php

/**
 * @file
 * Contains \Drupal\plugin_test_helper\Plugin\PluginTestHelper\MockManager.
 */

namespace Drupal\plugin_test_helper\Plugin\PluginTestHelper;

use Drupal\Component\Plugin\Factory\DefaultFactory;
use Drupal\Component\Plugin\PluginManagerBase;
use Drupal\Component\Plugin\Discovery\StaticDiscovery;

/**
 * Provides a plugin manager for testing plugin-related functionality.
 */
class MockManager extends PluginManagerBase {

  /**
   * Constructs a new instance.
   */
  public function __construct() {
    $this->discovery = new StaticDiscovery();

    $this->discovery->setDefinition('plugin_test_helper_plugin', array(
      'label' => t('Plugin'),
      'class' => 'Drupal\plugin_test_helper\Plugin\PluginTestHelper\MockPlugin',
    ));

    $this->discovery->setDefinition('plugin_test_helper_configurable_plugin', array(
      'label' => t('Configurable plugin'),
      'class' => 'Drupal\plugin_test_helper\Plugin\PluginTestHelper\MockConfigurablePlugin',
    ));

    $this->factory = new DefaultFactory($this->discovery);
  }
}
