<?php

/**
 * @file
 * Contains \Drupal\plugin\Plugin\DefaultPluginDefinitionMapper.
 */

namespace Drupal\plugin\Plugin;

/**
 * Provides a default plugin definition mapper.
 */
class DefaultPluginDefinitionMapper implements PluginDefinitionMapperInterface {

  /**
   * {@inheritdoc}
   */
  public function getPluginId(array $plugin_definition) {
    return $this->getPluginDefinitionProperty($plugin_definition, 'id');
  }

  /**
   * {@inheritdoc}
   */
  public function getParentPluginId(array $plugin_definition) {
    return $this->hasPluginDefinitionProperty($plugin_definition, 'parent_id') ? $this->getPluginDefinitionProperty($plugin_definition, 'parent_id') : NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function getPluginLabel(array $plugin_definition) {
    return $this->hasPluginDefinitionProperty($plugin_definition, 'label') ? $this->getPluginDefinitionProperty($plugin_definition, 'label') : $this->getPluginId($plugin_definition);
  }

  /**
   * {@inheritdoc}
   */
  public function getPluginDescription(array $plugin_definition) {
    return $this->hasPluginDefinitionProperty($plugin_definition, 'description') ? $this->getPluginDefinitionProperty($plugin_definition, 'description') : NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function getPluginProvider(array $plugin_definition) {
    return $this->hasPluginDefinitionProperty($plugin_definition, 'provider') ? $this->getPluginDefinitionProperty($plugin_definition, 'provider') : NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function hasPluginDefinitionProperty(array $plugin_definition, $name) {
    return array_key_exists($name, $plugin_definition);
  }

  /**
   * {@inheritdoc}
   */
  public function getPluginDefinitionProperty(array $plugin_definition, $name) {
    if ($this->hasPluginDefinitionProperty($plugin_definition, $name)) {
      return $plugin_definition[$name];
    }
    else {
      throw new \InvalidArgumentException(sprintf('Plugin definition property "%s" does not exist.', $name));
    }
  }

}
