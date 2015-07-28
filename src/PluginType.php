<?php

/**
 * @file
 * Contains \Drupal\plugin\PluginType.
 */

namespace Drupal\plugin;

use Drupal\Component\Plugin\PluginManagerInterface;
use Drupal\Core\DependencyInjection\ClassResolverInterface;
use Drupal\Core\DependencyInjection\DependencySerializationTrait;
use Drupal\Core\StringTranslation\TranslationInterface;
use Drupal\Core\StringTranslation\TranslationWrapper;
use Drupal\plugin\Plugin\DefaultPluginDefinitionMapper;
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
   * @var \Drupal\Core\StringTranslation\TranslationWrapper|string
   */
  protected $label;

  /**
   * The human-readable description.
   *
   * @var \Drupal\Core\StringTranslation\TranslationWrapper|string|null
   */
  protected $description;

  /**
   * The operations provider..
   *
   * @var \Drupal\plugin\PluginTypeOperationsProviderInterface
   */
  protected $operationsProvider;

  /**
   * The plugin definition mapper.
   *
   * @var \Drupal\plugin\Plugin\PluginDefinitionMapperInterface
   */
  protected $pluginDefinitionMapper;

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
    $this->label = $definition['label'] = (new TranslationWrapper($definition['label']))->setStringTranslation($string_translation);
    $this->description = $definition['description'] = isset($definition['description']) ? (new TranslationWrapper($definition['description']))->setStringTranslation($string_translation) : NULL;
    if (array_key_exists('field_type', $definition)) {
      $this->fieldType = $definition['field_type'];
    }
    $plugin_definition_mapper_class = isset($definition['plugin_definition_mapper_class']) ? $definition['plugin_definition_mapper_class'] : DefaultPluginDefinitionMapper::class;
    $this->pluginDefinitionMapper = new $plugin_definition_mapper_class();
    $operations_provider_class = isset($definition['operations_provider_class']) ? $definition['operations_provider_class'] : DefaultPluginTypeOperationsProvider::class;
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
  public function getPluginDefinitionMapper() {
    return $this->pluginDefinitionMapper;
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
