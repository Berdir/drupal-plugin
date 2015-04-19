<?php

/**
 * @file
 * Contains \Drupal\plugin_selector\Annotation\PluginSelector.
 */

namespace Drupal\plugin_selector\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * Provides a plugin selector plugin annotation.
 *
 * @Annotation
 */
class PluginSelector extends Plugin {

  /**
   * The plugin ID.
   *
   * @var string
   */
  public $id;

  /**
   * The translated human-readable plugin name.
   *
   * @var string
   */
  public $label;
}
