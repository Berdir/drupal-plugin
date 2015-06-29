<?php

/**
 * @file
 * Contains \Drupal\plugin\PluginTypeManager.
 */

namespace Drupal\plugin;

use Drupal\Component\Discovery\YamlDiscovery;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a plugin type manager.
 */
class PluginTypeManager implements PluginTypeManagerInterface {

  /**
   * The service container.
   *
   * @var \Symfony\Component\DependencyInjection\ContainerInterface
   */
  protected $container;

  /**
   * The module handler.
   *
   * @var \Drupal\Core\Extension\ModuleHandlerInterface
   */
  protected $moduleHandler;

  /**
   * Plugin type definition defaults.
   *
   * @var mixed[]
   */
  protected $pluginTypeDefinitionDefaults = [
    'class' => '\Drupal\plugin\PluginType',
  ];

  /**
   * The known plugin types.
   *
   * @var \Drupal\plugin\PluginTypeInterface[]|null
   *   An array of plugin types or NULL if plugin type discovery has not been
   *   executed yet.
   */
  protected $pluginTypes;

  /**
   * Creates a new instance.
   *
   * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
   *   The service container.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler.
   */
  public function __construct(ContainerInterface $container, ModuleHandlerInterface $module_handler) {
    $this->container = $container;
    $this->moduleHandler = $module_handler;
  }

  /**
   * {@inheritdoc}
   */
  public function hasPluginType($id) {
    return isset($this->getPluginTypes()[$id]);
  }

  /**
   * {@inheritdoc}
   */
  public function getPluginType($id) {
    $plugin_types = $this->getPluginTypes();
    if (isset($plugin_types[$id])) {
      return $plugin_types[$id];
    }
    else {
      throw new \InvalidArgumentException(sprintf('Plugin type %s is unknown.', $id));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getPluginTypes() {
    if (is_null($this->pluginTypes)) {
      $this->pluginTypes = [];

      // Get the plugin type definitions.
      $plugin_types_data_discovery = new YamlDiscovery('plugin_type', $this->moduleHandler->getModuleDirectories());
      $plugin_types_data_by_module = $plugin_types_data_discovery->findAll();

      // For every definition, set defaults and instantiate an object.
      foreach ($plugin_types_data_by_module as $module => $plugin_types_data) {
        foreach ($plugin_types_data as $plugin_type_id => $plugin_type_data) {
          $plugin_type_data += $this->pluginTypeDefinitionDefaults;
          $plugin_type_data['id'] = $plugin_type_id;
          /** @var \Drupal\plugin\PluginTypeInterface $class */
          $class = $plugin_type_data['class'];
          $this->pluginTypes[$plugin_type_id] = $class::createFromDefinition($this->container, $plugin_type_data);
        }
      }
    }

    return $this->pluginTypes;
  }

}
