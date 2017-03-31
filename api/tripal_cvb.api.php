<?php

/**
 * @file
 * API functions provided by Tripal CV Browser module.
 *
 * Provides an application programming interface (API) for working with Tripal
 * CV Borwser.
 *
 * @ingroup tripal_cvb
 */

/**
 * @defgroup tripal_cvb_api API of Tripal CV Browser module
 * @ingroup tripal_cvb
 * @{
 * Provides an application programming interface (API) for working with Tripal
 * CV Borwser.
 * @}
 */

/**
 * Returns CV settings.
 *
 * Returns an array containing the CV settings used by this Tripal CV Browser
 * installation.
 *
 * @param bool $reset
 *   Clear current settings and reload them from database.
 *
 * @return array
 *   key are Tripal CV Browser API term/field names and values are corresponding
 *   Chado cvterm_id.
 *
 * @ingroup tripal_cvb_api
 */
function tripal_cvb_get_cv_settings($reset = FALSE) {
  static $settings;
  // If not initialized, get it from cache if available.
  if (!isset($settings) || $reset) {
    if (!$reset
        && ($cache = cache_get('tripal_cvb_settings'))
        && !empty($cache->data)) {
      $settings = $cache->data;
    }
    else {
      // Not available in cache, get it from saved settings.
      $settings = variable_get('tripal_cvb_settings', array());
      drupal_alter('tripal_cvb_settings', $settings);
      cache_set('tripal_cvb_settings', $settings);
    }
  }
  else {
    drupal_alter('tripal_cvb_settings', $settings);
  }

  return $settings;
}

/**
 * Returns a list of children CV terms of the given CV term.
 *
 * @param int $cvterm_id
 *   a Chado CV term identifier.
 * @return string
 *   a JSON hash containing CV term children data associated by cvterm_id.
 *   Provided cvterm fields are:
 *    * cvterm_id: Chado CV term cvterm_id;
 *    * cv_id: Chado CV term cv_id;
 *    * cv: CV term CV name;
 *    * name: CV term name;
 *    * definition: CV term definition;
 *    * dbxref_id: Chado CV term dbxref_id;
 *    * dbxref: Chado CV term accession on its database;
 *    * db: Chado CV term database name;
 *    * urlprefix: URL prefix to access term description on its associated
 *      database (append the accession to access term);
 *    * is_obsolete: if non-0 ("1"), term is considered as obsolete;
 *    * is_relationshiptype: if non-0 ("1"), term is considered as qualifying a
 *      relationship;
 *    * relationship: relationship (name) with parent;
 *    * children_count: number of children terms.
 */
function tripal_cvb_get_cvterm_info_json($cvterm_id) {

  $cvterm_data = chado_select_record(
    'cvterm',
    array('*'),
    array(
      'cvterm_id' => $cvterm_id,
    )
  );
  
  if (is_array($cvterm_data)) {
    $cvterm_data = current($cvterm_data);
  }
  
  drupal_json_output($cvterm_data);
}

/**
 * Returns a list of children CV terms of the given CV term.
 *
 * @param int $cvterm_id
 *   a Chado CV term identifier.
 * @return array
 *   an array of CVTerms.
 */
function tripal_cvb_get_cvterm_children($cvterm_id) {
  $cvterm_data = array();
  
  $sql_query = '
    SELECT
      cvt.cvterm_id,
      cvt.cv_id,
      cv.name AS "cv",
      cvt.name,
      cvt.definition,
      cvt.dbxref_id,
      dbx.accession AS "dbxref",
      db.name AS "db",
      db.urlprefix AS "urlprefix",
      cvt.is_obsolete,
      cvt.is_relationshiptype,
      cvtrcvt.name AS "relationship",
      (SELECT COUNT(1) FROM cvterm_relationship cvtr2 WHERE cvtr2.object_id = cvtr.subject_id) AS "children_count"
    FROM cvterm cvt
      JOIN cvterm_relationship cvtr ON cvtr.subject_id = cvt.cvterm_id
      JOIN cvterm cvtrcvt ON cvtr.type_id = cvtrcvt.cvterm_id
      JOIN cv cv ON cv.cv_id = cvt.cv_id
      JOIN dbxref dbx ON dbx.dbxref_id = cvt.dbxref_id
      JOIN db db ON db.db_id = dbx.db_id
    WHERE cvtr.object_id = :object_cvterm_id;
  ';
  $relationship_records = chado_query(
    $sql_query,
    array(':object_cvterm_id' => $cvterm_id)
  );
  
  foreach ($relationship_records as $relationship) {
    $cvterm_data[$relationship->cvterm_id] = $relationship;
  }
  
  return $cvterm_data;
}

/**
 * Returns a list of children CV terms of the given CV term.
 *
 * @param int $cvterm_id
 *   a Chado CV term identifier.
 * @return string
 *   a JSON hash containing CV term children data associated by cvterm_id.
 */
function tripal_cvb_get_cvterm_children_json($cvterm_id) {
  drupal_json_output(tripal_cvb_get_cvterm_children($cvterm_id));
}

/**
 * Renders the CV Browser page.
 *
 * @param string $browser_type
 *   The type of object to browse. Supported types are 'cv' and 'cvterm'.
 * @param mixed $root_ids
 *   Single or multiple (array) Chado identifiers for the type of object 
 *   to browse.
 *
 * @return string
 *   The CV Browser page.
 *
 * @Throws Exception
 *   Throw an exception is the value type is not correct.
 */
function tripal_cvb_cv_render($browser_type, $root_ids) {

  if (!isset($browser_type)) {
    $browser_type = 'cv';
  }
  if (!isset($root_ids)) {
    $root_ids = '';
  }

  if ('browser' == $browser_type) {
    $query = new EntityFieldQuery();
    $query->entityCondition('entity_type', 'tripal_cvb');
    $query->propertyCondition('machine_name', $root_ids);
    $results = $query->execute();
    if (!empty($results)) {
      $entities = entity_load('tripal_cvb', array(key($results['tripal_cvb'])));
      $browser = current($entities);
    }
  }
  else {
    $browser = entity_create(
      'tripal_cvb',
      array(
        'root_type' => $browser_type,
        'root_ids' => $root_ids,
      )
    );
  }

  if (!isset($browser)) {
    $browser = entity_create('tripal_cvb',array());
  }

  return tripal_cvb_browser_render($browser);
}

/**
 * Renders the CV Browser page.
 *
 * @param tripal_cvb $browser
 *   A CV Browse object to render.
 *
 * @return string
 *   The CV Browser page.
 *
 * @Throws Exception
 *   Throw an exception is the value type is not correct.
 */
function tripal_cvb_browser_render($browser) {
  $root_ids = $browser->root_ids;

  if (!isset($root_ids)) {
    $root_ids = array(0);
  }
  if (!is_array($root_ids)) {
    $root_ids = explode('+', $root_ids);
  }

  // Separate litteral names and numeric identifiers.
  $selected_ids = array();
  $selected_names = array();
  foreach ($root_ids as $id) {
    if (preg_match('/^\d+$/', $id)) {
      $selected_ids[] = $id;
    }
    else {
      $selected_names[] = $id;
    }
  }

  // Make sure we got something.
  if (empty($selected_ids) && empty($selected_names)) {
    throw new Exception(t(
      "No identifier specified!"
    ));
  }

  if (!empty($selected_names) && !empty($selected_ids)) {
    drupal_set_message('Mixing names and identifiers will only retain objects matching both.', 'warning');
  }

  // Initialize stuff.
  $cv_terms = array();
  $where_clause = array();
  $values = array();

  // Check data type to build query.
  switch ($browser->root_type) {
    case 'cv':
      // We only want root terms of the given CVs.
      $where_clause[] = 'NOT EXISTS (
          SELECT TRUE
          FROM cvterm_relationship cvtr
            JOIN cvterm pcvt ON pcvt.cvterm_id = cvtr.object_id
          WHERE cvtr.subject_id = cvt.cvterm_id AND pcvt.cv_id = cvt.cv_id
          LIMIT 1
        )';
      if (!empty($selected_names)) {
        $where_clause[] = 'cv.name IN (:cv_names)';
        $values[':cv_names'] = $selected_names;
      }
      if (!empty($selected_ids)) {
        $where_clause[] = 'cvt.cv_id IN (:cv_ids)';
        $values[':cv_ids'] = $selected_ids;
      }
      break;

    case 'cvterm':
      if (!empty($selected_names)) {
        $where_clause[] = 'cvt.name IN (:cvterm_names)';
        $values[':cvterm_names'] = $selected_names;
      }
      if (!empty($selected_ids)) {
        $where_clause[] = 'cvt.cvterm_id IN (:cvterm_ids)';
        $values[':cvterm_ids'] = $selected_ids;
      }
      break;

    default:
      throw new Exception(t(
        "Unsupported object type @type!",
        array(
          '@type' => check_plain($browser->root_type),
        )
      ));
      break;
  }

  $sql_query = '
    SELECT
      cvt.cvterm_id,
      cvt.cv_id,
      cv.name AS "cv",
      cvt.name,
      cvt.definition,
      cvt.dbxref_id,
      dbx.accession AS "dbxref",
      db.name AS "db",
      db.urlprefix AS "urlprefix",
      cvt.is_obsolete,
      cvt.is_relationshiptype,
      NULL AS "relationship",
      (SELECT COUNT(1) FROM cvterm_relationship cvtr2 WHERE cvtr2.object_id = cvt.cvterm_id) AS "children_count"
    FROM cvterm cvt
      JOIN cv cv ON cv.cv_id = cvt.cv_id
      JOIN dbxref dbx ON dbx.dbxref_id = cvt.dbxref_id
      JOIN db db ON db.db_id = dbx.db_id'
    . (empty($where_clause)?'':' WHERE ')
    .  implode(' AND ', $where_clause);
  ;

  $term_records = chado_query(
    $sql_query,
    $values
  );

  // Get actions.
  $actions = array();
  if (isset($browser->tripal_cvb_cvterm_action)
      && isset($browser->tripal_cvb_cvterm_action[LANGUAGE_NONE])) {
      $actions = $browser->tripal_cvb_cvterm_action[LANGUAGE_NONE];
/*    foreach ($browser->tripal_cvb_cvterm_action[LANGUAGE_NONE] as $action) {

                            [type] => view
                            [action] => term_details:term_page
                            [title] => details
                            [autorun] => 1
                            [target] => region:content
                            [insert] => replace
    }*/
  }
  
  return theme(
    'tripal_cvbrowser',
    array(
      'terms' => $term_records,
      'browser' => $browser,
      'actions' => $actions,
    )
  );
}

