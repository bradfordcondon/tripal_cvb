<div class="tripal-cvb tripal-cvb-browser tripal-cvb-browser-<?php echo $browser->machine_name; ?>">
  <ul class="tripal-cvb">
<?php
foreach ($terms as $term) {
  if (0 < $term->children_count) {
    echo '    <li class="tripal-cvb tripal-cvb-root tripal-cvb-has-children tripal-cvb-collapsed"><span class="tripal-cvb-cvterm tripal-cvb-cvtermid-' . $term->cvterm_id . '" title="(' . $term->cv .') ' . str_replace('"', "'", $term->definition) . '">' . $term->name . '</span>';
  }
  else {
    echo '    <li class="tripal-cvb tripal-cvb-root tripal-cvb-leaf"><span class="tripal-cvb-cvterm tripal-cvb-cvtermid-' . $term->cvterm_id . '">' . $term->name . '</span>';
  }
  echo "</li>\n";
}
?>
  </ul>
  <script>
    var tripal_cvb_<?php echo preg_replace('/\W/', '_', $browser->machine_name); ?> =
<?php
    echo json_encode($actions);
?>
    ;
  </script>
</div>
