<?php

/**
 * @file
 * Contains \Drupal\plugin\Plugin\BlockPluginDefinitionMapper.
 */

namespace Drupal\plugin\Plugin;

/**
 * Provides a plugin definition mapper for blocks.
 */
class BlockPluginDefinitionMapper extends DefaultPluginDefinitionMapper {

  /**
   * {@inheritdoc}
   */
  public function getPluginLabel(array $plugin_definition) {
    return $this->hasPluginDefinitionProperty($plugin_definition, 'admin_label') ? $this->getPluginDefinitionProperty($plugin_definition, 'admin_label') : NULL;
  }

}
