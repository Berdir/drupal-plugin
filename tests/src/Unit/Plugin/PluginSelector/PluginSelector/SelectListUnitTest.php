<?php

/**
 * @file
 * Contains
 * \Drupal\Tests\plugin\Unit\Plugin\Plugin\Plugin\SelectListUnitTest.
 */

namespace Drupal\Tests\plugin\Unit\Plugin\PluginSelector\PluginSelector;

use Drupal\plugin\Plugin\Plugin\PluginSelector\SelectList;

/**
 * @coversDefaultClass \Drupal\plugin\Plugin\Plugin\PluginSelector\SelectList
 *
 * @group Plugin
 */
class SelectListUnitTest extends PluginSelectorBaseUnitTestBase {

  /**
   * The class under test.
   *
   * @var \Drupal\plugin\Plugin\Plugin\PluginSelector\SelectList
   */
  protected $sut;

  /**
   * The response policy.
   *
   * @var \Drupal\Core\PageCache\ResponsePolicyInterface|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $responsePolicy;

  /**
   * The string translator.
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

    $this->sut = new SelectList([], $this->pluginId, $this->pluginDefinition, $this->stringTranslation, $this->responsePolicy);
    $this->sut->setSelectablePluginType($this->selectablePluginType);
  }

  /**
   * @covers ::buildSelector
   * @covers ::buildHierarchy
   * @covers ::buildHierarchyLevel
   * @covers ::buildOptionsLevel
   * @covers ::sort
   */
  public function testBuildSelector() {
    $this->stringTranslation->expects($this->any())
      ->method('translate')
      ->will($this->returnArgument(0));

    $method = new \ReflectionMethod($this->sut, 'buildSelector');
    $method->setAccessible(TRUE);

    $plugin_id_a = $this->randomMachineName();
    $plugin_label_a = $this->randomMachineName();
    $plugin_definition_a = [
      'id' => $plugin_id_a,
      'label' => $plugin_label_a,
    ];
    $plugin_a = $this->getMock('\Drupal\Component\Plugin\PluginInspectionInterface');
    $plugin_a->expects($this->atLeastOnce())
      ->method('getPluginId')
      ->willReturn($plugin_id_a);
    $plugin_id_b = $this->randomMachineName();
    $plugin_label_b = $this->randomMachineName();
    $plugin_definition_b = [
      'id' => $plugin_id_b,
      'label' => $plugin_label_b,
    ];
    $plugin_b = $this->getMock('\Drupal\Component\Plugin\PluginInspectionInterface');

    $this->sut->setSelectedPlugin($plugin_a);
    $selector_title = $this->randomMachineName();
    $this->sut->setLabel($selector_title);
    $selector_description = $this->randomMachineName();
    $this->sut->setDescription($selector_description);

    $element = array(
      '#parents' => array('foo', 'bar'),
    );
    $form_state = $this->getMock('\Drupal\Core\Form\FormStateInterface');
    $available_plugins = [$plugin_a, $plugin_b];

    $this->selectablePluginManager->expects($this->atLeastOnce())
      ->method('getDefinitions')
      ->willReturn([
        $plugin_id_a => $plugin_definition_a,
        $plugin_id_b => $plugin_definition_b,
      ]);

    $expected_build_plugin_id = array(
      '#ajax' => array(
        'callback' => array('Drupal\plugin\Plugin\Plugin\PluginSelector\SelectList', 'ajaxRebuildForm'),
        'effect' => 'fade',
        'event' => 'change',
        'trigger_as' => array(
          'name' => 'foo[bar][select][container][change]',
        ),
      ),
      '#default_value' => $plugin_id_a,
      '#empty_value' => '',
      '#options' => array(
        $plugin_id_a => $plugin_label_a,
        $plugin_id_b => $plugin_label_b,
      ) ,
      '#required' => FALSE,
      '#title' => $selector_title,
      '#description' => $selector_description,
      '#type' => 'select',
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
