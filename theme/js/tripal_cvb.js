/**
 * @file
 * Tripal CV Browser Javascript library
 *
 */
(function ($/*, Drupal, window, document, undefined*/) {
"use strict";

  Drupal.tripal_cvb = Drupal.tripal_cvb || {};

  Drupal.tripal_cvb.getClassId = function (classes, prefix) {
    var re = new RegExp('(?:^|\\s)' + prefix + '(\\S+)');
    var id_match = re.exec(classes);
    return (id_match ? id_match[1] : false);
  }
  
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
    $('.tripal-cvb-has-children > .tripal-cvb-cvterm')
      .not('.tripal-cvb-onclick-processed')
      .addClass('tripal-cvb-onclick-processed')
      .on('click.tripal-cvb-expand', function() {
        var $cvterm_span = $(this);
        var $cvterm_li = $(this).parent();
        var term_classes = $cvterm_li.children('.tripal-cvb-cvterm:first').prop('class');
        var cvterm_id = Drupal.tripal_cvb.getClassId(term_classes, 'tripal-cvb-cvtermid-');
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
            $cvterm_span
              .off('click.tripal-cvb-expand')
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
    $('li.tripal-cvb')
      .not('.tripal-cvb-action-processed')
      .addClass('tripal-cvb-action-processed')
      .each(function(index, element) {
        var browser_classes = $(element).parents('.tripal-cvb-browser:first').prop('class');
        var browser_id = Drupal.tripal_cvb.getClassId(browser_classes, 'tripal-cvb-browser-');
        var term_classes = $(element).children('.tripal-cvb-cvterm:first').prop('class');
        var cvterm_id = Drupal.tripal_cvb.getClassId(term_classes, 'tripal-cvb-cvtermid-');
        var actions = [];
        try {
          actions = eval('tripal_cvb_' + browser_id.replace(/\W/g, '_'));
        }
        catch (error) {
          console.log(error);
        };
        $.each(actions, function(index, action) {
          var $action_element = $('<span class="tripal-cvb-action">' + action.title + '</span>');
          var display_content = function(content) {};
          var get_content = function() {return '';};

          // Get target.
          var target_selector = $action_element;
          var target_match = action.target.match(/(.+):(.+)/);
          var target_type = action.target;
          var target_id = '';
          if (target_match) {
            target_type = target_match[1];
            target_id = target_match[2];
          }
          switch (target_type) {
              case 'term':
                target_selector = $action_element;
                break;

            case 'region':
              var target_selector = '.region-' . target_id;
              if ('content' == target_id) {
                target_selector = '#content';
              }
              break;

              case 'dom':
                target_selector = target_id;
                break;

              default:
                break;
          }

          // Get display function.
          if ('replace' == action.insert) {
            display_content = function(content) {
              $(target_selector).html(content);
            };
          }
          else if ('append' == action.insert) {
            display_content = function(content) {
              $(target_selector).append(content);
            }
          }
          else {
            alert('Unsupported insertion method: ' + action.insert);
          }

          // Get content fetching function.
          var action_parameters = action.action;
          switch (action.type) {
            case 'view':
              get_content = function() {
                var view_match = action_parameters.match(/(.+):(.+)/);
                if (view_match) {
                  var view_name = view_match[1];
                  var display_id = view_match[2];
                  $.ajax({
                    url: Drupal.settings.basePath + '/views/ajax',
                    type: 'post',
                    data: {
                      view_name: view_name,
                      view_display_id: display_id,
                      view_args: cvterm_id,
                    },
                    dataType: 'json',
                    success: function (response) {
                      if (response[1] !== undefined) {
                          display_content(response[1].data);
                      }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                    }
                  });
                }
                else {
                  alert('Invalid view settings!');
                }
              }
              break;

            case 'path':
              // No break here: we use url part.
              action_parameters = Drupal.settings.basePath + '/' + action_parameters;

            case 'url':
              get_content = function() {
                $.ajax({
                  url: action_parameters + cvterm_id,
                  type: 'html',
                  success: function (response) {
                    if (response) {
                      display_content($(response).find('body').html());
                    }
                  },
                  error: function(jqXHR, textStatus, errorThrown) {
                  }
                });
              }
              break;

            case 'js':
              get_content = function() {
                try {
                  eval(action.action + '(' + cvterm_id + ');');
                }
                catch (error) {
                  console.log(error);
                };
              }
              break;

            default:
              break;
          }

          $(element).append(' ').append($action_element);

          if (action.autorun) {
            get_content();
          }
          else {
            $action_element.on('click', get_content);
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