/**
 * @file
 * Tripal CV Browser Javascript library
 *
 */
(function ($/*, Drupal, window, document, undefined*/) {
"use strict";

  Drupal.tripal_cvb = Drupal.tripal_cvb || {};

  Drupal.tripal_cvb.toggleCVSubTree = function (event){
    if ($(this).parent().is('.tripal-cvb-collapsed')) {
      $(this).parent()
        .removeClass('tripal-cvb-collapsed')
        .addClass('tripal-cvb-expanded')
      ;
    }
    else {
      $(this).parent()
        .removeClass('tripal-cvb-expanded')
        .addClass('tripal-cvb-collapsed')
      ;
    }
    $(this).siblings('ul:first').toggle();
  }
  
  Drupal.tripal_cvb.initCVTreeNodes = function (){
    $('.tripal-cvb-has-children')
      .not('.tripal-cvb-onclick-processed')
      .addClass('tripal-cvb-onclick-processed')
      .on('click.tripal-cvb-expand', function() {
        var $cvterm_li = $(this);
        var term_classes = $cvterm_li.children('.tripal-cvb-cvterm:first').prop('class');
        var patt = /tripal-cvb-cvtermid-(\d+)/;
        var id_match = patt.exec(term_classes);
        var cvterm_id = id_match[1];
        $cvterm_li
          .removeClass('tripal-cvb-collapsed')
          .addClass('tripal-cvb-expanded')
          .prepend($('<div class="ajax-progress"><div class="throbber">&nbsp;</div></div>'));
        // Calls the function that returns the children CV Terms as JSON.
        $.ajax({
          url: Drupal.settings.basePath + 'tripal/cvb/cvterm/' + cvterm_id + '/children',
          type: 'GET',
          dataType: 'json',
          success: function(output) {
            if (output && ('object' == typeof output)
                && (Object.keys(output).length > 0)) {
              var ul = $('<ul class="tripal-cvb"></ul>');
              $.each(output, function(child_cvterm_id, child_cvterm) {
                var li_class =
                'tripal-cvb'
                  + (0 < parseInt(child_cvterm.children_count)?' tripal-cvb-has-children tripal-cvb-collapsed':' tripal-cvb-leaf')
                  + ' tripal-cvb-relationship-' + child_cvterm.relationship.replace(/\W+/g, '_')
                ;
                var li_title = child_cvterm.relationship.replace('"', "'");
                var cvterm_class =
                  'tripal-cvb-cvterm tripal-cvb-cvtermid-' + child_cvterm_id
                  + (child_cvterm.is_obsolete == '1'?' tripal-cvb-obsolete':'')
                ;
                var cvterm_title = child_cvterm.definition;
                if (cvterm_title) {
                  cvterm_title = '(' + child_cvterm.cv.replace('"', "'") + ') ' + cvterm_title.replace('"', "'");
                }
                else {
                  cvterm_title = '';
                }
                var cvterm_name = child_cvterm.name
                  .replace('&', '&amp;')
                  .replace('<', '&lt;')
                  .replace('>', '&lt;')
                ;
                ul.append(
                  '<li class="' + li_class + '" title="' + li_title + '">'
                  + '<span class="' + cvterm_class + '" title="' + cvterm_title + '">' + cvterm_name + '</span>'
                  + '</li>\n'
                );
              });
              $cvterm_li.append(ul);
              Drupal.tripal_cvb.initCVTreeNodes();
            }
            $cvterm_li
              .off('click.tripal-cvb-expand')
              .children('.tripal-cvb-cvterm:first')
                .on('click', Drupal.tripal_cvb.toggleCVSubTree);
            $cvterm_li
              .find('> .ajax-progress')
                .remove();
          },
          error: function(jqXHR, textStatus, errorThrown) {
            $cvterm_li
              .remove('.ajax-progress');
            alert('Failed to get CVTerm data for cvterm_id ' + cvterm_id + ': ' + textStatus);
          }
        });
      })
    ;
  };
 
  Drupal.behaviors.tripal_cvb = {
    attach: function(context, settings) {
/******************************************************************************/
$(function() {

    Drupal.tripal_cvb.initCVTreeNodes();
  
});
/******************************************************************************/
    }
  };
})(jQuery);