<?php

/**
 * @file
 * Contains \Drupal\plugin_test_helper\Plugin\PluginTestHelper\MockManager.
 */

namespace Drupal\plugin_test_helper\Plugin\PluginTestHelper;

use Drupal\Component\Plugin\Factory\DefaultFactory;
use Drupal\Component\Plugin\PluginManagerBase;
use Drupal\Component\Plugin\Discovery\StaticDiscovery;
use Drupal\plugin\PluginDefinition\ArrayPluginDefinitionDecorator;
use Drupal\plugin\PluginDiscovery\TypedDiscoveryInterface;

/**
 * Provides a plugin manager for testing plugin-related functionality.
 */
class MockManager extends PluginManagerBase implements TypedDiscoveryInterface {

  /**
   * Constructs a new instance.
   */
  public function __construct() {
    $this->discovery = new StaticDiscovery();

    $plugin_id = 'plugin_test_helper_plugin';
    $this->discovery->setDefinition($plugin_id, new ArrayPluginDefinitionDecorator([
      'id' => $plugin_id,
      'label' => t('Plugin'),
      'class' => 'Drupal\plugin_test_helper\Plugin\PluginTestHelper\MockPlugin',
    ]));

    $configurable_plugin_id = 'plugin_test_helper_configurable_plugin';
    $this->discovery->setDefinition($configurable_plugin_id, new ArrayPluginDefinitionDecorator([
      'id' => $configurable_plugin_id,
      'label' => t('Configurable plugin'),
      'class' => 'Drupal\plugin_test_helper\Plugin\PluginTestHelper\MockConfigurablePlugin',
    ]));

    $this->factory = new DefaultFactory($this->discovery);
  }
}
