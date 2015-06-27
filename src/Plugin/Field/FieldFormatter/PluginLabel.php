<?php

/**
 * @file
 * Contains \Drupal\plugin\Plugin\field\formatter\PluginLabel.
 */

namespace Drupal\plugin\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\plugin\Plugin\DefaultPluginDefinitionMapper;

/**
 * A plugin bag field formatter.
 *
 * @FieldFormatter(
 *   id = "plugin_label",
 *   label = @Translation("Label")
 * )
 *
 * @see plugin_field_formatter_info_alter()
 */
class PluginLabel extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items) {
    $build = [];
    /** @var \Drupal\plugin\Plugin\Field\FieldType\PluginCollectionItemInterface $item */
    foreach ($items as $delta => $item) {
      $plugin_definition = $item->getContainedPluginInstance()->getPluginDefinition();
      $mapper = new DefaultPluginDefinitionMapper();
      $build[$delta] = [
        '#markup' => $mapper->getPluginLabel($plugin_definition),
      ];
    }

    return $build;
  }

}
