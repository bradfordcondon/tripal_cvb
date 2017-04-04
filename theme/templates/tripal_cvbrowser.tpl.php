<?php

/**
 * @file
 * Tripal CV Browser browser page.
 *
 * Displays the root part of a given CV browser and initializes its action
 * javascript array.
 *
 * @ingroup tripal_cvb
 */
 
drupal_add_css(drupal_get_path('module', 'tripal_cvb') . '/theme/css/tripal_cvb.css');
drupal_add_js(drupal_get_path('module', 'tripal_cvb') .'/theme/js/tripal_cvb.jss');
?>

<div class="tripal-cvb tripal-cvb-browser<?php
  echo ($browser->machine_name ? ' tripal-cvb-browser-' . $browser->machine_name : '');
?>">
<?php
if ($terms->rowCount()) {
  echo "<ul class=\"tripal-cvb\">\n";
  // Displays first level nodes.
  foreach ($terms as $term) {
    if (0 < $term->children_count) {
      echo
        '    <li class="tripal-cvb tripal-cvb-root tripal-cvb-has-children tripal-cvb-collapsed"><span class="tripal-cvb-cvterm tripal-cvb-cvtermid-'
        . $term->cvterm_id
        . '" title="('
        . $term->cv
        . ') '
        . str_replace('"', "'", $term->definition)
        . '">'
        . $term->name
        . '</span>';
    }
    else {
      echo
        '    <li class="tripal-cvb tripal-cvb-root tripal-cvb-leaf"><span class="tripal-cvb-cvterm tripal-cvb-cvtermid-'
        . $term->cvterm_id
        . '">'
        . $term->name
        . '</span>';
    }
    echo "</li>\n";
  }
  echo "</ul>\n";
}
else {
  echo t("<span>No term found!</span>");
}
?>
  <script type="text/javascript">
    var tripal_cvb_<?php
      echo preg_replace('/\W/', '_', $browser->machine_name);
    ?> =
<?php
    echo json_encode($actions);
?>
    ;
  </script>
</div>
