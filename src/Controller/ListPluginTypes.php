<?php

/**
 * @file
 * Contains \Drupal\plugin\Controller\ListPluginTypes.
 */

namespace Drupal\plugin\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\StringTranslation\TranslationInterface;
use Drupal\plugin\PluginTypeManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Handles the "list plugin types" route.
 */
class ListPluginTypes extends ControllerBase {

  /**
   * The module handler.
   *
   * @var \Drupal\Core\Extension\ModuleHandlerInterface
   */
  protected $moduleHandler;

  /**
   * The plugin type manager.
   *
   * @var \Drupal\plugin\PluginTypeManagerInterface
   */
  protected $pluginTypeManager;

  /**
   * Constructs a new class instance.
   *
   * @param \Drupal\Core\StringTranslation\TranslationInterface $string_translation
   *   The string translator.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler.
   * @param \Drupal\plugin\PluginTypeManagerInterface $plugin_type_manager
   *   The plugin type manager.
   */
  public function __construct(TranslationInterface $string_translation, ModuleHandlerInterface $module_handler, PluginTypeManagerInterface $plugin_type_manager) {
    $this->moduleHandler = $module_handler;
    $this->pluginTypeManager = $plugin_type_manager;
    $this->stringTranslation = $string_translation;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static($container->get('string_translation'), $container->get('module_handler'), $container->get('plugin.plugin_type_manager'));
  }

  /**
   * Handles the route.
   *
   * @return mixed[]
   *   A render array.
   */
  public function execute() {
    $build = [
      '#empty' => $this->t('There are no available plugin types.'),
      '#header' => [$this->t('Type'), $this->t('Description'), $this->t('Provider'), $this->t('Operations')],
      '#type' => 'table',
    ];
    $plugin_types = $this->pluginTypeManager->getPluginTypes();
    foreach ($plugin_types as $plugin_type_id => $plugin_type) {
      $operations_provider = $plugin_type->getOperationsProvider();
      $operations = $operations_provider ? $operations_provider->getOperations($plugin_type_id) : [];

      $build[$plugin_type_id]['label'] = [
        '#markup' => $plugin_type->getLabel(),
      ];
      $build[$plugin_type_id]['description'] = [
        '#markup' => $plugin_type->getDescription(),
      ];
      $build[$plugin_type_id]['provider'] = [
        '#markup' => $this->moduleHandler->getName($plugin_type->getProvider()),
      ];
      $build[$plugin_type_id]['operations'] = [
        '#links' => $operations,
        '#type' => 'operations',
      ];
    }

    return $build;
  }

}
