<?php

/**
 * @file
 * Contains
 * \Drupal\Tests\plugin\Unit\Plugin\Plugin\Plugin\RadiosUnitTest.
 */

namespace Drupal\Tests\plugin\Unit\Plugin\PluginSelector\PluginSelector;

use Drupal\plugin\Plugin\Plugin\PluginSelector\Radios;

/**
 * @coversDefaultClass \Drupal\plugin\Plugin\Plugin\PluginSelector\Radios
 *
 * @group Plugin
 */
class RadiosUnitTest extends PluginSelectorBaseUnitTestBase {

  /**
   * The class under test.
   *
   * @var \Drupal\plugin\Plugin\Plugin\PluginSelector\Radios
   */
  protected $sut;

  /**
   * The response policy.
   *
   * @var \Drupal\Core\PageCache\ResponsePolicyInterface|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $responsePolicy;

  /**
   * The string translation service.
   *
   * @var \Drupal\Core\StringTranslation\TranslationInterface|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $stringTranslation;

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();

    $this->responsePolicy = $this->getMockBuilder('\Drupal\Core\PageCache\ResponsePolicy\KillSwitch')
      ->disableOriginalConstructor()
      ->getMock();

    $this->stringTranslation = $this->getStringTranslationStub();

    $this->sut = new Radios([], $this->pluginId, $this->pluginDefinition, $this->stringTranslation, $this->responsePolicy);
    $this->sut->setSelectablePluginType($this->selectablePluginType);
  }

  /**
   * @covers ::buildSelectorForm
   */
  public function testBuildSelectorFormWithoutAvailablePlugins() {
    $form = [];
    $form_state = $this->getMock('\Drupal\Core\Form\FormStateInterface');

    $this->selectablePluginManager->expects($this->any())
      ->method('getDefinitions')
      ->will($this->returnValue([]));

    $build = $this->sut->buildSelectorForm($form, $form_state);

    $this->assertArrayHasKey('clear', $build);
  }

  /**
   * @covers ::buildSelector
   */
  public function testBuildSelector() {
    $this->stringTranslation->expects($this->any())
      ->method('translate')
      ->will($this->returnArgument(0));

    $method = new \ReflectionMethod($this->sut, 'buildSelector');
    $method->setAccessible(TRUE);

    $plugin_id = $this->randomMachineName();
    $plugin_label = $this->randomMachineName();
    $plugin_definition = [
      'id' => $plugin_id,
      'label' => $plugin_label,
    ];
    $plugin = $this->getMock('\Drupal\Component\Plugin\PluginInspectionInterface');
    $plugin->expects($this->atLeastOnce())
      ->method('getPluginDefinition')
      ->willReturn($plugin_definition);
    $plugin->expects($this->atLeastOnce())
      ->method('getPluginId')
      ->will($this->returnValue($plugin_id));

    $this->mapper->expects($this->any())
      ->method('getPluginLabel')
      ->willReturn($plugin_label);

    $this->sut->setSelectedPlugin($plugin);
    $selector_title = $this->randomMachineName();
    $this->sut->setLabel($selector_title);
    $selector_description = $this->randomMachineName();
    $this->sut->setDescription($selector_description);

    $element = array(
      '#parents' => array('foo', 'bar'),
      '#title' => $selector_title,
    );
    $form_state = $this->getMock('\Drupal\Core\Form\FormStateInterface');
    $available_plugins = array($plugin);

    $expected_build_plugin_id = array(
      '#ajax' => array(
        'callback' => array('Drupal\plugin\Plugin\Plugin\PluginSelector\Radios', 'ajaxRebuildForm'),
        'effect' => 'fade',
        'event' => 'change',
        'progress' => 'none',
        'trigger_as' => array(
          'name' => 'foo[bar][select][container][change]',
        ),
      ),
      '#attached' => [
        'library' => ['plugin/plugin_selector.plugin_radios'],
      ],
      '#default_value' => $plugin_id,
      '#empty_value' => 'select',
      '#options' => array(
        $plugin_id => $plugin_label,
      ) ,
      '#required' => FALSE,
      '#title' => $selector_title,
      '#description' => $selector_description,
      '#type' => 'radios',
    );
    $expected_build_change = array(
      '#ajax' => array(
        'callback' => array('Drupal\plugin\Plugin\Plugin\PluginSelector\AdvancedPluginSelectorBase', 'ajaxRebuildForm'),
      ),
      '#attributes' => array(
        'class' => array('js-hide')
      ),
      '#limit_validation_errors' => array(array('foo', 'bar', 'select', 'plugin_id')),
      '#name' => 'foo[bar][select][container][change]',
      '#submit' => [['Drupal\plugin\Plugin\Plugin\PluginSelector\AdvancedPluginSelectorBase', 'rebuildForm']],
      '#type' => 'submit',
      '#value' => 'Choose',
    );
    $build = $method->invokeArgs($this->sut, array($element, $form_state, $available_plugins));
    $this->assertEquals($expected_build_plugin_id, $build['container']['plugin_id']);
    $this->assertEquals($expected_build_change, $build['container']['change']);
    $this->assertSame('container', $build['container']['#type']);
  }

}
