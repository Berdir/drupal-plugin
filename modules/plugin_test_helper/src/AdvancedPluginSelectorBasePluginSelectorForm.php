<?php

/**
 * @file
 * Contains \Drupal\plugin_test_helper\AdvancedPluginSelectorBasePluginSelectorForm.
 */

namespace Drupal\plugin_test_helper;

use Drupal\Component\Plugin\PluginManagerInterface;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\DependencyInjection\DependencySerializationTrait;
use Drupal\Core\Form\FormInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\plugin\Plugin\DefaultPluginDefinitionMapper;
use Drupal\plugin\Plugin\FilteredPluginManager;
use Drupal\plugin\Plugin\Plugin\PluginSelector\PluginSelectorManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a form to test plugin selector plugins based on AdvancedPluginSelectorBase.
 */
class AdvancedPluginSelectorBasePluginSelectorForm implements ContainerInjectionInterface, FormInterface {

  use DependencySerializationTrait;

  /**
   * The plugin selector manager.
   *
   * @var \Drupal\plugin\Plugin\Plugin\PluginSelector\PluginSelectorManagerInterface
   */
  protected $pluginSelectorManager;

  /**
   * A plugin manager.
   *
   * @var \Drupal\Component\Plugin\PluginManagerInterface.
   */
  protected $pluginManager;

  /**
   * Constructs a new class instance.
   */
  function __construct(PluginManagerInterface $plugin_manager, PluginSelectorManagerInterface $plugin_selector_manager) {
    $this->pluginManager = $plugin_manager;
    $this->pluginSelectorManager = $plugin_selector_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(new SelectablePluginManager(), $container->get('plugin.manager.plugin.plugin_selector'));
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'plugin_test_helper_advanced_plugin_selector_base';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $allowed_selectable_plugin_ids = NULL, $plugin_id = NULL, $tree = FALSE) {
    if ($form_state->has('plugin_selector')) {
      $plugin_selector = $form_state->get('plugin_selector');
    }
    else {
      $plugin_definition_mapper = new DefaultPluginDefinitionMapper();
      $plugin_manager = new FilteredPluginManager($this->pluginManager, $plugin_definition_mapper);
      $plugin_manager->setPluginIdFilter(explode(',', $allowed_selectable_plugin_ids));
      $plugin_selector = $this->pluginSelectorManager->createInstance($plugin_id);
      $plugin_selector->setPluginManager($plugin_manager, $plugin_definition_mapper);
      $plugin_selector->setRequired();
      $form_state->set('plugin_selector', $plugin_selector);
    }

    $form['plugin'] = $plugin_selector->buildSelectorForm([], $form_state);
    // Nest the selector in a tree if that's required.
    if ($tree) {
      $form['tree'] = array(
        '#tree' => TRUE,
      );
      $form['tree']['plugin'] = $form['plugin'];
      unset($form['plugin']);
    }
    $form['actions'] = array(
      '#type' => 'actions',
    );
    $form['actions']['submit'] = array(
      '#type' => 'submit',
      '#value' => t('Submit'),
    );

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    /** @var \Drupal\plugin\Plugin\Plugin\PluginSelector\PluginSelectorInterface $plugin_selector */
    $plugin_selector = $form_state->get('plugin_selector');
    $plugin_form = isset($form['tree']) ? $form['tree']['plugin'] : $form['plugin'];
    $plugin_selector->validateSelectorForm($plugin_form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    /** @var \Drupal\plugin\Plugin\Plugin\PluginSelector\PluginSelectorInterface $plugin_selector */
    $plugin_selector = $form_state->get('plugin_selector');
    $plugin_form = isset($form['tree']) ? $form['tree']['plugin'] : $form['plugin'];
    $plugin_selector->submitSelectorForm($plugin_form, $form_state);
    \Drupal::state()->set($this->getFormId(), $plugin_selector->getSelectedPlugin());
  }
}
