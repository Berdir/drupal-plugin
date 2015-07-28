<?php

/**
 * @file
 * Contains
 * \Drupal\Tests\plugin\Unit\Plugin\Field\FieldFormatter\PluginLabelTest.
 */

namespace Drupal\Tests\plugin\Unit\Plugin\Field\FieldFormatter;

use Drupal\Component\Plugin\PluginInspectionInterface;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\plugin\Plugin\DefaultPluginDefinitionMapper;
use Drupal\plugin\Plugin\Field\FieldFormatter\PluginLabel;
use Drupal\plugin\Plugin\Field\FieldType\PluginCollectionItemInterface;
use Drupal\plugin\Plugin\Field\FieldType\PluginCollectionItemList;
use Drupal\plugin\PluginTypeInterface;
use Drupal\Tests\UnitTestCase;

/**
 * @coversDefaultClass \Drupal\plugin\Plugin\Field\FieldFormatter\PluginLabel
 *
 * @group Plugin
 */
class PluginLabelTest extends UnitTestCase {

  /**
   * The field definition.
   *
   * @var \Drupal\Core\Field\FieldDefinitionInterface|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $fieldDefinition;

  /**
   * The class under test.
   *
   * @var \Drupal\plugin\Plugin\Field\FieldFormatter\PluginLabel
   */
  protected $sut;

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    $this->fieldDefinition = $this->getMock(FieldDefinitionInterface::class);

    $this->sut = new PluginLabel('plugin_label', [], $this->fieldDefinition, [], $this->randomMachineName(), $this->randomMachineName(), []);
  }

  /**
   * @covers ::viewElements
   */
  public function testViewElements() {
    $plugin_label_a = $this->randomMachineName();
    $plugin_label_b = $this->randomMachineName();

    $plugin_definition_a = [
      'label' => $plugin_label_a,
    ];

    $plugin_definition_b = [
      'label' => $plugin_label_b,
    ];

    $plugin_instance_a = $this->getMock(PluginInspectionInterface::class);
    $plugin_instance_a->expects($this->atLeastOnce())
      ->method('getPluginDefinition')
      ->willReturn($plugin_definition_a);

    $plugin_instance_b = $this->getMock(PluginInspectionInterface::class);
    $plugin_instance_b->expects($this->atLeastOnce())
      ->method('getPluginDefinition')
      ->willReturn($plugin_definition_b);

    $item_a = $this->getMock(PluginCollectionItemInterface::class);
    $item_a->expects($this->atLeastOnce())
      ->method('getContainedPluginInstance')
      ->willReturn($plugin_instance_a);

    $item_b = $this->getMock(PluginCollectionItemInterface::class);
    $item_b->expects($this->atLeastOnce())
      ->method('getContainedPluginInstance')
      ->willReturn($plugin_instance_b);

    /** @var \Drupal\plugin\Plugin\Field\FieldType\PluginCollectionItemInterface[]|\PHPUnit_Framework_MockObject_MockObject[] $items */
    $items = [$item_a, $item_b];

    $plugin_definition_mapper = new DefaultPluginDefinitionMapper();

    $plugin_type = $this->getMock(PluginTypeInterface::class);
    $plugin_type->expects($this->atLeastOnce())
      ->method('getPluginDefinitionMapper')
      ->willReturn($plugin_definition_mapper);

    foreach ($items as $item) {
      $item->expects($this->atLeastOnce())
        ->method('getPluginType')
        ->willReturn($plugin_type);
    }

    $iterator = new \ArrayIterator($items);
    $item_list = $this->getMockBuilder(PluginCollectionItemList::class)
      ->disableOriginalConstructor()
      ->setMethods(['getEntity', 'getIterator'])
      ->getMock();
    $item_list->expects($this->atLeastOnce())
      ->method('getIterator')
      ->willReturn($iterator);

    $expected_build = [
      [
        '#markup' => $plugin_label_a,
      ],
      [
        '#markup' => $plugin_label_b,
      ],
    ];

    $this->assertSame($expected_build, $this->sut->viewElements($item_list));
  }

}
