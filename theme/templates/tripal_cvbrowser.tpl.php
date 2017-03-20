<div class="tripal-cvb">
  <ul class="tripal-cvb">
<?php
foreach ($terms as $term) {
  if (0 < $term->children_count) {
    echo '    <li class="tripal-cvb tripal-cvb-root tripal-cvb-has-children tripal-cvb-collapsed"><span class="tripal-cvb-cvterm tripal-cvb-cvtermid-' . $term->cvterm_id . '" title="(' . $term->cv .') ' . str_replace('"', "'", $term->definition) . '">' . $term->name . "</span></li>\n";
  }
  else {
    echo '    <li class="tripal-cvb tripal-cvb-root tripal-cvb-leaf"><span class="tripal-cvb-cvterm tripal-cvb-cvtermid-' . $term->cvterm_id . '">' . $term->name . "</span></li>\n";
  }
}
?>
  </ul>
</div>
