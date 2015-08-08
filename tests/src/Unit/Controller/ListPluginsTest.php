<?php

/**
 * @file
 * Contains \Drupal\Tests\plugin\Unit\Controller\ListPluginsTest.
 */

namespace Drupal\Tests\plugin\Unit\Controller;

use Drupal\Component\Plugin\PluginManagerInterface;
use Drupal\Core\DependencyInjection\ClassResolverInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\plugin\Controller\ListPlugins;
use Drupal\plugin\PluginDefinition\PluginDescriptionDefinitionInterface;
use Drupal\plugin\PluginDefinition\PluginLabelDefinitionInterface;
use Drupal\plugin\PluginType;
use Drupal\plugin\PluginTypeInterface;
use Drupal\plugin\PluginTypeManagerInterface;
use Drupal\Tests\UnitTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @coversDefaultClass \Drupal\plugin\Controller\ListPlugins
 *
 * @group Plugin
 */
class ListPluginsTest extends UnitTestCase {

  /**
   * The class under test.
   *
   * @var \Drupal\plugin\Controller\ListPlugins
   */
  protected $sut;

  /**
   * The current user.
   *
   * @var \Drupal\Core\Session\AccountInterface|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $currentUser;

  /**
   * The module handler.
   *
   * @var \Drupal\Core\Session\AccountInterface|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $moduleHandler;

  /**
   * The plugin type manager.
   *
   * @var \Drupal\plugin\PluginTypeManagerInterface|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $pluginTypeManager;

  /**
   * The string translator.
   *
   * @var \Drupal\Core\StringTranslation\TranslationInterface
   */
  protected $stringTranslation;

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    $this->moduleHandler = $this->getMock(ModuleHandlerInterface::class);

    $this->pluginTypeManager = $this->getMock(PluginTypeManagerInterface::class);

    $this->stringTranslation = $this->getStringTranslationStub();

    $this->sut = new ListPlugins($this->stringTranslation, $this->moduleHandler, $this->pluginTypeManager);
  }

  /**
   * @covers ::create
   * @covers ::__construct
   */
  function testCreate() {
    $container = $this->getMock(ContainerInterface::class);
    $map = [
      ['module_handler', ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE, $this->moduleHandler],
      ['plugin.plugin_type_manager', ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE, $this->pluginTypeManager],
      ['string_translation', ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE, $this->stringTranslation],
    ];
    $container->expects($this->any())
      ->method('get')
      ->willReturnMap($map);

    $sut = ListPlugins::create($container);
    $this->assertInstanceOf(ListPlugins::class, $sut);
  }

  /**
   * @covers ::title
   */
  public function testTitle() {
    $class_resolver = $this->getMock(ClassResolverInterface::class);

    $plugin_manager = $this->getMock(PluginManagerInterface::class);

    $plugin_type_id = $this->randomMachineName();
    $plugin_type_label = $this->randomMachineName();

    $plugin_type_definition = [
      'id' => $plugin_type_id,
      'label' => $plugin_type_label,
      'provider' => $this->randomMachineName(),
    ];
    $plugin_type = new PluginType($plugin_type_definition, $this->stringTranslation, $class_resolver, $plugin_manager);

    $this->pluginTypeManager->expects($this->atLeastOnce())
      ->method('getPluginType')
      ->with($plugin_type_id)
      ->willReturn($plugin_type);

    $title = $this->sut->title($plugin_type_id);
    $this->assertContains($plugin_type_label, $title);
  }

  /**
   * @covers ::execute
   */
  public function testExecute() {
    $plugin_manager = $this->getMock(PluginManagerInterface::class);

    $plugin_definition_id_a = $this->randomMachineName();
    $plugin_definition_label_a = $this->randomMachineName();
    $plugin_definition_a = $this->getMock(PluginLabelDefinitionInterface::class);
    $plugin_definition_a->expects($this->atLeastOnce())
      ->method('getId')
      ->willReturn($plugin_definition_id_a);
    $plugin_definition_a->expects($this->atLeastOnce())
      ->method('getLabel')
      ->willReturn($plugin_definition_label_a);
    $plugin_definition_id_b = $this->randomMachineName();
    $plugin_definition_description_b = $this->randomMachineName();
    $plugin_definition_b = $this->getMock(PluginDescriptionDefinitionInterface::class);
    $plugin_definition_b->expects($this->atLeastOnce())
      ->method('getId')
      ->willReturn($plugin_definition_id_b);
    $plugin_definition_b->expects($this->atLeastOnce())
      ->method('getDescription')
      ->willReturn($plugin_definition_description_b);

    $plugin_definitions = [
      $plugin_definition_id_a => $plugin_definition_a,
      $plugin_definition_id_b => $plugin_definition_b,
    ];

    $plugin_manager->expects($this->atLeastOnce())
      ->method('getDefinitions')
      ->willReturn($plugin_definitions);

    $plugin_type_id = $this->randomMachineName();

    $plugin_type = $this->getMock(PluginTypeInterface::class);
    $plugin_type->expects($this->atLeastOnce())
      ->method('ensureTypedPluginDefinition')
      ->willReturnArgument(0);
    $plugin_type->expects($this->atLeastOnce())
      ->method('getPluginManager')
      ->willReturn($plugin_manager);

    $this->pluginTypeManager->expects($this->atLeastOnce())
      ->method('getPluginType')
      ->with($plugin_type_id)
      ->willReturn($plugin_type);
    $this->pluginTypeManager->expects($this->atLeastOnce())
      ->method('hasPluginType')
      ->with($plugin_type_id)
      ->willReturn(TRUE);

    $build = $this->sut->execute($plugin_type_id);

    $this->assertSame($plugin_definition_label_a, (string) $build[$plugin_definition_id_a]['label']['#markup']);
    $this->assertNull($build[$plugin_definition_id_a]['description']['#markup']);
    $this->assertSame($plugin_definition_id_b, (string) $build[$plugin_definition_id_b]['label']['#markup']);
    $this->assertSame($plugin_definition_description_b, (string) $build[$plugin_definition_id_b]['description']['#markup']);
  }

}
