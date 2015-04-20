<?php

/**
 * @file
 * Contains \Drupal\plugin_selector_test\SelectablePluginManager.
 */

namespace Drupal\plugin_selector_test;

use Drupal\Component\Plugin\Discovery\StaticDiscovery;
use Drupal\Component\Plugin\Factory\DefaultFactory;
use Drupal\Component\Plugin\PluginManagerBase;

/**
 * Provides a manager for selectable plugins.
 */
class SelectablePluginManager extends PluginManagerBase {

  /**
   * Creates a new instance.
   */
  public function __construct() {
    $this->discovery = new StaticDiscovery();

    $this->discovery->setDefinition('plugin_selector_configurable', array(
      'id' => 'plugin_selector_configurable',
      'label' => t('Configurable selectable plugin'),
      'class' => 'Drupal\plugin_selector_test\Plugin\PluginSelectorTest\SelectablePlugin\Configurable',
    ));

    $this->discovery->setDefinition('plugin_selector_non_configurable', array(
      'id' => 'plugin_selector_non_configurable',
      'label' => t('Non-configurable selectable plugin'),
      'class' => 'Drupal\plugin_selector_test\Plugin\PluginSelectorTest\SelectablePlugin\NonConfigurable',
    ));

    $this->factory = new DefaultFactory($this->discovery, '\Drupal\Component\Plugin\PluginInspectionInterface');
  }
}
