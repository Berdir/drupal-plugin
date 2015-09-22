<?php

/**
 * @file
 * Contains \Drupal\plugin\PluginType.
 */

namespace Drupal\plugin;

use Drupal\Component\Plugin\PluginManagerInterface;
use Drupal\Core\DependencyInjection\ClassResolverInterface;
use Drupal\Core\DependencyInjection\DependencySerializationTrait;
use Drupal\Core\StringTranslation\TranslatableString;
use Drupal\Core\StringTranslation\TranslationInterface;
use Drupal\plugin\PluginDefinition\PluginDefinitionInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a plugin type.
 */
class PluginType implements PluginTypeInterface {

  use DependencySerializationTrait;

  /**
   * The ID.
   *
   * @var string
   */
  protected $id;

  /**
   * Whether this plugin type can be used as a field type.
   *
   * @var bool
   */
  protected $fieldType = TRUE;

  /**
   * The human-readable label.
   *
   * @var \Drupal\Core\StringTranslation\TranslatableString|string
   */
  protected $label;

  /**
   * The human-readable description.
   *
   * @var \Drupal\Core\StringTranslation\TranslatableString|string|null
   */
  protected $description;

  /**
   * The operations provider..
   *
   * @var \Drupal\plugin\PluginTypeOperationsProviderInterface
   */
  protected $operationsProvider;

  /**
   * The plugin definition decorator class.
   *
   * @var string|null
   *   A class that implements
   *   \Drupal\plugin\PluginDefinition\PluginDefinitionDecoratorInterface or
   *   NULL if definitions of plugins of this type do not have to be decorated
   *   (e.g. already implement
   *   \Drupal\plugin\PluginDefinition\PluginDefinitionInterface).
   */
  protected $pluginDefinitionDecoratorClass;

  /**
   * The plugin type provider.
   *
   * @var string
   *   The provider is the machine name of the module that provides the plugin
   *   type.
   */
  protected $provider;

  /**
   * The plugin manager.
   *
   * @var \Drupal\Component\Plugin\PluginManagerInterface
   */
  protected $pluginManager;

  /**
   * Constructs a new instance.
   *
   * @param mixed[] $definition
   *   The plugin type definition.
   * @param \Drupal\Core\StringTranslation\TranslationInterface $string_translation
   *   The string translator.
   * @param \Drupal\Core\DependencyInjection\ClassResolverInterface $class_resolver
   *   The class resolver.
   * @param \Drupal\Component\Plugin\PluginManagerInterface $plugin_manager
   *   The plugin type's plugin manager.
   *
   * @param mixed[] $definition
   */
  public function __construct(array $definition, TranslationInterface $string_translation, ClassResolverInterface $class_resolver, PluginManagerInterface $plugin_manager) {
    $this->id = $definition['id'];
    $this->label = $definition['label'] = new TranslatableString($definition['label'], [], [], $string_translation);
    $this->description = $definition['description'] = isset($definition['description']) ? new TranslatableString($definition['description'], [], [], $string_translation) : NULL;
    if (array_key_exists('field_type', $definition)) {
      $this->fieldType = $definition['field_type'];
    }
    if (array_key_exists('plugin_definition_decorator_class', $definition)) {
      $this->pluginDefinitionDecoratorClass = $definition['plugin_definition_decorator_class'];
    }
    $operations_provider_class = array_key_exists('operations_provider_class', $definition) ? $definition['operations_provider_class'] : DefaultPluginTypeOperationsProvider::class;
    $this->operationsProvider = $class_resolver->getInstanceFromDefinition($operations_provider_class);
    $this->pluginManager = $plugin_manager;
    $this->provider = $definition['provider'];
  }

  /**
   * {@inheritdoc}
   */
  public static function createFromDefinition(ContainerInterface $container, array $definition) {
    return new static($definition, $container->get('string_translation'), $container->get('class_resolver'), $container->get($definition['plugin_manager_service_id']));
  }

  /**
   * {@inheritdoc}
   */
  public function getId() {
    return $this->id;
  }

  /**
   * {@inheritdoc}
   */
  public function getLabel() {
    return $this->label;
  }

  /**
   * {@inheritdoc}
   */
  public function getDescription() {
    return $this->description;
  }

  /**
   * {@inheritdoc}
   */
  public function getProvider() {
    return $this->provider;
  }

  /**
   * {@inheritdoc}
   */
  public function getPluginManager() {
    return $this->pluginManager;
  }

  /**
   * {@inheritdoc}
   */
  public function ensureTypedPluginDefinition($plugin_definition) {
    if ($this->pluginDefinitionDecoratorClass && !($plugin_definition instanceof $this->pluginDefinitionDecoratorClass)) {
      $plugin_definition_decorator_class = $this->pluginDefinitionDecoratorClass;
      return $plugin_definition_decorator_class::createFromDecoratedDefinition($plugin_definition);
    }
    elseif ($plugin_definition instanceof PluginDefinitionInterface) {
      return $plugin_definition;
    }
    else {
      throw new \Exception(sprintf('A plugin definition of plugin type %s does not implement required %s, but its type also does not specify a plugin definition decorator.', $this->getId(), PluginDefinitionInterface::class));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getOperationsProvider() {
    return $this->operationsProvider;
  }

  /**
   * {@inheritdoc}
   */
  public function isFieldType() {
    return $this->fieldType;
  }

}
