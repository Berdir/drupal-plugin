<?php

/**
 * @file
 * Contains \Drupal\Core\Plugin\PluginCategoryDefinitionInterface.
 */

namespace Drupal\plugin\PluginDefinition;

/**
 * Defines a plugin definition that includes a category.
 *
 * @ingroup Plugin
 */
interface PluginCategoryDefinitionInterface extends PluginDefinitionInterface {

  /**
   * Sets the category.
   *
   *
   * @param \Drupal\Core\StringTranslation\TranslationWrapper|string $category
   *   The category.
   *
   * @return $this
   */
  public function setCategory($category);

  /**
   * Gets the category.
   *
   * @return \Drupal\Core\StringTranslation\TranslationWrapper|string|null
   *   The category.
   */
  public function getCategory();

}
