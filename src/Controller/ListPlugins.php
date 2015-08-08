<?php

/**
 * @file
 * Contains \Drupal\plugin\Controller\ListPlugins.
 */

namespace Drupal\plugin\Controller;

use Drupal\plugin\PluginDefinition\PluginDefinitionInterface;
use Drupal\plugin\PluginDefinition\PluginDescriptionDefinitionInterface;
use Drupal\plugin\PluginDefinition\PluginLabelDefinitionInterface;
use Drupal\plugin\PluginDiscovery\TypedDefinitionEnsuringPluginDiscoveryDecorator;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Handles the "list plugin" route.
 */
class ListPlugins extends ListBase {

  /**
   * Returns the route's title.
   *
   * @param string $plugin_type_id
   *   The plugin type ID.
   *
   * @return string
   */
  public function title($plugin_type_id) {
    return $this->t('%label plugins', [
      '%label' => $this->pluginTypeManager->getPluginType($plugin_type_id)->getLabel(),
    ]);
  }

  /**
   * Handles the route.
   *
   * @param string $plugin_type_id
   *   The plugin type ID.
   *
   * @return mixed[]|\Symfony\Component\HttpFoundation\Response
   *   A render array or a Symfony response.
   */
  public function execute($plugin_type_id) {
    if (!$this->pluginTypeManager->hasPluginType($plugin_type_id)) {
      throw new NotFoundHttpException();
    }
    $plugin_type = $this->pluginTypeManager->getPluginType($plugin_type_id);

    $build = [
      '#empty' => $this->t('There are no available plugins.'),
      '#header' => [$this->t('Type'), $this->t('Description'), $this->t('Provider')],
      '#type' => 'table',
    ];
    $plugin_discovery = new TypedDefinitionEnsuringPluginDiscoveryDecorator($plugin_type);
    /** @var \Drupal\plugin\PluginDefinition\PluginDefinitionInterface[] $plugin_definitions */
    $plugin_definitions = [];
    foreach ($plugin_discovery->getDefinitions() as $plugin_definition) {
      $label = $plugin_definition instanceof PluginLabelDefinitionInterface ? (string) $plugin_definition->getLabel() : $plugin_definition->getId();
      $plugin_definitions[$label] = $plugin_definition;
    }
    uksort($plugin_definitions, 'strnatcasecmp');
    foreach ($plugin_definitions as $label => $plugin_definition) {
      $build[$plugin_definition->getId()] = [
        'label' => [
          '#markup' => $label,
        ],
        'description' => [
          '#markup' => $plugin_definition instanceof PluginDescriptionDefinitionInterface ? (string) $plugin_definition->getDescription() : NULL,
        ],
        'provider' => [
          '#markup' => $this->getProviderLabel($plugin_definition->getProvider()),
        ],
      ];
    }

    return $build;
  }

}
