var upd_wf_intvl = {};
var upd_data_intvl = {};
$(document).ready(function() {
  $('.dl-instance').each(function() {
    upd_wf_intvl[$(this).attr("id")] = false;
    upd_data_intvl[$(this).attr("id")] = false;
  });
});

function dlfldigi_call(instance, key, value1, value2) {
  
  var reqdata = "";
  if (typeof(key) == "object") {
    reqdata = "key=" + key.join(",");
  } else {
    reqdata = "key=" + key + (typeof(value1) != "undefined" ? "&value1=" + value1 : "") + (typeof(value2) != "undefined" ? "&value2=" + value2 : "");
  }
  reqdata = "inst=" + instance + "&" + reqdata;
  // console.log(print_r(reqdata, true));

  $.ajax({
    url: "./xmlrpc/",
    data: reqdata,
    dataType: "json",
    timeout: 3000,
    beforeSend: function() {},
    success: function(response) {
      // console.log(print_r(response, true));
      for (var key in response) {
        var value = response[key];
        // alert(key + "=" + value);
        var field_id = "." + key.replace(".", "_");
        field_id = "#" + instance + " " + field_id;
        // console.log("field_id=" + field_id);
        
        if (key == "rx.get_data" || key == "text.get_rx") {
          var rx = value;
          // console.log("rx.get_data=" + rx);
          //rx = unescape(decodeURIComponent(rx));
          // rx = rx.replace("<", "&lt;");
          rx = $.base64.decode(rx);
          $(field_id).append(rx);
          if (!$(field_id).is(":focus"))
            $(field_id).scrollTop($(field_id)[0].scrollHeight);
          // else 
            // $("#t1").prepend("!scroll ");
        // } else if (key == "main.get_wf_sideband") {
          // $(field_id + ' option:contains("' + value + '")').attr("selected", "selected");
          // $(field_id + ' option[value="' + value + '"]').attr("selected", "selected");
          // console.log(field_id + ' option:contains("' + value + '")');
        } else if (key == "text.clear_rx") {
          // alert(key);
          // $('.text_get_rx').val("");
          // $('.rx_get_data').val("");
          $(field_id + ' .rx_get_data').text("");
        } else {
          // console.log(key + "=" + typeof(value));
          if (typeof(value) == "boolean") {
            // if (value) value = 1;
            // else value = 0;
            // value = value.toString();
            if (key == "main.get_rsid" || key == "main.get_afc") {
              $(field_id).attr("data-enabled", value.toString())
              if (value) {
                $(field_id + " span").addClass("button-enabled");
              } else {
                $(field_id + " span").removeClass("button-enabled");
              }
            }
          }
          $(field_id).val(value);
        }
      }
    },
  });
}



function upd_wf(inst) {
  $("#" + inst + " .wf").each(function() {
    var isrc = $(this).attr("data-src") + "?" + Date.now();
    $(this).attr("src", isrc);
  });
}
function upd_data(inst) {
  dlfldigi_getData(inst);
}


function dlfldigi_getData(inst) {
  var calls = new Array(
    "modem.get_name",
    "modem.get_carrier",
    "main.get_status1",
    "main.get_status2",
    "main.get_wf_sideband",
    "main.get_frequency",
    "main.get_rsid",
    "main.get_afc",
    // "main.get_reverse"//,
    // "rig.get_bandwidth"
    "rx.get_data"
  );
  dlfldigi_call(inst, calls);

  ////dlfldigi_call("text.get_rx_length");
  // var rx = $('.text_get_rx').val();
  // var rxl = rx.length;
  // console.log("len=" + rxl);
  // dlfldigi_call("text.get_rx", rxl);
}
function getInstanceId(jQthis) {
  return jQthis.closest(".dl-instance").attr("id");
}


var valMap = [0, 1, 2, 5, 10, 15, 30, 60];
function _slider_upd_val(inst, upd_type, ui) {
  $("#" + inst + " .upd_" + upd_type + "_value").html(valMap[ui.value] == 0 ? "off" : valMap[ui.value] + "s");
  $("#" + inst + " .upd_" + upd_type).val(valMap[ui.value]).trigger("change");
}
$(document).ready(function() {
  $(".slider-range").each(function() {
    var dl_inst = getInstanceId($(this));
    var upd_type = $(this).parent().attr("data-upd-type");

    $(this).slider({
      min: 0,
      max: valMap.length - 1,
      value: 0,
      slide: function(event, ui) { _slider_upd_val(dl_inst, upd_type, ui); }
    });
  });
});


$(document).ready(function() {

  $(".modem_inc_carrier_ctrl").bind("click", function() {
    var dir = $(this).attr("data-dir");
    var step = $(".modem_inc_carrier_step option:selected").val();
    if (dir == "dec") step = "-" + step;
    // alert(t + v);
    dlfldigi_call(getInstanceId($(this)), "modem.inc_carrier", step);
    setTimeout(function() {
      dlfldigi_call(getInstanceId($(this)), "modem.get_carrier");
    }, 100);
    return false;
  });
  $(".modem_get_name").bind("change", function() {
    dlfldigi_call(getInstanceId($(this)), "modem.set_by_name", $(this).val());
    setTimeout(function() {
      dlfldigi_call(getInstanceId($(this)), "modem.get_name");
    }, 100);
    return false;
  });
  $(".main_get_wf_sideband").bind("change", function() {
    // alert("main_get_wf_sideband");
    dlfldigi_call(getInstanceId($(this)), "main.set_wf_sideband", $(this).val());
    setTimeout(function() {
      dlfldigi_call(getInstanceId($(this)), "main.get_wf_sideband");
    }, 100);
    return false;
  });

  $(".main_get_rsid").bind("click", function() {
    var v = $(this).attr("data-enabled");
    if (v == "true") v = false; else v = true;
    dlfldigi_call(getInstanceId($(this)), "main.set_rsid", v);
    setTimeout(function() {
      dlfldigi_call(getInstanceId($(this)), "main.get_rsid");
    }, 100);
    return false;
  });
  $(".main_get_afc").bind("click", function() {
    var v = $(this).attr("data-enabled");
    if (v == "true") v = false; else v = true;
    dlfldigi_call(getInstanceId($(this)), "main.set_afc", v);
    setTimeout(function() {
      dlfldigi_call(getInstanceId($(this)), "main.get_afc");
    }, 100);
    return false;
  });
  
  $(".text_clear_rx").bind("click", function() {
    dlfldigi_call(getInstanceId($(this)), "text.clear_rx");
    // $('.text_clear_rx').text();
    return false;
  });

  $('.upd_wf').change(function() {
    var inst = getInstanceId($(this));
    // console.log("upd_wf=" + inst);

    clearInterval(upd_wf_intvl[inst]);
    // var v = $("#" + inst + " .upd_wf option:selected").val();
    var v = $("#" + inst + " .upd_wf").val();
    // console.log("upd_wf=" + v);
    if (v > 0) {
      upd_wf(inst);
      upd_wf_intvl[inst] = setInterval(function() {
        upd_wf(inst);
      }, 1000 * v);      
    }
  });
  $('.upd_data').change(function() {
    var inst = getInstanceId($(this));
    
    clearInterval(upd_data_intvl[inst]);
    // var v = $("#" + inst + " .upd_data option:selected").val();
    var v = $("#" + inst + " .upd_data").val();
    if (v > 0) {
      upd_data(inst);
      upd_data_intvl[inst] = setInterval(function() {
        upd_data(inst);
      }, 1000 * v);      
    }
    // console.log(print_r(upd_data_intvl, true));
  });
  
  
  $(".wf-container").click(function(event) {
    var inst = getInstanceId($(this));
    var clickOffsetX = event.pageX - $(this).offset().left;
    dlfldigi_call(inst, "modem.set_carrier", clickOffsetX * 4);
    setTimeout(function() {
      dlfldigi_call(inst, "modem.get_carrier");
    }, 100);
    event.preventDefault();
    //$("#t1").prepend("c="+(clickOffsetX*4+10) + " / ");
  });
  $(".wf-container").mousemove(function(event) {
    var inst = getInstanceId($(this));
    var elOffsetX = $(this).offset().left;
    var clickOffsetX = event.pageX - elOffsetX;
    $("#" + inst + " .wf-carrier").show().css("margin-left", (clickOffsetX-1) + "px").height($("#" + inst + " .wf-container").height());
  });
  $(".wf-container").mouseout(function() {
    var inst = getInstanceId($(this));
    $("#" + inst + " .wf-carrier").hide();
  });

});


//--------------------------------------------

function print_r (array, return_val) {
    // http://kevin.vanzonneveld.net
    // +   original by: Michael White (http://getsprink.com)
    // +   improved by: Ben Bryan
    // +      input by: Brett Zamir (http://brett-zamir.me)
    // +      improved by: Brett Zamir (http://brett-zamir.me)
    // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // -    depends on: echo
    // *     example 1: print_r(1, true);
    // *     returns 1: 1
    var output = '',
        pad_char = ' ',
        pad_val = 4,
        d = this.window.document,
        getFuncName = function (fn) {
            var name = (/\W*function\s+([\w\$]+)\s*\(/).exec(fn);
            if (!name) {
                return '(Anonymous)';
            }
            return name[1];
        },
        repeat_char = function (len, pad_char) {
            var str = '';
            for (var i = 0; i < len; i++) {
                str += pad_char;
            }
            return str;
        },
        formatArray = function (obj, cur_depth, pad_val, pad_char) {
            if (cur_depth > 0) {
                cur_depth++;
            }

            var base_pad = repeat_char(pad_val * cur_depth, pad_char);
            var thick_pad = repeat_char(pad_val * (cur_depth + 1), pad_char);
            var str = '';

            if (typeof obj === 'object' && obj !== null && obj.constructor && getFuncName(obj.constructor) !== 'PHPJS_Resource') {
                str += 'Array\n' + base_pad + '(\n';
                for (var key in obj) {
                    if (Object.prototype.toString.call(obj[key]) === '[object Array]') {
                        str += thick_pad + '[' + key + '] => ' + formatArray(obj[key], cur_depth + 1, pad_val, pad_char);
                    }
                    else {
                        str += thick_pad + '[' + key + '] => ' + obj[key] + '\n';
                    }
                }
                str += base_pad + ')\n';
            }
            else if (obj === null || obj === undefined) {
                str = '';
            }
            else { // for our "resource" class
                str = obj.toString();
            }

            return str;
        };

    output = formatArray(array, 0, pad_val, pad_char);

    if (return_val !== true) {
        if (d.body) {
            this.echo(output);
        }
        else {
            try {
                d = XULDocument; // We're in XUL, so appending as plain text won't work; trigger an error out of XUL
                this.echo('<pre xmlns="http://www.w3.org/1999/xhtml" style="white-space:pre;">' + output + '</pre>');
            } catch (e) {
                this.echo(output); // Outputting as plain text may work in some plain XML
            }
        }
        return true;
    }
    return output;
}