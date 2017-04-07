<?php

/**
 * @file
 * TripalCVBrowserController class definition.
 */

/**
 * Entity controller for CV Browser entities (TripalCVBrowser).
 */
class TripalCVBrowserController extends EntityAPIControllerExportable {

  /**
   * Creates a new CV Browser.
   */
  public function create(array $values = array()) {
    global $user;
    $values += array(
      'cvbid' => 0,
      'title' => 'CV Browser',
      'machine_name' => '',
      'description' => '',
      'root_type' => 'cvterm',
      'root_ids' => '0',
      'show_cv' => '1',
      'uid' => $user->uid,
      'type' => 'tripal_cvb',
    );
    return parent::create($values);
  }

  /**
   * Fills CV Browser field settings.
   */
  public function buildContent(
    $entity,
    $view_mode = 'full',
    $langcode = NULL,
    $content = array()
  ) {

    $content['description'] = array(
      '#theme'              => 'field',
      '#weight'             => 1,
      '#title'              => t('Description'),
      '#access'             => TRUE,
      '#label_display'      => 'above',
      '#view_mode'          => 'full',
      '#language'           => LANGUAGE_NONE,
      '#field_translatable' => TRUE,
      '#field_name'         => 'description',
      '#field_type'         => 'text',
      '#entity_type'        => 'tripal_cvb',
      '#bundle'             => 'tripal_cvb',
      '#items'              => array(array('value' => $entity->description)),
      '#formatter'          => 'text_default',
      0                     => array(
        '#markup' => check_plain($entity->description),
      ),
    );

    return parent::buildContent($entity, $view_mode, $langcode, $content);
  }

  /**
   * Loads CV Browser.
   */
  public function load($ids = FALSE, $conditions = array()) {
    $browser = parent::load($ids, $conditions);
    return $browser;
  }

  /**
   * Saves a CV Browser data.
   */
  public function save($browser, DatabaseTransaction $transaction = NULL) {
    $return_value = NULL;
    $return_value = parent::save($browser, $transaction);

    // Update path alias.
    if ($return_value) {
      // Get connection URI.
      $browser_uri = 'tripal_cvb/' . $browser->cvbid;
      // Get previous alias if one.
      $old_path = path_load(
        array('source' => $browser_uri)
      );
      // And remove it.
      if ($old_path) {
        path_delete($old_path['pid']);
      }
      // Add new alias.
      $alias =
        'tripal-cvb-'
        . $browser->machine_name;
      $path_alias = array(
        'source' => $browser_uri,
        'alias' => $alias,
      );
      path_save($path_alias);
    }

    return $return_value;
  }

  /**
   * Removes connections and their associated data.
   */
  public function delete($ids, DatabaseTransaction $transaction = NULL) {
    // Remove previous path aliases.
    foreach ($ids as $id) {
      $browser_uri = 'tripal_cvb/' . $id;
      // Get previous alias if one.
      $old_path = path_load(
        array('source' => $browser_uri)
      );
      // And remove it.
      if ($old_path) {
        path_delete($old_path['pid']);
      }
    }
    return parent::delete($ids, $transaction);
  }

}
