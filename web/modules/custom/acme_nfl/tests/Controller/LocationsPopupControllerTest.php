<?php

namespace Drupal\acme_nfl\Tests;

use Drupal\simpletest\WebTestBase;
use Drupal\Core\Config\ConfigManagerInterface;

/**
 * Provides automated tests for the acme_nfl module.
 */
class LocationsPopupControllerTest extends WebTestBase {

  /**
   * Drupal\Core\Config\ConfigManagerInterface definition.
   *
   * @var \Drupal\Core\Config\ConfigManagerInterface
   */
  protected $configManager;


  /**
   * {@inheritdoc}
   */
  public static function getInfo() {
    return [
      'name' => "acme_nfl LocationsPopupController's controller functionality",
      'description' => 'Test Unit for module acme_nfl and controller LocationsPopupController.',
      'group' => 'Other',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();
  }

  /**
   * Tests acme_nfl functionality.
   */
  public function testLocationsPopupController() {
    // Check that the basic functions of module acme_nfl.
    $this->assertEquals(TRUE, TRUE, 'Test Unit Generated via Drupal Console.');
  }

}
