<?php

/**
 * @file
 * Contains \Drupal\Tests\plugin\Unit\DefaultPluginTypeOperationsProviderTest.
 */

namespace Drupal\Tests\plugin\Unit;

use Drupal\plugin\DefaultPluginTypeOperationsProvider;
use Drupal\Tests\plugin\OperationsProviderTestTrait;
use Drupal\Tests\UnitTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @coversDefaultClass \Drupal\plugin\DefaultPluginTypeOperationsProvider
 *
 * @group Plugin
 */
class DefaultPluginTypeOperationsProviderTest extends UnitTestCase {

  use OperationsProviderTestTrait;

  /**
   * The string translator.
   *
   * @var \Drupal\Core\StringTranslation\TranslationInterface
   */
  protected $stringTranslation;

  /**
   * The container services.
   *
   * @var object[]
   *   Keys are service IDs and values are the instantiated services.
   */
  protected $services = [];

  /**
   * The class under test.
   *
   * @var \Drupal\plugin\DefaultPluginTypeOperationsProvider
   */
  protected $sut;

  public function setUp() {
    $this->stringTranslation = $this->getStringTranslationStub();

    $this->services = [
      'string_translation' => $this->stringTranslation,
    ];

    $this->sut = new DefaultPluginTypeOperationsProvider($this->stringTranslation);
  }

  /**
   * @covers ::create
   * @covers ::__construct
   */
  function testCreate() {
    $container = $this->getMock('\Symfony\Component\DependencyInjection\ContainerInterface');
    $map = [];
    foreach ($this->services as $service_id => $service) {
      $map[] = [$service_id, ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE, $service];
    }
    $container->expects($this->any())
      ->method('get')
      ->willReturnMap($map);

    /** @var \Drupal\plugin\DefaultPluginTypeOperationsProvider $sut_class */
    $sut_class = get_class($this->sut);
    $sut = $sut_class::create($container);
    $this->assertInstanceOf($sut_class, $sut);
  }

  /**
   * @covers ::getOperations
   */
  public function testGetOperations() {
    $this->assertOperationsLinks($this->sut->getOperations($this->randomMachineName()));
  }

}