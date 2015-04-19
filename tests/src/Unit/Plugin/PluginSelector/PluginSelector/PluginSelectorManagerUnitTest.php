<?php

/**
 * @file
 * Contains \Drupal\Tests\plugin_selector\Unit\Plugin\PluginSelector\PluginSelector\PluginSelectorManagerUnitTest.
 */

namespace Drupal\Tests\plugin_selector\Unit\Plugin\PluginSelector\PluginSelector;

use Drupal\plugin_selector\Plugin\PluginSelector\PluginSelector\PluginSelectorManager;
use Drupal\Tests\UnitTestCase;
use Zend\Stdlib\ArrayObject;

/**
 * @coversDefaultClass \Drupal\plugin_selector\Plugin\PluginSelector\PluginSelector\PluginSelectorManager
 *
 * @group Plugin Selector
 */
class PluginSelectorManagerUnitTest extends UnitTestCase {

  /**
   * The cache backend used for testing.
   *
   * @var \Drupal\Core\Cache\CacheBackendInterface|\PHPUnit_Framework_MockObject_MockObject
   */
  public $cache;

  /**
   * The plugin discovery used for testing.
   *
   * @var \Drupal\Component\Plugin\Discovery\DiscoveryInterface|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $discovery;

  /**
   * The plugin factory used for testing.
   *
   * @var \Drupal\Component\Plugin\Factory\FactoryInterface|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $factory;

  /**
   * The module handler used for testing.
   *
   * @var \Drupal\Core\Extension\ModuleHandlerInterface|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $moduleHandler;

  /**
   * The class under test.
   *
   * @var \Drupal\plugin_selector\Plugin\PluginSelector\PluginSelector\PluginSelectorManager
   */
  public $sut;

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    $this->discovery = $this->getMock('\Drupal\Component\Plugin\Discovery\DiscoveryInterface');

    $this->factory = $this->getMock('\Drupal\Component\Plugin\Factory\FactoryInterface');

    $this->moduleHandler = $this->getMock('\Drupal\Core\Extension\ModuleHandlerInterface');

    $this->cache = $this->getMock('\Drupal\Core\Cache\CacheBackendInterface');

    $namespaces = new ArrayObject();

    $this->sut = new PluginSelectorManager($namespaces, $this->cache, $this->moduleHandler);
    $property = new \ReflectionProperty($this->sut, 'discovery');
    $property->setAccessible(TRUE);
    $property->setValue($this->sut, $this->discovery);
    $property = new \ReflectionProperty($this->sut, 'factory');
    $property->setAccessible(TRUE);
    $property->setValue($this->sut, $this->factory);
  }

  /**
   * @covers ::__construct
   */
  public function testConstruct() {
    $namespaces = new ArrayObject();
    $this->sut = new PluginSelectorManager($namespaces, $this->cache, $this->moduleHandler);
  }

  /**
   * @covers ::getFallbackPluginId
   */
  public function testGetFallbackPluginId() {
    $plugin_id = $this->randomMachineName();
    $plugin_configuration = array($this->randomMachineName());
    $this->assertInternalType('string', $this->sut->getFallbackPluginId($plugin_id, $plugin_configuration));
  }

  /**
   * @covers ::getDefinitions
   */
  public function testGetDefinitions() {
    $definitions = array(
      'foo' => array(
        'label' => $this->randomMachineName(),
      ),
    );
    $this->discovery->expects($this->once())
      ->method('getDefinitions')
      ->will($this->returnValue($definitions));
    $this->moduleHandler->expects($this->once())
      ->method('alter')
      ->with('plugin_selector_plugin_selector');
    $this->assertSame($definitions, $this->sut->getDefinitions());
  }

}
