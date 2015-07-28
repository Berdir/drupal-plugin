<?php

/**
 * @file
 * Contains \Drupal\Tests\plugin\Unit\Plugin\Plugin\Plugin\PluginSelectorBaseUnitTestBase.
 */

namespace Drupal\Tests\plugin\Unit\Plugin\PluginSelector\PluginSelector;

use Drupal\Component\Plugin\PluginInspectionInterface;
use Drupal\Component\Plugin\PluginManagerInterface;
use Drupal\Core\DependencyInjection\ClassResolverInterface;
use Drupal\plugin\Plugin\PluginDefinitionMapperInterface;
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
   * The plugin definition mapper.
   *
   * @var \Drupal\plugin\Plugin\PluginDefinitionMapperInterface
   */
  protected $pluginDefinitionMapper;

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
    $this->pluginDefinitionMapper = $this->getMock(PluginDefinitionMapperInterface::class);

    $this->pluginId = $this->randomMachineName();

    $class_resolver = $this->getMock(ClassResolverInterface::class);

    $this->selectablePluginManager = $this->getMock(PluginManagerInterface::class);

    $plugin_type_definition = [
      'id' => $this->randomMachineName(),
      'label' => $this->randomMachineName(),
      'provider' => $this->randomMachineName(),
    ];
    $this->selectablePluginType = new PluginType($plugin_type_definition, $this->getStringTranslationStub(), $class_resolver, $this->selectablePluginManager);

    $this->selectedPlugin = $this->getMock(PluginInspectionInterface::class);
  }

}
