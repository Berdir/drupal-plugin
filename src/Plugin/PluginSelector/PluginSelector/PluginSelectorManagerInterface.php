<?php

/**
 * @file
 * Contains \Drupal\plugin_selector\Plugin\PluginSelector\PluginSelector\PluginSelectorManagerInterface.
 */

namespace Drupal\plugin_selector\Plugin\PluginSelector\PluginSelector;

use Drupal\Component\Plugin\PluginManagerInterface;

/**
 * Defines a plugin selector manager.
 */
interface PluginSelectorManagerInterface extends PluginManagerInterface {

  /**
   * Creates a plugin selector.
   *
   * @param string $plugin_id
   *   The id of the plugin being instantiated.
   * @param array $configuration
   *   An array of configuration relevant to the plugin instance.
   *
   * @return \Drupal\plugin_selector\Plugin\PluginSelector\PluginSelector\PluginSelectorInterface
   */
  public function createInstance($plugin_id, array $configuration = []);

}
