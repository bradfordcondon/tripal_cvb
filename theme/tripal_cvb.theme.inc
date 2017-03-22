<?php

/**
 * @file
 * This file should contain all Drupal hooks for theming content.
 *
 */

/**
 * Implements hook_preprocess_HOOK().
 *
 * Used to alter or add to theme variables. The variables are passed into
 * templates when processing. This function organizes the relationships
 * into more simple structures for parsing in the template file.
 *
 * @ingroup tripal_cvb
 */
// function tripal_cvb_preprocess_tripal_cvbrowser(&$variables) {
// //  drupal_add_js(drupal_get_path('module', 'tripal_cvb') .'/theme/js/d3.min.js');
// //  drupal_add_css(drupal_get_path('module', 'tripal_cvb') .'/theme/css/tripal_cvb.css');
// 
//   $variables['cv'] = '';
// }

function tripal_cvb_preprocess_tripal_cvb_list(&$variables) {
  // Set the breadcrumb.
  $breadcrumb = array();
  $breadcrumb[] = l(t('Home'), '<front>');
  drupal_set_breadcrumb($breadcrumb);

  $variables['cvb_table'] = entity_ui_controller('tripal_cvb')->overviewTable();
  $variables['pager'] = theme('pager');
}
