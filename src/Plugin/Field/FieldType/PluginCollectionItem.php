<?php

/**
 * @file
 * Contains \Drupal\plugin\Plugin\Field\FieldType\PluginCollectionItemBase.
 */

namespace Drupal\plugin\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a plugin collection field.
 *
 * @FieldType(
 *   default_widget = "plugin_selector:plugin_select_list",
 *   default_formatter = "plugin_label",
 *   id = "plugin_collection",
 *   label = @Translation("Plugin collection"),
 *   category = @Translation("Plugin"),
 *   deriver = "\Drupal\plugin\Plugin\Field\FieldType\PluginCollectionItemDeriver",
 *   list_class = "\Drupal\plugin\Plugin\Field\FieldType\PluginCollectionItemList"
 * )
 */
class PluginCollectionItem extends PluginCollectionItemBase {

  /**
   * {@inheritdoc}
   */
  public function getPluginType() {
    /** @var \Drupal\plugin\PluginTypeManagerInterface $plugin_type_manager */
    $plugin_type_manager = \Drupal::service('plugin.plugin_type_manager');

    return $plugin_type_manager->getPluginType($this->getPluginDefinition()['plugin_type_id']);
  }

}
