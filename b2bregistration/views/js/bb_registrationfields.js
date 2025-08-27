/**
 * DISCLAIMER
 *
 * Do not edit or add to this file.
 * You are not authorized to modify, copy or redistribute this file.
 * Permissions are reserved by FME Modules.
 *
 *  @author    FME Modules
 *  @copyright 2021 FME Modules
 *  @license   Comerical Licence
 *  @package   b2bregistration
 */

var img_types = ['jpg', 'jpeg', 'png', 'bmp', 'gif'];
var empty_img = 'modules/registrationfields/views/img/empty.png';
var rf_required_fields = ".rf_input_wrapper input:checkbox, .rf_input_wrapper input:radio, .rf_input_wrapper select";
var rf_to_find = 'input[type="checkbox"]:checked, input[type="radio"]:checked, select option:selected';
$(document).ready(function() {
  reCrawlOpts();
});

$(document).on("change", ".rf_input_wrapper select", function(e) {
  checkDependency($(this));
});

$(document).on("click", ".rf_input_wrapper input:checkbox, .rf_input_wrapper input:radio", function(e) {
  checkDependency($(this));
});

$(document).on('click','.image_container', function() {
    if ($(this).siblings().hasClass('image_input')) {
      $(this).siblings('.image_input').click();
    } else {
      $(this).siblings('.uploader').find('.image_input').click();
    }
});

$(document).on('change', '.image_input', function(event){
    var ext = (typeof $(this).data('extensions') !== 'undefined' && $(this).data('extensions'))? $(this).data('extensions').toLowerCase().split(',') : '';
    var result = array_intersect(ext, img_types);
    $(this).parent().find('.extension_error').hide();
    if (typeof result !== 'undefined' && result.length) {
        if (this.files && this.files[0] && this.files[0].type) {
            var mime = this.files[0].type.split("/").pop();
            if (jQuery.inArray(mime, ext) >= 0) {
                readURL(this)
            } else {
                $('#preview-' + $(this).data('id')).attr('src', empty_img);
                if ($(this).parent().find('.extension_error').hasClass('extension_error')) {
                  $(this).parent().find('.extension_error').show();
                } else {
                  $(this).parent().parent().find('.extension_error').show();
                }
            }
        }
    }
});

$(document).on('click','#updateFields', function(e) {
    var rf_is_checked = checkRequiredBoxes();
    if (rf_is_checked.length && $.inArray(false, rf_is_checked) >= 0) {
      e.preventDefault();
      return false;
    }
});

function reCrawlOpts() {
  $(rf_required_fields).each(function(e) {
      dependanceLookup($(this));
    });
    checkNlevelDependency();
  return false;
}
 
function checkNlevelDependency() {
  $('.rf_no_display').each(function(e) {
    var id = $(this).attr('data-id');
    if ($(this).css('display') == 'none') {
      $('[data-f="' + id + '"]').hide();
      $('[data-f="' + id + '"]').find("input.rf_checkboxes").val(1);
    }
  });
}

function readURL(input) {
    var reader = new FileReader();
    reader.onload = function (e) {
        $('#preview-' + $(input).data('id')).attr('src', e.target.result);
    }
    reader.readAsDataURL(input.files[0]);
}

function checkRequiredBoxes() {
  var is_checked = [];
  $('.rf_error_wrapper').remove();
  $('.rf_checkboxes').each(function(e) {
    if ($(this).attr('data-required') == 1 && $(this).val() <= 0) {
      var parentDiv = $(this).closest('.rf_input_wrapper');
      var field_label = parentDiv.find('.rf_input_label').text();
      parentDiv.prepend('<div class="error alert alert-danger rf_error_wrapper">'
        + field_label + is_required_label
        + '</div>');
      is_checked.push(false);
    }
  });
  return is_checked;
}

/**
 * find array intersection
 * @return array
 */
function array_intersect() {
  var i, all, shortest, nShortest, n, len, ret = [], obj={}, nOthers;
  nOthers = arguments.length-1;
  nShortest = arguments[0].length;
  shortest = 0;
  for (i=0; i<=nOthers; i++){
    n = arguments[i].length;
    if (n<nShortest) {
      shortest = i;
      nShortest = n;
    }
  }

  for (i=0; i<=nOthers; i++) {
    n = (i===shortest)?0:(i||shortest); //Read the shortest array first. Read the first array instead of the shortest
    len = arguments[n].length;
    for (var j=0; j<len; j++) {
        var elem = arguments[n][j];
        if(obj[elem] === i-1) {
          if(i === nOthers) {
            ret.push(elem);
            obj[elem]=0;
          } else {
            obj[elem]=i;
          }
        }else if (i===0) {
          obj[elem]=0;
        }
    }
  }
  return ret;
}

function checkDependency(object) {
  var nbr_checkedboxes = object.closest(".rf_input_wrapper").find(rf_to_find).length;
  object.closest(".rf_input_wrapper").find(".rf_checkboxes").val(nbr_checkedboxes);
  dependanceLookup(object, true);
}

function getBooleanVal(val) {
	return parseInt(val);//((val == 'Yes')? 1 : 0);
}

// function dependanceLookup(_el, ev = false) {
//   if (_el.is("input:radio") || _el.is("input:checkbox") || _el.is("select")) {
//     var __cf_id = _el.closest('.rf_input_wrapper').attr("data-id");
//     var __f_id = _el.attr("data-field");
//     var __f_type = _el.attr("data-type");
//     var _req_input = $(".rf_only_f_" + __f_id).find("input.is_required");
//     var __v_id = _el.val();
//     var _checkedBoxes = [];

//     if (__f_type == 'boolean') {
//       __v_id = getBooleanVal(_el.val());
//     } else if (__f_type == 'select') {
//       __v_id = $(_el).find(":selected").val();
//     } else if ($.inArray(__f_type, ['radio', 'checkbox']) >= 0) {
//       __v_id = $("input[name='" + _el.attr('name') + "']:checked").val();
//       if (__f_type == 'checkbox') {
//         $.each($("input[name='" + _el.attr('name') + "']:checked"), function(){
//           _checkedBoxes.push($(this).val());
//         });
//       }
//     };

//     var __elem_target = $('.rf_no_display_' + __f_id + '_' + __v_id);

//     if (_el.is(":checked") || (_el.has("option:selected") && _el.is("select"))) {
//       if (_el.is("select") && __elem_target.length > 0) {
//         $(".rf_only_f_" + __f_id).hide();
//         //_req_input.attr("checked", false);
//         var __rf_children_target = parseInt(_req_input.attr("data-field"));
//         if (__rf_children_target > 0) {
//           $(".rf_only_f_" + __rf_children_target).hide();
//         }
//       } else if (_el.is("input:radio") && __elem_target.length > 0) {
//         $(".rf_only_f_" + __f_id).hide();
//         //_req_input.attr("checked", false);
//         var __rf_children_target = parseInt(_req_input.attr("data-field"));
//         if (__rf_children_target > 0) {
//           $(".rf_only_f_" + __rf_children_target).hide();
//         }
//       }
//       __elem_target.show();
//       __elem_target.find("input.rf_checkboxes").val(0);
//       if (_el.is("input:radio") && __elem_target.length < 1) {
//         $(".rf_only_f_" + __f_id).hide();
//         //_req_input.attr("checked", false);
//         var __rf_children_target = parseInt(_req_input.attr("data-field"));
//         if (__rf_children_target > 0) {
//           $(".rf_only_f_" + __rf_children_target).hide();
//         }
//       } else if (_el.is("select") && __elem_target.length < 1) {
//         $(".rf_only_f_" + __f_id).hide();
//         $(".rf_only_f_" + __f_id).find("select").val(0);
//       }

//       // handle dependent checkboxes
//       if (__f_type == 'checkbox') {
//         var _dependent_checkbox = $('[data-f="' + __cf_id + '"]');
//         var _dependent_val = _dependent_checkbox.closest('.rf_input_wrapper').attr('data-v');
//         if (typeof __v_id == 'undefined' || (_checkedBoxes.length && $.inArray(_dependent_val, _checkedBoxes) === -1)) {
//           _dependent_checkbox.hide();
//         } else {
//           _dependent_checkbox.show();
//         }
//       }
//     } else {
//       __elem_target.hide();

//       //_req_input.attr("checked", false);
//       __elem_target.find("input.rf_checkboxes").val(1);
//       var ___rf_children_target = parseInt(_req_input.attr("data-field"));
//       if (___rf_children_target > 0) {
//         $(".rf_only_f_" + ___rf_children_target).hide();
//       }

//       // handle dependent checkboxes
//       if (__f_type == 'checkbox') {
//         var _dependent_checkbox = $('[data-f="' + __cf_id + '"]');
//         var _dependent_val = _dependent_checkbox.closest('.rf_input_wrapper').attr('data-v');
//         if (typeof __v_id == 'undefined' || (_checkedBoxes.length && $.inArray(_dependent_val, _checkedBoxes) === -1)) {
//           _dependent_checkbox.hide();
//         } else {
//           _dependent_checkbox.show();
//         }
//       }

//     }
//   }
// }
function dependanceLookup(_el, ev) {
  ev = (typeof ev === 'undefined')? false : ev;

  if (_el.is("input:radio") || _el.is("input:checkbox") || _el.is("select")) {
    var _cf_id = _el.closest('.rf_input_wrapper').attr("data-id");
    var _f_id = _el.attr("data-field");
    var _f_type = _el.attr("data-type");
    var req_input = $(".rf_only_f_" + _f_id).find("input.is_required");
    var _v_id = _el.val();
    var _checkedBoxes = [];

    if (_f_type == 'boolean') {
      _v_id = getBooleanVal(_el.val());
    } else if (_f_type == 'select') {
      _v_id = _el.find(":selected").val();
    } else if (_f_type === 'radio' || _f_type === 'checkbox') {
      _v_id = $("input[name='" + _el.attr('name') + "']:checked").val();
      if (_f_type === 'checkbox') {
        $("input[name='" + _el.attr('name') + "']:checked").each(function() {
          _checkedBoxes.push($(this).val());
        });
      }
    }

    var _elem_target = $('.rf_no_display_' + _f_id + '_' + _v_id);

    if (_el.is(":checked") || (_el.has("option:selected") && _el.is("select"))) {
      // Hide all elements related to the field before showing the specific one
      $(".rf_only_f_" + _f_id).hide();
      _elem_target.show();
      _elem_target.find("input.rf_checkboxes").val(0);

      // Additional logic for radios and selects
      if (_f_type === 'radio' || _f_type === 'select') {
        $(".rf_only_f_" + _f_id).find("input.is_required").prop("checked", false);
        $(".rf_only_f_" + _f_id).find("select").val(0);
      }

      // Handle dependent checkboxes visibility
      if (_f_type === 'checkbox') {
        $('[data-f="' + _f_id + '"]').each(function() {
          var _dependent_val = $(this).attr('data-v');
          var shouldShow = _checkedBoxes.includes(_dependent_val);

          if (shouldShow) {
            $(this).show();
          } else {
            $(this).hide();
          }
        });
      }
    } else {
      _elem_target.hide();
      _elem_target.find("input.rf_checkboxes").val(1);
      $(".rf_only_f_" + _f_id).hide();
      $(".rf_only_f_" + _f_id).find("input.is_required").prop("checked", false);

      // Ensure dependent checkboxes are hidden if they don't match checked ones
      if (_f_type === 'checkbox') {
        $('[data-f="' + _f_id + '"]').each(function() {
          var _dependent_val = $(this).attr('data-v');
          if (_checkedBoxes.includes(_dependent_val)) {
            $(this).show();
          } else {
            $(this).hide();
          }
        });
      }
    }

    // Additional logic for hiding based on display status
    if ($('[data-id="' + _cf_id + '"]').closest('.rf_input_wrapper').css('display') === 'none') {
      $('[data-id="' + _cf_id + '"]').hide();
      $('[data-id="' + _cf_id + '"]').find("input.rf_checkboxes").val(1);
    }
  }
}