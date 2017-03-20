<?php

/**
 * @file
 * Contains the functions used for administration of the Tripal CV Browser
 * module.
 *
 * @ingroup tripal_cvb
 */

/**
 * Administrative settings form.
 *
 * @return string
 *   the HTML content of the administration form.
 *
 * @ingroup tripal_cvb
 */
function tripal_cvb_admin_form($form, &$form_state) {
  $form = array();

  // CV settings.
  $form['cv_settings'] = array(
    '#type'        => 'fieldset',
    '#attributes'  => array('class' => array('container-inline')),
    '#title'       => t('Controlled vocabulary settings'),
    '#collapsible' => TRUE,
    '#collapsed'   => FALSE,
  );

  $form['save_settings'] = array(
    '#type'        => 'submit',
    '#value'       => t('Save configuration'),
    '#submit'      => array('tripal_cvb_admin_form_submit'),
  );

  return $form;
}

/**
 * Check and applies Tripal CV Browser configuration changes.
 *
 * @ingroup tripal_cvb
 */
function tripal_cvb_admin_form_submit($form_id, &$form_state) {

}
