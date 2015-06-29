<?php

/**
 * @file
 * Contains \Drupal\Tests\plugin\Unit\PluginTypeTest.
 */

namespace Drupal\Tests\plugin\Unit;

use Drupal\plugin\PluginType;
use Drupal\Tests\UnitTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @coversDefaultClass \Drupal\plugin\PluginType
 *
 * @group Plugin
 */
class PluginTypeTest extends UnitTestCase {

  /**
   * The service container.
   *
   * @var \Symfony\Component\DependencyInjection\ContainerInterface|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $container;

  /**
   * The plugin manager.
   *
   * @var \Drupal\Component\Plugin\PluginManagerInterface|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $pluginManager;

  /**
   * The plugin type definition.
   *
   * @var mixed[]
   */
  protected $pluginTypeDefinition;

  /**
   * The class under test.
   *
   * @var \Drupal\plugin\PluginType
   */
  protected $sut;

  public function setUp() {
    $this->pluginTypeDefinition = [
      'id' => $this->randomMachineName(),
      'label' => $this->getRandomGenerator()->string(),
      'description' => $this->getRandomGenerator()->string(),
      'provider' => $this->randomMachineName(),
      'plugin_manager_service_id' => $this->randomMachineName(),
    ];

    $this->pluginManager = $this->getMock('\Drupal\Component\Plugin\PluginManagerInterface');

    $this->container = $this->getMock('\Symfony\Component\DependencyInjection\ContainerInterface');
    $map = [
      [$this->pluginTypeDefinition['plugin_manager_service_id'], ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE, $this->pluginManager],
    ];
    $this->container->expects($this->atLeastOnce())
      ->method('get')
      ->willReturnMap($map);

    $this->sut = PluginType::createFromDefinition($this->container, $this->pluginTypeDefinition);
  }

  /**
   * @covers ::createFromDefinition
   * @covers ::__construct
   */
  public function testCreateFromDefinition() {
    $this->sut = PluginType::createFromDefinition($this->container, $this->pluginTypeDefinition);
  }

  /**
   * @covers ::getId
   */
  public function testGetPluginId() {
    $this->assertSame($this->pluginTypeDefinition['id'], $this->sut->getId());
  }

  /**
   * @covers ::getLabel
   */
  public function testGetLabel() {
    $this->assertSame($this->pluginTypeDefinition['label'], $this->sut->getLabel()->getUntranslatedString());
  }

  /**
   * @covers ::getDescription
   */
  public function testGetDescription() {
    $this->assertSame($this->pluginTypeDefinition['description'], $this->sut->getDescription()->getUntranslatedString());
  }

  /**
   * @covers ::getProvider
   */
  public function testGetProvider() {
    $this->assertSame($this->pluginTypeDefinition['provider'], $this->sut->getProvider());
  }

  /**
   * @covers ::getPluginManager
   */
  public function testGetPluginManager() {
    $this->assertSame($this->pluginManager, $this->sut->getPluginManager());
  }

}
