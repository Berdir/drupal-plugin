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
   * The class under test.
   *
   * @var \Drupal\plugin\DefaultPluginTypeOperationsProvider
   */
  protected $sut;

  public function setUp() {
    $this->stringTranslation = $this->getStringTranslationStub();

    $this->sut = new DefaultPluginTypeOperationsProvider($this->stringTranslation);
  }

  /**
   * @covers ::create
   * @covers ::__construct
   */
  function testCreate() {
    $container = $this->getMock('\Symfony\Component\DependencyInjection\ContainerInterface');
    $map = [
      ['string_translation', ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE, $this->stringTranslation],
    ];
    $container->expects($this->any())
      ->method('get')
      ->willReturnMap($map);

    $sut = DefaultPluginTypeOperationsProvider::create($container);
    $this->assertInstanceOf('\Drupal\plugin\DefaultPluginTypeOperationsProvider', $sut);
  }

  /**
   * @covers ::getOperations
   */
  public function testGetOperations() {
    $this->assertOperationsLinks($this->sut->getOperations($this->randomMachineName()));
  }

}
