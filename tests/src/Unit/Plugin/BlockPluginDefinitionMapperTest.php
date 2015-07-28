<?php

/**
 * @file
 * Contains \Drupal\Tests\plugin\Unit\Plugin\Plugin\BlockPluginDefinitionMapperTest.
 */

namespace Drupal\Tests\plugin\Unit\Plugin;

use Drupal\plugin\Plugin\BlockPluginDefinitionMapper;
use Drupal\Tests\UnitTestCase;

/**
 * @coversDefaultClass \Drupal\plugin\Plugin\BlockPluginDefinitionMapper
 *
 * @group Plugin
 */
class BlockPluginDefinitionMapperTest extends UnitTestCase {

  /**
   * The plugin definition.
   *
   * @var mixed[]
   */
  protected $pluginDefinition;

  /**
   * The class under test.
   *
   * @var \Drupal\plugin\Plugin\BlockPluginDefinitionMapper
   */
  protected $sut;

  public function setUp() {
    $this->pluginDefinition = [
      'admin_label' => $this->getRandomGenerator()->string(),
    ];

    $this->sut = new BlockPluginDefinitionMapper();
  }

  /**
   * @covers ::getPluginLabel
   */
  public function testGetPluginLabel() {
    $this->assertSame($this->pluginDefinition['admin_label'], $this->sut->getPluginLabel($this->pluginDefinition));

    unset($this->pluginDefinition['admin_label']);

    $this->assertNull($this->sut->getPluginLabel($this->pluginDefinition));
  }

}
