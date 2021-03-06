<?php

/**
 * @file
 * Contains \Drupal\plugin\PluginDefinition\PluginDefinitionInterface.
 */

namespace Drupal\plugin\PluginDefinition;

/**
 * Defines a plugin definition.
 *
 * @ingroup Plugin
 */
interface PluginDefinitionInterface {

  /**
   * Sets the plugin ID.
   *
   * @param string $id
   *   The plugin ID.
   *
   * @return $this
   */
  public function setId($id);

  /**
   * Gets the plugin ID.
   *
   * @return string
   *   The plugin ID.
   */
  public function getId();

  /**
   * Sets the class.
   *
   * @param string $class
   *   A fully qualified class name.
   *
   * @return $this
   *
   * @throws \InvalidArgumentException
   */
  public function setClass($class);

  /**
   * Gets the class.
   *
   * @return string
   *   A fully qualified class name.
   */
  public function getClass();

  /**
   * Sets the plugin provider.
   *
   * The provider is the name of the module that provides the plugin, or "core',
   * or "component".
   *
   * @param string $provider
   *   The provider.
   *
   * @return $this
   */
  public function setProvider($provider);

  /**
   * Gets the plugin provider.
   *
   * The provider is the name of the module that provides the plugin, or "core',
   * or "component".
   *
   * @return string
   *   The provider.
   */
  public function getProvider();

  /**
   * Merges another definition into this one, using the other for defaults.
   *
   * @param static $other_definition
   *   The other definition to merge into $this. It will not override $this, but
   *   be used to extract default values from instead.
   *
   * @return $this
   *
   * @throws \InvalidArgumentException
   *   Thrown if $other_definition is no instance of $this.
   */
  public function mergeDefaultDefinition(PluginDefinitionInterface $other_definition);

  /**
   * Merges another definition into this one, using the other for overrides.
   *
   * @param static $other_definition
   *   The other definition to merge into $this. It will override any values
   *   already set in $this.
   *
   * @return $this
   *
   * @throws \InvalidArgumentException
   *   Thrown if $other_definition is no instance of $this.
   */
  public function mergeOverrideDefinition(PluginDefinitionInterface $other_definition);

}
