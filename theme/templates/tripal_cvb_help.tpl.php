<?php

/**
 * @file
 * Tripal CV Browser help page.
 *
 * @ingroup tripal_cvb
 */
?>

<h3>About Tripal CV Browser</h3>
<p>
Tripal CV Browser extension provides a tree browser that enable CV and ontology
browsing.
The browser base URL is <?php url('tripal/cvbrowser/'); ?> followed by either
'cv/' and a Chado CV identifier (cv_id) or 'cvterm/' and a Chado CV term
identifier (cvterm_id). If you want to browse more than one CV or CV term
subtree on the browser, you can specify several identifiers separated by the
plus &quot;+&quot; sign. You can also use CV names or CV term names (case
sensistive) instead of identifiers but for the later, you may have more than one
CV term matching.
</p>