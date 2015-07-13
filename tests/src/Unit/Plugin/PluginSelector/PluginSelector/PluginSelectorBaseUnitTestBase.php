<?php

/**
 * @file
 * Contains \Drupal\Tests\plugin\Unit\Plugin\Plugin\Plugin\PluginSelectorBaseUnitTestBase.
 */

namespace Drupal\Tests\plugin\Unit\Plugin\PluginSelector\PluginSelector;

use Drupal\plugin\PluginType;
use Drupal\Tests\UnitTestCase;

/**
 * Provides a base for tests for classes that extend
 * \Drupal\plugin\Plugin\Plugin\PluginSelector\PluginSelectorBase.
 */
abstract class PluginSelectorBaseUnitTestBase extends UnitTestCase {

  /**
   * The plugin definition of the class under test.
   *
   * @var array
   */
  protected $pluginDefinition = [];

  /**
   * The plugin ID of the class plugin under test.
   *
   * @var array
   */
  protected $pluginId;

  /**
   * The plugin manager through which to select plugins.
   *
   * @var \Drupal\Component\Plugin\PluginManagerInterface|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $selectablePluginManager;

  /**
   * The plugin type of which to select plugins.
   *
   * @var \Drupal\plugin\PluginTypeInterface|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $selectablePluginType;

  /**
   * The selected plugin.
   *
   * @var \Drupal\Component\Plugin\PluginInspectionInterface|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $selectedPlugin;

  /**
   * The class under test.
   *
   * @var \Drupal\plugin\Plugin\Plugin\PluginSelector\PluginSelectorBase|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $sut;

  /**
   * {@inheritdoc}
   *
   */
  public function setUp() {
    $this->mapper = $this->getMock('\Drupal\plugin\Plugin\PluginDefinitionMapperInterface');

    $this->pluginId = $this->randomMachineName();

    $this->selectablePluginManager = $this->getMock('\Drupal\Component\Plugin\PluginManagerInterface');

    $plugin_type_definition = [
      'id' => $this->randomMachineName(),
      'label' => $this->randomMachineName(),
      'provider' => $this->randomMachineName(),
    ];
    $this->selectablePluginType = new PluginType($plugin_type_definition, $this->selectablePluginManager);

    $this->selectedPlugin = $this->getMock('\Drupal\Component\Plugin\PluginInspectionInterface');
  }

}
