<?php

/**
 * @file
 * Contains \Drupal\plugin\Plugin\DataType\PluginId.
 */

namespace Drupal\plugin\Plugin\DataType;

use Drupal\Core\TypedData\Plugin\DataType\StringData;

/**
 * Provides a plugin ID data type.
 *
 * @DataType(
 *   id = "plugin_id",
 *   label = @Translation("Plugin ID")
 * )
 */
class PluginId extends StringData {

  /**
   * {@inheritdoc}
   */
  public function setValue($value, $notify = TRUE) {
    $value = (string) $value;
    /** @var \Drupal\plugin\Plugin\Field\FieldType\PluginCollectionItemInterface $parent */
    $parent = $this->getParent();
    $plugin_instance = $parent->getContainedPluginInstance();
    if ($value && (!$plugin_instance || $plugin_instance->getPluginId() != $value)) {
      $plugin_instance = $parent->createContainedPluginInstance($value);
      $parent->setContainedPluginInstance($plugin_instance);
      $this->parent->onChange($this->getName());
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getValue() {
    /** @var \Drupal\plugin\Plugin\Field\FieldType\PluginCollectionItemInterface $parent */
    $parent = $this->getParent();
    $plugin_instance = $parent->getContainedPluginInstance();
    if ($plugin_instance) {
      return $plugin_instance->getPluginId();
    }
    return '';
  }

}
