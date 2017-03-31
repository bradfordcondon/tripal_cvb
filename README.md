Tripal CV Browser Extension Module
==================================

CONTENTS OF THIS FILE
---------------------

 * Introduction
 * Requirements
 * Installation
 * Configuration
 * Actions
 * Maintainers


INTRODUCTION
------------

This module can be used to display and browse a hierachical tree representation
of any controlled vocabulary (CV) stored into Chado (cvterm table). The
vocabulary to browse must have relationships stored into the cvterm_relationship
table where the children term is the subject (subject_id) and the parent term is
the object (object_id). Circular relationships and network relationships are not
an issue since the tree is not generated nor loaded when the CV browser page is
accessed; Only root elements are displayed and each first level of a subtree is
loaded (ajax) when the used click on the parent node.

A generic CV browser page can be used to browse any CV from CV root term(s) or
from user-selected terms. The URL of the page can be constructed this way:
'tripal/cvbrowser/' + <either 'cv' or 'cvterm', depending on the type of
identifiers provided after> + <CV or CV term identifier(s) or name(s) (separated
by "+" signs if more than on value is provided)>.
Examples:
 - tripal/cvbrowser/cv/biological_process+molecular_function+cellular_component
 - tripal/cvbrowser/cvterm/1234


REQUIREMENTS
------------

This module requires the following modules:

 * Tripal 7.x-2.x (not tested under 3.x) (http://www.drupal.org/project/tripal)


INSTALLATION
------------

 * Install as you would normally install a contributed Drupal module. See:
   https://drupal.org/documentation/install/modules-themes/modules-7
   for further information.

 * Enable the module in "Admin menu > Site building > Modules" (/admin/modules).


CONFIGURATION
-------------

 * Configure the CV browser in "Administration > Tripal > Extensions >
   Tripal CV Browser > Settings" (/admin/tripal/extension/tripal_cvb).

 * Configure user permissions in "Administration > People > Permissions"
   (/admin/people/permissions):

   - "Use tripal cv browser page": allows users to access to the generic CV
     browser page. With this permission, any CV or CV term can be browsed as the
     user only needs to know the CV name or CV cv_id or CV term name or CV term
     cvterm_id in order to use it in the browser page URL. If you want to
     restrict CV browsing, then do not give this permission to your users.

   - "Administer Tripal CV Browser": allows users to access to the
     CV browser administration pages and see the administration help page.

 * After creating custom CV browser(s), place them on your site using the block
   configuration page in "Administration > Structure > Blocks"
   (admin/structure/block). You can also configure access permission and block
   visibility for each block using their "configuration" link.
   If you prefer to have your browsers displayed as a page instead of a block,
   you can use the Drupal path 'cvbrowser/browser/' + the CV browser machine
   name. In this case, access are managed using the "Use tripal cv browser page"
   permission.


ACTIONS
-------

Actions are operations that can be performed automatically or by a user click
on a term of a CV browser. There are several types of actions:

 - View: it uses a display of a Drupal view to render something given a
   "cvterm_id" as parameter.

 - Path: it uses a Drupal path and append a given "cvterm_id" to it as parameter
   in order to display the associated page content.

 - External URL: it displays a link (with the "cvterm_id" appended) to an
   external site page.

 - Javascript: it executes the given javascript function name given it the
   "cvterm_id" as only argument.

Action field (ie. action to use) content depends on the type of action. For
"View", it must contain the view machine name, colon, and the display id. For
"Path", it must be the Drupal path with or without ending slash. For "External
URL", it must be the full URL (the "cvterm_id" will be appended to that URL).
And finally, for "Javascript", it must be the function name without parenthesis
or other piece of code.

Link label is the label of the action that is displayed before the action has
been executed.

The Auto-run checkbox setup if the action should be executed when the page is
loaded or when the user clicks on the action link. This setting is ignored by
the "External URL" type of action.

The target defines where the output of the action should be displayed. There are
3 types of targets:

 - Term line: it will be displayed on the same line as the CV term on the CV
   browser tree.

 - Theme region: it will be displayed on a region of the current Drupal theme.

 - DOM object identifier: it will be displayed in the HTML element matching the
   given identifier (it is based on jQuery selectors). By default, it will be
   the CV term <span> element.

The target identifier is used to identify the corresponding target according to
its type: it is ignored for the Term line, it is the region machine name for
the Theme region and it is a jQuery selector for the DOM object identifier.

The last setting is the insertion method: "Replace" will replace the content of
the corresponding target and "Append" will add the content to the given
target.
Note: the target settings are ignored for the "External URL" type of action.


MAINTAINERS
-----------

Current maintainers:

 * Valentin Guignon (vguignon) - https://www.drupal.org/user/423148
 * Gaëtan Droc
