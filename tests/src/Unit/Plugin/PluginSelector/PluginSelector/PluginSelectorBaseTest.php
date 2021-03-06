<?php

/**
 * @file
 * Contains \Drupal\Tests\plugin\Unit\Plugin\Plugin\Plugin\PluginSelectorBaseTest.
 */

namespace Drupal\Tests\plugin\Unit\Plugin\PluginSelector\PluginSelector;

use Drupal\Component\Plugin\Discovery\DiscoveryInterface;
use Drupal\Component\Plugin\Factory\FactoryInterface;
use Drupal\Component\Plugin\PluginInspectionInterface;
use Drupal\Core\Form\FormState;
use Drupal\plugin\Plugin\Plugin\PluginSelector\PluginSelectorBase;

/**
 * @coversDefaultClass \Drupal\plugin\Plugin\Plugin\PluginSelector\PluginSelectorBase
 *
 * @group Plugin
 */
class PluginSelectorBaseTest extends PluginSelectorBaseTestBase {

  /**
   * The class under test.
   *
   * @var \Drupal\plugin\Plugin\Plugin\PluginSelector\PluginSelectorBase|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $sut;

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();
    $configuration = [];
    $this->sut = $this->getMockBuilder(PluginSelectorBase::class)
      ->setConstructorArgs(array($configuration, $this->pluginId, $this->pluginDefinition))
      ->getMockForAbstractClass();
  }

  /**
   * @covers ::__construct
   */
  public function testConstruct() {
    $configuration = [];
    $this->sut = $this->getMockBuilder(PluginSelectorBase::class)
      ->setConstructorArgs(array($configuration, $this->pluginId, $this->pluginDefinition))
      ->getMockForAbstractClass();
  }

  /**
   * @covers ::defaultConfiguration
   */
  public function testDefaultConfiguration() {
    $configuration = $this->sut->defaultConfiguration();
    $this->assertInternalType('array', $configuration);
  }

  /**
   * @covers ::calculateDependencies
   */
  public function testCalculateDependencies() {
    $this->assertSame([], $this->sut->calculateDependencies());
  }

  /**
   * @covers ::setConfiguration
   * @covers ::getConfiguration
   */
  public function testGetConfiguration() {
    $configuration = array($this->randomMachineName());
    $this->assertSame($this->sut, $this->sut->setConfiguration($configuration));
    $this->assertSame($configuration, $this->sut->getConfiguration());
  }

  /**
   * @covers ::setLabel
   * @covers ::getLabel
   */
  public function testGetLabel() {
    $label = $this->randomMachineName();
    $this->assertSame($this->sut, $this->sut->setLabel($label));
    $this->assertSame($label, $this->sut->getLabel());
  }

  /**
   * @covers ::setDescription
   * @covers ::getDescription
   */
  public function testGetDescription() {
    $description = $this->randomMachineName();
    $this->assertSame($this->sut, $this->sut->setDescription($description));
    $this->assertSame($description, $this->sut->getDescription());
  }

  /**
   * @covers ::setCollectPluginConfiguration
   * @covers ::getCollectPluginConfiguration
   */
  public function testGetCollectPluginConfiguration() {
    $collect = (bool) mt_rand(0, 1);
    $this->assertSame($this->sut, $this->sut->setCollectPluginConfiguration($collect));
    $this->assertSame($collect, $this->sut->getCollectPluginConfiguration());
  }

  /**
   * @covers ::setPreviouslySelectedPlugins
   * @covers ::getPreviouslySelectedPlugins
   */
  public function testGetPreviouslySelectedPlugins() {
    $plugin = $this->getMock(PluginInspectionInterface::class);
    $this->sut->setPreviouslySelectedPlugins([$plugin]);
    $this->assertSame([$plugin], $this->sut->getPreviouslySelectedPlugins());
  }

  /**
   * @covers ::setKeepPreviouslySelectedPlugins
   * @covers ::getKeepPreviouslySelectedPlugins
   *
   * @depends testGetPreviouslySelectedPlugins
   */
  public function testGetKeepPreviouslySelectedPlugins() {
    $keep = (bool) mt_rand(0, 1);
    $plugin = $this->getMock(PluginInspectionInterface::class);
    $this->sut->setPreviouslySelectedPlugins([$plugin]);
    $this->assertSame($this->sut, $this->sut->setKeepPreviouslySelectedPlugins($keep));
    $this->assertSame($keep, $this->sut->getKeepPreviouslySelectedPlugins());

    // Confirm that all previously selected plugins are removed.
    $this->sut->setPreviouslySelectedPlugins([$plugin]);
    $this->sut->setKeepPreviouslySelectedPlugins(FALSE);
    $this->assertEmpty($this->sut->getPreviouslySelectedPlugins());
  }

  /**
   * @covers ::setSelectedPlugin
   * @covers ::getSelectedPlugin
   */
  public function testGetSelectedPlugin() {
    $plugin = $this->getMock(PluginInspectionInterface::class);
    $this->assertSame($this->sut, $this->sut->setSelectedPlugin($plugin));
    $this->assertSame($plugin, $this->sut->getSelectedPlugin());
  }

  /**
   * @covers ::setRequired
   * @covers ::isRequired
   */
  public function testGetRequired() {
    $this->assertFalse($this->sut->isRequired());
    $this->assertSame($this->sut, $this->sut->setRequired());
    $this->assertTrue($this->sut->isRequired());
    $this->sut->setRequired(FALSE);
    $this->assertFalse($this->sut->isRequired());
  }

  /**
   * @covers ::setSelectablePluginType
   * @covers ::setSelectablePluginDiscovery
   * @covers ::setSelectablePluginFactory
   */
  public function testSetSelectablePluginType() {
    $this->sut->setSelectablePluginType($this->selectablePluginType);

    $this->sut->setSelectablePluginDiscovery($this->getMock(DiscoveryInterface::class));

    $this->sut->setSelectablePluginFactory($this->getMock(FactoryInterface::class));
  }

  /**
   * @covers ::buildSelectorForm
   * @covers ::setSelectablePluginType
   */
  public function testBuildSelectorForm() {
    $this->sut->setSelectablePluginType($this->selectablePluginType);

    $form = [];
    $form_state = new FormState();

    $form = $this->sut->buildSelectorForm($form, $form_state);

    $this->assertInternalType('array', $form);
  }

}
