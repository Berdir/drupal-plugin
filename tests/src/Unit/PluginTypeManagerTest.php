<?php

/**
 * @file
 * Contains \Drupal\Tests\plugin\Unit\PluginTypeManagerTest.
 */

namespace Drupal\Tests\plugin\Unit;

use Drupal\Component\FileCache\FileCacheFactory;
use Drupal\Component\Plugin\PluginManagerInterface;
use Drupal\plugin\PluginTypeManager;
use Drupal\Tests\UnitTestCase;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use org\bovigo\vfs\vfsStreamWrapper;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @coversDefaultClass \Drupal\plugin\PluginTypeManager
 *
 * @group Plugin
 */
class PluginTypeManagerTest extends UnitTestCase {

  /**
   * The service container.
   *
   * @var \Symfony\Component\DependencyInjection\ContainerInterface|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $container;

  /**
   * The module handler.
   *
   * @var \Drupal\Core\Extension\ModuleHandlerInterface|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $moduleHandler;

  /**
   * The plugin type's plugin managers.
   *
   * @var \Drupal\Component\Plugin\PluginManagerInterface[]
   *   Keys are plugin type IDs.
   */
  protected $pluginManagers = [];

  /**
   * The plugin type definitions.
   *
   * @var array[]
   */
  protected $pluginTypeDefinitions = [];

  /**
   * The class under test.
   *
   * @var \Drupal\plugin\PluginTypeManager
   */
  protected $sut;

  /**
   * Builds a plugin type definition file.
   *
   * @param string $id
   *
   * @return string
   */
  protected function buildPluginDefinitionYaml($id, $label, $description, $provider, $plugin_manager_service_id) {
    return <<<EOT
$id:
  label: "$label"
  description: "$description"
  provider: $provider
  plugin_manager_service_id: $plugin_manager_service_id
EOT;

  }

  public function setUp() {
    FileCacheFactory::setPrefix($this->randomMachineName());

    $plugin_type_id_a = $this->randomMachineName();
    $this->pluginTypeDefinitions[$plugin_type_id_a] = [
      'label' => $this->randomMachineName(),
      'description' => $this->randomMachineName(),
      'provider' => $this->randomMachineName(),
      'plugin_manager_service_id' => $this->randomMachineName(),
    ];
    $plugin_type_id_b = $this->randomMachineName();
    $this->pluginTypeDefinitions[$plugin_type_id_b] = [
      'label' => $this->randomMachineName(),
      'description' => $this->randomMachineName(),
      'provider' => $this->randomMachineName(),
      'plugin_manager_service_id' => $this->randomMachineName(),
    ];

    $this->pluginManagers = [
      $plugin_type_id_a => $this->getMock('\Drupal\Component\Plugin\PluginManagerInterface'),
      $plugin_type_id_b => $this->getMock('\Drupal\Component\Plugin\PluginManagerInterface'),
    ];

    vfsStreamWrapper::register();
    $root = new vfsStreamDirectory('modules');
    vfsStreamWrapper::setRoot($root);

    $this->moduleHandler = $this->getMock('Drupal\Core\Extension\ModuleHandlerInterface');
    $this->moduleHandler->expects($this->any())
      ->method('getModuleDirectories')
      ->willReturn(array(
        'module_a' => vfsStream::url('modules/module_a'),
        'module_b' => vfsStream::url('modules/module_b'),
      ));

    $this->container = $this->getMock('\Symfony\Component\DependencyInjection\ContainerInterface');
    $map = [
      ['string_translation', ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE, $this->getStringTranslationStub()],
      [$this->pluginTypeDefinitions[$plugin_type_id_a]['plugin_manager_service_id'], ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE, $this->pluginManagers[$plugin_type_id_a]],
      [$this->pluginTypeDefinitions[$plugin_type_id_b]['plugin_manager_service_id'], ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE, $this->pluginManagers[$plugin_type_id_b]],
    ];
    $this->container->expects($this->any())
      ->method('get')
      ->willReturnMap($map);

    $url = vfsStream::url('modules');
    mkdir($url . '/module_a');
    file_put_contents($url . '/module_a/module_a.plugin_type.yml', $this->buildPluginDefinitionYaml($plugin_type_id_a, $this->pluginTypeDefinitions[$plugin_type_id_a]['label'], $this->pluginTypeDefinitions[$plugin_type_id_a]['description'], $this->pluginTypeDefinitions[$plugin_type_id_a]['provider'], $this->pluginTypeDefinitions[$plugin_type_id_a]['plugin_manager_service_id']));
    mkdir($url . '/module_b');
    file_put_contents($url . '/module_b/module_b.plugin_type.yml', $this->buildPluginDefinitionYaml($plugin_type_id_b, $this->pluginTypeDefinitions[$plugin_type_id_b]['label'], $this->pluginTypeDefinitions[$plugin_type_id_b]['description'], $this->pluginTypeDefinitions[$plugin_type_id_b]['provider'], $this->pluginTypeDefinitions[$plugin_type_id_b]['plugin_manager_service_id']));

    $this->sut = new PluginTypeManager($this->container, $this->moduleHandler);
  }

  /**
   * @covers ::__construct
   */
  public function testConstruct() {
    $this->sut = new PluginTypeManager($this->container, $this->moduleHandler);
  }

  /**
   * @covers ::hasPluginType
   */
  public function testHasPluginType() {
    foreach ($this->pluginTypeDefinitions as $plugin_type_id => $plugin_type_definition) {
      $this->assertTrue($this->sut->hasPluginType($plugin_type_id));
    }
  }

  /**
   * @covers ::getPluginType
   */
  public function testGetPluginType() {
    foreach ($this->pluginTypeDefinitions as $plugin_type_id => $plugin_type_definition) {
      $plugin_type = $this->sut->getPluginType($plugin_type_id);
      $this->assertPluginTypeIntegrity($plugin_type_id, $plugin_type_definition, $this->pluginManagers[$plugin_type_id], $plugin_type);
    }
  }

  /**
   * @covers ::getPluginType
   *
   * @expectedException \InvalidArgumentException
   */
  public function testGetPluginTypeWithInvalidPluginTypeId() {
    $this->sut->getPluginType($this->randomMachineName());
  }

  /**
   * @covers ::getPluginTypes
   */
  public function testGetPluginTypes() {
    foreach ($this->sut->getPluginTypes() as $plugin_type) {
      $this->assertPluginTypeIntegrity($plugin_type->getId(), $this->pluginTypeDefinitions[$plugin_type->getId()], $this->pluginManagers[$plugin_type->getId()], $plugin_type);
    }
  }

  /**
   * Asserts the integrity of a plugin type based on its definition.
   *
   * @param string $plugin_type_id
   * @param mixed[] $plugin_type_definition
   * @param \Drupal\Component\Plugin\PluginManagerInterface $plugin_manager
   * @param mixed $plugin_type
   */
  protected function assertPluginTypeIntegrity($plugin_type_id, $plugin_type_definition, PluginManagerInterface $plugin_manager, $plugin_type) {
    $this->assertInstanceOf('\Drupal\plugin\PluginTypeInterface', $plugin_type);
    $this->assertSame($plugin_type_id, $plugin_type->getId());
    $this->assertSame($plugin_type_definition['label'], $plugin_type->getLabel()->getUntranslatedString());
    $this->assertSame($plugin_type_definition['description'], $plugin_type->getDescription()->getUntranslatedString());
    $this->assertSame($plugin_type_definition['provider'], $plugin_type->getProvider());
    $this->assertSame($plugin_manager, $plugin_type->getPluginManager());
  }

}
