Tripal CV Browser
------------------
This module can be used to display and browse a hierachical tree representation
of a controlled vocabulary (CV) stored into Chado (cvterm table). The vocabulary
to browse must have relationships stored into the cvterm_relationship table
where the children term is the subject (subject_id) and the parent term is the
object (object_id). Circular relationships and network relationships are not an
issue since the tree is not generated nor loaded when the CV browser page is
accessed; Only root elements are displayed and each first level of a subtree is
loaded (ajax) when the used click on the parent node.