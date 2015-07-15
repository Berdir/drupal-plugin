<?php

/**
 * @file
 * Contains \Drupal\plugin\Controller\ListPlugins.
 */

namespace Drupal\plugin\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\StringTranslation\TranslationInterface;
use Drupal\plugin\PluginTypeManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Handles the "list plugin" route.
 */
class ListPlugins extends ControllerBase {

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
    $plugin_definition_mapper = $plugin_type->getPluginDefinitionMapper();
    $plugin_definitions = $plugin_type->getPluginManager()->getDefinitions();
    uasort($plugin_definitions, function (array $plugin_definition_a, array $plugin_definition_b) use ($plugin_definition_mapper) {
      return strnatcasecmp($plugin_definition_mapper->getPluginLabel($plugin_definition_a), $plugin_definition_mapper->getPluginLabel($plugin_definition_b));
    });
    foreach ($plugin_definitions as $plugin_id => $plugin_definition) {

      $build[$plugin_id]['label'] = [
        '#markup' => $plugin_definition_mapper->getPluginLabel($plugin_definition),
      ];
      $build[$plugin_id]['description'] = [
        '#markup' => $plugin_definition_mapper->getPluginDescription($plugin_definition),
      ];
      $build[$plugin_id]['provider'] = [
        '#markup' => $this->moduleHandler->getName($plugin_definition_mapper->getPluginProvider($plugin_definition)),
      ];
    }

    return $build;
  }

}
