<?php

/**
 * @file
 * Contains \Drupal\plugin\PluginTypeManagerInterface.
 */

namespace Drupal\plugin;

/**
 * Defines a plugin type manager.
 */
interface PluginTypeManagerInterface {

  /**
   * Checks whether a plugin type is known.
   *
   * @param string $id
   *   The plugin type's ID.
   *
   * @return bool
   */
  public function hasPluginType($id);

  /**
   * Gets a known plugin type.
   *
   * @param string $id
   *   The plugin type's ID.
   *
   * @return \Drupal\plugin\PluginTypeInterface
   *
   * @throws \InvalidArgumentException
   *   Thrown if the pplugin type is unknown.
   */
  public function getPluginType($id);

  /**
   * Gets the known plugin types.
   *
   * @return \Drupal\plugin\PluginTypeInterface[]
   */
  public function getPluginTypes();

}
