<?php

/**
 * @file
 * Contains \Drupal\Tests\plugin\Unit\Plugin\PluginOperationsProviderPluginManagerTraitUnitTest.
 */

namespace Drupal\Tests\plugin\Unit\Plugin;

use Drupal\Core\DependencyInjection\ClassResolverInterface;
use Drupal\plugin\Plugin\PluginOperationsProviderPluginManagerTrait;
use Drupal\Tests\UnitTestCase;

/**
 * @coversDefaultClass \Drupal\plugin\Plugin\PluginOperationsProviderPluginManagerTrait
 *
 * @group Plugin
 */
class PluginOperationsProviderPluginManagerTraitUnitTest extends UnitTestCase {

  /**
   * The class resolver.
   *
   * @var \Drupal\Core\DependencyInjection\ClassResolverInterface|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $classResolver;

  /**
   * The trait under test.
   *
   * @var \Drupal\plugin\Plugin\PluginOperationsProviderPluginManagerTrait
   */
  public $trait;

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    $this->classResolver = $this->getMock('\Drupal\Core\DependencyInjection\ClassResolverInterface');
  }

  /**
   * @covers ::getOperationsProvider
   */
  public function testGetOperationsProvider() {
    $plugin_definitions = array(
      'foo' => array(
        'id' => 'foo',
        'operations_provider' => '\Drupal\Tests\plugin\Unit\Plugin\PluginOperationsProviderPluginManagerTraitUnitTestOperationsProvider',
      ),
      'bar' => array(
        'id' => 'bar',
      ),
    );

    $operations_provider = new \stdClass();

    $this->trait = new OperationsProviderPluginManagerTraitUnitTestPluginManager($this->classResolver, $plugin_definitions);

    $this->classResolver->expects($this->any())
      ->method('getInstanceFromDefinition')
      ->with($plugin_definitions['foo']['operations_provider'])
      ->will($this->returnValue($operations_provider));

    $this->assertSame($operations_provider, $this->trait->getOperationsProvider('foo'));
    $this->assertNull($this->trait->getOperationsProvider('bar'));
  }

}

class OperationsProviderPluginManagerTraitUnitTestPluginManager {

  use PluginOperationsProviderPluginManagerTrait;

  /**
   * The plugin definitions.
   *
   * @var array
   */
  protected $pluginDefinitions = [];

  /**
   * Creates a new class instance.
   */
  public function __construct(ClassResolverInterface $class_resolver, array $plugin_definitions) {
    $this->classResolver = $class_resolver;
    $this->pluginDefinitions = $plugin_definitions;
  }

  /**
   * Returns a plugin definition.
   */
  protected function getDefinition($plugin_id) {
    return $this->pluginDefinitions[$plugin_id];
  }
}
