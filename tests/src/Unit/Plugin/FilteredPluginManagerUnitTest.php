<?php

/**
 * @file
 * Contains \Drupal\Tests\plugin_selector\Unit\Plugin\FilteredPluginManagerUnitTest.
 */

namespace Drupal\Tests\plugin_selector\Unit\Plugin;

use Drupal\Component\Plugin\Discovery\CachedDiscoveryInterface;
use Drupal\Component\Plugin\PluginManagerInterface;
use Drupal\plugin_selector\Plugin\FilteredPluginManager;
use Drupal\Tests\UnitTestCase;

/**
 * @coversDefaultClass \Drupal\plugin_selector\Plugin\FilteredPluginManager
 */
class FilteredPluginManagerUnitTest extends UnitTestCase {

  /**
   * The plugin definition mapper.
   *
   * @var \Drupal\plugin_selector\Plugin\PluginDefinitionMapperInterface|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $pluginDefinitionMapper;

  /**
   * The original plugin manager.
   *
   * @var \Drupal\Component\Plugin\PluginManagerInterface|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $pluginManager;

  /**
   * The class under test.
   *
   * @var \Drupal\plugin_selector\Plugin\FilteredPluginManager
   */
  protected $sut;

  public function setUp() {
    $this->pluginDefinitionMapper = $this->getMock('\Drupal\plugin_selector\Plugin\PluginDefinitionMapperInterface');

    $this->pluginManager = $this->getMock('\Drupal\Component\Plugin\PluginManagerInterface');

    $this->sut = new FilteredPluginManager($this->pluginManager, $this->pluginDefinitionMapper);
  }

  /**
   * @covers ::__construct
   */
  public function testConstruct() {
    $this->sut = new FilteredPluginManager($this->pluginManager, $this->pluginDefinitionMapper);
  }

  /**
   * @covers ::getInstance
   *
   * @expectedException \BadMethodCallException
   */
  public function testGetInstance() {
    $this->sut->getInstance([]);
  }

  /**
   * @covers ::getDefinitions
   * @covers ::filterDefinition
   * @covers ::setPluginIdFilter
   * @covers ::resetPluginIdFilter
   */
  public function testGetDefinitions() {
    $plugin_id_a = $this->randomMachineName();
    $plugin_definition_a = [
      'id' => $plugin_id_a,
    ];
    $plugin_id_b = $this->randomMachineName();
    $plugin_definition_b = [
      'id' => $plugin_id_b,
    ];
    $plugin_id_c = $this->randomMachineName();
    $plugin_definition_c = [
      'id' => $plugin_id_c,
    ];

    $plugin_definitions = [
      $plugin_id_a => $plugin_definition_a,
      $plugin_id_b => $plugin_definition_b,
      $plugin_id_c => $plugin_definition_c,
    ];

    $this->pluginManager->expects($this->atLeastOnce())
      ->method('getDefinitions')
      ->willReturn($plugin_definitions);

    $map = [
      [$plugin_definition_a, $plugin_id_a],
      [$plugin_definition_b, $plugin_id_b],
      [$plugin_definition_c, $plugin_id_c],
    ];
    $this->pluginDefinitionMapper->expects($this->atLeastOnce())
      ->method('getPluginId')
      ->willReturnMap($map);

    $this->sut->setPluginIdFilter([$plugin_id_a, $plugin_id_c]);

    $expected_plugin_definitions = [
      $plugin_id_a => $plugin_definition_a,
      $plugin_id_c => $plugin_definition_c,
    ];
    $this->assertEquals($expected_plugin_definitions, $this->sut->getDefinitions());

    $this->sut->resetPluginIdFilter();

    $this->assertEquals($plugin_definitions, $this->sut->getDefinitions());
  }

  /**
   * @covers ::createInstance
   */
  public function testCreateInstance() {
    $plugin_id_a = $this->randomMachineName();
    $plugin_definition_a = [
      'id' => $plugin_id_a,
    ];
    $plugin_a = $this->getMock('\Drupal\Component\Plugin\PluginInspectionInterface');
    $plugin_id_b = $this->randomMachineName();
    $plugin_definition_b = [
      'id' => $plugin_id_b,
    ];

    $plugin_definitions = [
      $plugin_id_a => $plugin_definition_a,
      $plugin_id_b => $plugin_definition_b,
    ];

    $this->pluginManager->expects($this->atLeastOnce())
      ->method('getDefinitions')
      ->willReturn($plugin_definitions);

    $map = [
      [$plugin_definition_a, $plugin_id_a],
      [$plugin_definition_b, $plugin_id_b],
    ];
    $this->pluginDefinitionMapper->expects($this->atLeastOnce())
      ->method('getPluginId')
      ->willReturnMap($map);

    $this->sut->setPluginIdFilter([$plugin_id_a]);

    $this->pluginManager->expects($this->once())
      ->method('createInstance')
      ->with($plugin_id_a)
      ->willReturn($plugin_a);

    $this->assertSame($plugin_a, $this->sut->createInstance($plugin_id_a));

    $this->setExpectedException('\Drupal\Component\Plugin\Exception\PluginNotFoundException');

    $this->sut->createInstance($plugin_id_b);
  }

  /**
   * @covers ::clearCachedDefinitions
   */
  public function testClearCachedDefinitionsWithUncachedDiscoveryPluginManager() {
    $this->pluginManager->expects($this->never())
      ->method('clearCachedDefinitions');
    $this->pluginManager->expects($this->exactly(2))
      ->method('getDefinitions')
      ->willReturn([]);

    // There are no cached definitions yet.
    $this->sut->getDefinitions();
    // This should return the cached definitions.
    $this->sut->getDefinitions();

    $this->sut->clearCachedDefinitions();
    // This should return newly built definitions.
    $this->sut->getDefinitions();
  }

  /**
   * @covers ::clearCachedDefinitions
   */
  public function testClearCachedDefinitionsWithCachedDiscoveryPluginManager() {
    $this->pluginManager = $this->getMockForAbstractClass('\Drupal\Tests\plugin_selector\Unit\Plugin\FilteredPluginManagerUnitTestCachedDiscoveryPluginManager');

    $this->sut = new FilteredPluginManager($this->pluginManager, $this->pluginDefinitionMapper);

    $this->pluginManager->expects($this->once())
      ->method('clearCachedDefinitions');
    $this->pluginManager->expects($this->exactly(2))
      ->method('getDefinitions')
      ->willReturn([]);

    // There are no cached definitions yet.
    $this->sut->getDefinitions();
    // This should return the cached definitions.
    $this->sut->getDefinitions();

    $this->sut->clearCachedDefinitions();
    // This should return newly built definitions.
    $this->sut->getDefinitions();
  }

  /**
   * @covers ::useCaches
   */
  public function testUseCachesWithCachedDiscoveryPluginManager() {
    $this->pluginManager = $this->getMockForAbstractClass('\Drupal\Tests\plugin_selector\Unit\Plugin\FilteredPluginManagerUnitTestCachedDiscoveryPluginManager');

    $this->sut = new FilteredPluginManager($this->pluginManager, $this->pluginDefinitionMapper);

    $this->pluginManager->expects($this->once())
      ->method('clearCachedDefinitions');
    $this->pluginManager->expects($this->exactly(3))
      ->method('getDefinitions')
      ->willReturn([]);

    // There are no cached definitions yet.
    $this->sut->getDefinitions();
    // This should return the cached definitions.
    $this->sut->getDefinitions();

    $this->sut->useCaches(FALSE);
    // This should return newly built definitions.
    $this->sut->getDefinitions();
    // This should return newly built definitions again, because we disabled
    // caching.
    $this->sut->getDefinitions();
  }

}

/**
 * Provides a dummy plugin manager that caches definitions.
 */
abstract class FilteredPluginManagerUnitTestCachedDiscoveryPluginManager implements PluginManagerInterface, CachedDiscoveryInterface {
}
