<?php

/**
 * Contains \Drupal\plugin\Plugin\OperationsProviderPluginManagerTrait.
 */

namespace Drupal\plugin\Plugin;

/**
 * Implements \Drupal\plugin\PluginOperationsProviderProviderInterface for plugin managers.
 *
 * Classes using this trait MUST implement
 * \Drupal\Component\Plugin\Discovery\DiscoveryInterface and SHOULD implement
 * \Drupal\plugin\PluginOperationsProviderProviderInterface.
 */
trait PluginOperationsProviderPluginManagerTrait {

  /**
   * The class resolver.
   *
   * @var \Drupal\Core\DependencyInjection\ClassResolverInterface
   */
  protected $classResolver;

  /**
   * {@inheritdoc}
   */
  public function getOperationsProvider($plugin_id) {
    /** @var \Drupal\Component\Plugin\Discovery\DiscoveryInterface|\Drupal\plugin\Plugin\PluginOperationsProviderPluginManagerTrait $this */
    $definition = $this->getDefinition($plugin_id);
    if (isset($definition['operations_provider'])) {
      return $this->classResolver->getInstanceFromDefinition($definition['operations_provider']);
    }
    return NULL;
  }

}
