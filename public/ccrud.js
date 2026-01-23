window.addEventListener("load", function() {
  const e = "Pacote cCrud", a = [
    { name: "jQuery", check: () => window.jQuery },
    { name: "Bootstrap 5", check: () => window.bootstrap },
    { name: "jQuery UI", check: () => window.jQuery && window.jQuery.ui },
    { name: "jQuery UI Timepicker", check: () => window.jQuery && window.jQuery.fn.datetimepicker },
    { name: "Select2", check: () => window.jQuery && window.jQuery.fn.select2 },
    { name: "SumoSelect", check: () => window.jQuery && window.jQuery.fn.SumoSelect },
    { name: "jQuery Mask", check: () => window.jQuery && window.jQuery.fn.mask },
    { name: "AlertifyJS", check: () => window.alertify },
    { name: "CKEditor 4", check: () => window.CKEDITOR },
    { name: "Cropper.js", check: () => window.Cropper },
    { name: "Font Awesome", check: () => {
      if (Array.from(document.querySelectorAll('link[rel="stylesheet"]')).some(
        (l) => l.href.includes("font-awesome") || l.href.includes("fontawesome")
      ))
        return !0;
      const c = document.createElement("i");
      c.className = "fa fa-check", c.style.display = "none", document.body.appendChild(c);
      const u = window.getComputedStyle(c).getPropertyValue("font-family");
      return document.body.removeChild(c), u.includes("Font Awesome") || u.includes("FontAwesome");
    } }
  ];
  console.log(`[${e}] Verificando dependências...`);
  let n = !0;
  a.forEach((d) => {
    try {
      if (!d.check())
        throw new Error(`Dependência não encontrada: ${d.name}`);
    } catch {
      n = !1, console.error(
        `[${e}] ERRO: A biblioteca "${d.name}" não foi encontrada. Por favor, garanta que ela seja carregada na sua página ANTES do script do cCrud.`
      );
    }
  }), n ? console.log(`[${e}] Todas as dependências foram carregadas com sucesso.`) : console.error(
    `[${e}] Uma ou mais dependências não foram carregadas. O pacote pode não funcionar como esperado.`
  );
});
var cCrud = {
  config: function(e) {
    return cCrud_config[e] !== void 0 ? cCrud_config[e] : e;
  },
  lang: function(e) {
    return cCrud_config.lang[e] !== void 0 ? cCrud_config.lang[e] : e;
  },
  current_task: null,
  after_task: null,
  current_focus: null,
  current_pos: null,
  parent_container: null,
  close_modal: !1,
  request: function(e, t, a) {
    $.ajax({
      type: "post",
      url: cCrud.config("url"),
      dataType: "html",
      cache: !1,
      data: {
        cCrud: t
      },
      beforeSend: function() {
        $(document).trigger("cCrudbeforerequest", [e, t]), cCrud.close_modal = t.close, cCrud.current_task = t.task, cCrud.current_focus = $("*:focus"), cCrud.after_task = t.after;
      },
      success: function(n) {
        $(".cCrud_result_validation").lenght || $("body").append($("<div>").attr("class", "cCrud_result_validation"));
        var d = n;
        cCrud.check_message(d), cCrud.exception || (cCrud.close_modal == !0 && ($("#cCrud-modal-window").modal("hide"), cCrud.parent_container && (e = cCrud.parent_container, cCrud.parent_container = null), cCrud.close_modal = !1), $(e).html(n), a && a(e));
      },
      error: function(n, d, o) {
        cCrud.show_error(cCrud.lang("undefined_error")), console.log(n.statusText), console.log(n.responseText);
      },
      complete: function(n) {
        $(document).trigger("cCrudafterrequest", [e, t]), cCrud.hide_progress(e);
      }
    });
  },
  modal_request: function(e, t) {
    t.is_modal = !0, cCrud.data2form(t), cCrud.bootstrap_modal("Aguarde", ""), setTimeout(function() {
      el = $("#cCrud-modal-window .modal-content").addClass("cCrud").addClass("cCrud-ajax"), cCrud.request(el, t, cCrud.reinit);
    }, 500);
  },
  init: function(e) {
    $(document).trigger("cCrudinit");
  },
  new_window_request: function(e, t) {
    var a = cCrud.data2form(t), n = window.open("", "cCrud_request", "scrollbars,resizable,height=500,width=900");
    n.document.open(), n.document.write(a), n.document.close(), $(n.document.body).find("form").submit();
  },
  data2form: function(e) {
    var t = '<!DOCTYPE HTML><html><head><meta http-equiv="content-type" content="text/html;charset=utf-8" /></head><body>';
    return t += '<form method="post" action="' + cCrud.config("url") + '">', $.map(e, function(a, n) {
      $.isPlainObject(a) || (t += '<input type="hidden" name="cCrud[' + n + ']" value="' + a + '" />');
    }), t += "</form></body></html>", t;
  },
  unique_check: function(e, t, a) {
    t.unique = {}, t.task = "unique", $(e).find(".cCrud-input[data-unique]").length ? ($(e).find(".cCrud-input[data-unique]").each(function(n, d) {
      t.unique[$(d).attr("name")] = $(d).val();
    }), $.ajax({
      type: "post",
      url: cCrud.config("url"),
      beforeSend: function() {
        cCrud.show_progress(e);
      },
      data: {
        cCrud: t
      },
      dataType: "json",
      success: function(n) {
        if (n.error)
          return $(e).find(n.error.selector).parent().parent().addClass("has-error"), cCrud.show_error(cCrud.lang("unique_error")), !1;
        a && a(e);
      },
      complete: function() {
        cCrud.hide_progress(e);
      },
      error: function(n, d, o) {
        console.log(d), console.log(n.responseText);
      },
      cache: !1
    })) : a && a(e);
  },
  show_progress: function(e) {
    $(e).closest(".cCrud").find(".cCrud-overlay").fadeTo(300, 0.6);
  },
  hide_progress: function(e) {
    $(e).closest(".cCrud").find(".cCrud-overlay").css("display", "none");
  },
  get_container: function(e) {
    return $(e).closest(".cCrud-ajax");
  },
  list_data: function(e, t) {
    var a = {};
    cCrud.validation_error = 0, cCrud.save_editor_content(e), $(e).find(".cCrud-data:not([type='checkbox'])").each(function() {
      cCrud.check_container(this, e) && (a[$(this).attr("name")] = cCrud.prepare_val(this));
    }), $(e).find('.cCrud-data[type="checkbox"]:not([disabled])').each(function() {
      cCrud.check_container(this, e) && $(this).prop("checked") && (a[$(this).attr("name")] = cCrud.prepare_val(this));
    }), t && $.isPlainObject(t) ? $.extend(a, t) : t && $.extend(a, $(t).data()), a.postdata = {};
    var n = a.task == "save";
    n && ($(".is-invalid", e).removeClass("is-invalid"), $(document).trigger("cCrudbeforevalidate", [e])), $('.cCrud-input:not([type="checkbox"],[type="radio"],[disabled])', e).each(function() {
      if (cCrud.check_container(this, e)) {
        var c = cCrud.prepare_val(this);
        a.postdata[$(this).attr("name")] = c;
        var u = $(this).data("required"), l = $(this).data("pattern"), s = $(this).data("plugin"), p = $(this).data("validar");
        n && u && !cCrud.validation_required(c, u) ? cCrud.field_invalid(this) : n && l && s != "formatter" && !cCrud.validation_pattern(c, l) ? cCrud.field_invalid(this) : n && p == "cnpj" && !cCrud.validation_cnpj(c) ? cCrud.field_invalid(this) : n && p == "url" && !cCrud.validation_url(c) && cCrud.field_invalid(this);
      }
    });
    var d = !1, o = !1;
    return $(e).find('.cCrud-input[group-required="true"]:not([type="checkbox"],[type="radio"],[disabled])').each(function() {
      if (cCrud.check_container(this, e)) {
        o = !0;
        var c = cCrud.prepare_val(this);
        a.postdata[$(this).attr("name")] = c, $(this).data("pattern"), cCrud.validation_required(c, 1) && (d = !0);
      }
    }), o && !d && $(e).find('.cCrud-input[group-required="true"]:not([type="checkbox"],[type="radio"],[disabled])').each(function() {
      cCrud.check_container(this, e) && cCrud.field_invalid(this);
    }), $(e).find('.cCrud-input[data-type="checkboxes"]:not([disabled])').each(function() {
      a.postdata[$(this).attr("name")] === void 0 && (a.postdata[$(this).attr("name")] = ""), cCrud.check_container(this, e) && $(this).prop("checked") && (a.postdata[$(this).attr("name")] ? a.postdata[$(this).attr("name")] += "," + cCrud.prepare_val(this) : a.postdata[$(this).attr("name")] = cCrud.prepare_val(this));
    }), $(e).find('.cCrud-input[type="radio"]:not([disabled])').each(function() {
      cCrud.check_container(this, e) && $(this).prop("checked") && (a.postdata[$(this).attr("name")] = cCrud.prepare_val(this));
    }), $(e).find('.cCrud-input[data-type="bool"]:not([disabled])').each(function() {
      cCrud.check_container(this, e) && (a.postdata[$(this).attr("name")] = $(this).prop("checked") ? 1 : 0);
    }), $(e).find(".cCrud-searchdata.cCrud-search-active").each(function() {
      cCrud.check_container(this, e) && (a[$(this).attr("name")] = cCrud.prepare_val(this));
    }), n && $(document).trigger("cCrudaftervalidate", [e, a]), a;
  },
  field_invalid: function(e) {
    cCrud.validation_error = 1, $(e).addClass("is-invalid"), $(e).closest(".form-group").addClass("is-invalid"), $('*[href="#' + $(e).closest(".tab-pane").attr("id") + '"]').addClass("is-invalid");
  },
  list_controls_data: function(e, t) {
    var a = {};
    return $(e).find(".cCrud-data").each(function() {
      cCrud.check_container(this, e) && (a[$(this).attr("name")] = cCrud.prepare_val(this));
    }), a;
  },
  check_container: function(e, t) {
    return $(e).closest(".cCrud-ajax").attr("id") == $(t).attr("id");
  },
  save_editor_content: function(e) {
    if ($(e).find(".cCrud-texteditor").length && (typeof tinyMCE < "u" && tinyMCE.triggerSave(), typeof CKEDITOR < "u"))
      for (instance in CKEDITOR.instances)
        $("#" + instance).length && CKEDITOR.instances[instance].updateElement();
  },
  prepare_val: function(e) {
    switch ($(e).data("type")) {
      case "datetime":
      case "timestamp":
      case "date":
      case "time":
      default:
        return $.trim($(e).val());
    }
  },
  change_filter: function(e, t, a) {
    $(t).find(".cCrud-searchdata").hide().removeClass("cCrud-search-active");
    var n = "";
    switch (e) {
      case "datetime":
      case "timestamp":
      case "date":
      case "time":
        var d = "date";
        break;
      case "bool":
        var d = "bool";
        break;
      case "select":
      case "multiselect":
      case "radio":
      case "checkboxes":
        var d = "dropdown";
        n = '[data-fieldname="' + a + '"]';
        break;
      default:
        var d = "default";
        break;
    }
    $(t).find('.cCrud-searchdata[data-fieldtype="' + d + '"]' + n).show().addClass("cCrud-search-active"), d == "date" && cCrud.init_datepicker_range(e, t);
  },
  init_datepicker_range: function(e, t) {
    if (!$.fn.datetimepicker) {
      console.error("[cCrud] jQuery UI Timepicker Addon não está carregado");
      return;
    }
    if ($(t).find(".cCrud-datepicker-from").data("DateTimePicker") == null && $(t).find(".cCrud-datepicker-to").data("DateTimePicker") == null)
      switch (from = $(t).find(".cCrud-datepicker-from").datetimepicker(), to = $(t).find(".cCrud-datepicker-to").datetimepicker(), e) {
        case "time":
          element.datetimepicker({
            format: cCrud_config.moment_time_format,
            useCurrent: !1
          });
          break;
        case "datetime":
        case "timestamp":
          element.datetimepicker({
            format: cCrud_config.moment_date_format + " " + cCrud_config.moment_time_format,
            useCurrent: !1
          });
          break;
        case "date":
          element.datetimepicker({
            format: cCrud_config.moment_date_format,
            useCurrent: !1
          });
          break;
        case "year":
          element.datetimepicker({
            viewMode: "years",
            format: cCrud_config.moment_year_format,
            useCurrent: !1
          });
          break;
        default:
          cCrud.link_datetime_fields(from, to);
          break;
      }
  },
  init_datepicker: function(e) {
    if (!$.fn.datetimepicker) {
      console.error("[cCrud] jQuery UI Timepicker Addon não está carregado");
      return;
    }
    $(e).find(".cCrud-datepicker").each(function() {
      if ($(this).data("DateTimePicker") == null) {
        var t = $(this), a = $(this).data("type");
        switch (a) {
          case "time":
            t.datetimepicker({
              format: cCrud_config.moment_time_format,
              useCurrent: !1
            });
            break;
          case "datetime":
          case "timestamp":
            t.datetimepicker({
              format: cCrud_config.moment_date_format + " " + cCrud_config.moment_time_format,
              useCurrent: !1
            });
            break;
          case "date":
            t.datetimepicker({
              format: cCrud_config.moment_date_format,
              useCurrent: !1
            });
            break;
          case "year":
            t.datetimepicker({
              viewMode: "years",
              format: cCrud_config.moment_year_format,
              useCurrent: !1
            });
            break;
          default:
            var n = t.data("rangestart"), d = t.data("rangeend");
            cCrud.link_datetime_fields(n, d);
            break;
        }
      }
    });
  },
  link_datetime_fields: function(e, t) {
    e != null && e.data("DateTimePicker") != null && t != null && t.data("DateTimePicker") != null && ($(t).data("DateTimePicker").useCurrent(!1), $(e).on("dp.change", function(a) {
      $(t).data("DateTimePicker").minDate(a.date);
    }), $(t).on("dp.change", function(a) {
      $(e).data("DateTimePicker").maxDate(a.date);
    }));
  },
  init_texteditor: function(e) {
    var t = $(e).find(".cCrud-texteditor:not(.editor-loaded)");
    $(t).length && (cCrud.config("force_editor") || typeof tinyMCE < "u" || typeof CKEDITOR < "u") && ($(t).addClass("editor-loaded").addClass("editor-instance"), typeof tinyMCE < "u" ? tinyMCE.init({
      mode: "textareas",
      editor_selector: "editor-instance",
      height: "250"
    }) : typeof CKEDITOR < "u" && $(".editor-instance").each(function() {
      $(this).data("editor-config") ? CKEDITOR.replace($(this).get(0), { customConfig: $(this).data("editor-config") }) : CKEDITOR.replace($(this).get(0));
    }), $(t).removeClass("editor-instance"));
  },
  upload_file: function(e, t, a) {
    var n = $(e).closest(".cCrud-upload-container");
    t.field = $(e).data("field"), t.oldfile = $(n).find(".cCrud-input").val(), t.task = "upload", t.mode = $(e).closest(".cCrud-ajax").find('.cCrud-data[name="task"]').val(), t.type = $(e).data("type");
    var d = cCrud.get_extension($(e).val());
    if (t.type == "image")
      switch (d.toLowerCase()) {
        case "jpg":
        case "jpeg":
        case "gif":
        case "png":
          break;
        default:
          return cCrud.show_error(cCrud.lang("image_type_error")), $(e).val(""), !1;
      }
    $(document).trigger("cCrudbeforeupload", [a, t]), cCrud.show_progress(a), $.ajaxFileUpload({
      secureuri: !1,
      fileElementId: $(e).attr("id"),
      data: {
        cCrud: t
      },
      url: cCrud.config("url"),
      success: function(o) {
        cCrud.hide_progress(a), $(n).replaceWith(o), $(document).trigger("cCrudafterupload", [a, t, status]);
        var c = $(o).find("img.cCrud-crop");
        $(c).length && cCrud.show_crop_window(c, a);
      },
      error: function() {
        cCrud.hide_progress(a), cCrud.show_error(cCrud.lang("undefined_error"));
      }
    });
  },
  show_crop_window: function(e, t) {
    var a = $(t).find("img.cCrud-crop").closest(".cCrud-upload-container");
    $(e).dialog({
      resizable: !1,
      height: "auto",
      width: "auto",
      modal: !0,
      closeOnEscape: !1,
      buttons: {
        OK: function() {
          var n = cCrud.list_data(t, {
            task: "crop_image"
          });
          $(a).find(".xrud-crop-data").each(function() {
            n[$(this).attr("name")] = $(this).val();
          }), $(document).trigger("cCrudbeforeecrop", [t, n]), cCrud.show_progress(t), $.ajax({
            data: {
              cCrud: n
            },
            success: function(d) {
              cCrud.hide_progress(t), $(a).replaceWith(d), $(document).trigger("cCrudaftercrop", [t, n]);
            },
            error: function() {
              cCrud.hide_progress(t), cCrud.show_error(cCrud.lang("undefined_error"));
            },
            type: "post",
            url: cCrud.config("url"),
            dataType: "html",
            cache: !1
          }), $(this).dialog("destroy"), $(".cCrud-crop").remove();
        }
      },
      close: function(n, d) {
        var o = cCrud.list_data(t, {
          task: "crop_image"
        });
        $(a).find(".xrud-crop-data").each(function() {
          o[$(this).attr("name")] = $(this).val();
        }), o.w = 0, o.h = 0, cCrud.show_progress(t), $.ajax({
          data: {
            cCrud: o
          },
          success: function(c) {
            cCrud.hide_progress(t), $(a).replaceWith(c);
          },
          error: function() {
            cCrud.hide_progress(t), cCrud.show_error(cCrud.lang("undefined_error"));
          },
          type: "post",
          url: cCrud.config("url"),
          dataType: "html",
          cache: !1
        }), $(this).dialog("destroy"), $(".cCrud-crop").remove();
      },
      open: function(n, d) {
        cCrud.load_image(e.attr("src"), function(o) {
          var c = parseInt($(e).data("width")), u = parseInt($(e).data("height")), l = parseFloat($(e).data("ratio")), s = {};
          s.boxWidth = c, s.boxHeight = u, u > 500 && (s.boxHeight = 500, s.boxWidth = Math.round(c * 500 / u)), s.boxWidth > 550 && (s.boxWidth = 550, s.boxHeight = Math.round(u * 550 / c));
          var p = Math.round(($(window).width() - $(".ui-dialog.ui-widget").width()) / 2), f = Math.round(($(window).height() - $(".ui-dialog.ui-widget").height()) / 2);
          $(".ui-dialog.ui-widget").css({
            position: "fixed",
            left: p + "px",
            top: f + "px"
          }), s.minSize = [50, 50], l && (s.aspectRatio = l), s.onChange = cCrud.get_coordinates, s.keySupport = !1, s.trueSize = [c, u];
          var m = c / 4, C = u / 4, h = m * 3, g = C * 3;
          s.setSelect = [m, C, h, g], s.allowSelect = !1, $(".ui-dialog img.cCrud-crop").Jcrop(s);
        });
      }
    });
  },
  load_image: function(e, t) {
    var a = new Image();
    a.src = e, a.complete ? t && t(a) : ($(document).trigger("startload"), a.onload = function() {
      $(document).trigger("stopload"), t && t(a);
    }, a.onerror = function() {
      $(document).trigger("stopload"), t && t(!1);
    });
  },
  remove_file: function(e, t, a) {
    var n = $(e).closest(".cCrud-upload-container");
    t.field = $(e).data("field"), t.file = $(n).find(".cCrud-input").val(), t.task = "remove_upload", cCrud.show_progress(a), $.ajax({
      data: {
        cCrud: t
      },
      success: function(d) {
        cCrud.hide_progress(a), $(n).replaceWith(d);
      },
      type: "post",
      url: cCrud.config("url"),
      dataType: "html",
      cache: !1,
      error: function() {
        cCrud.hide_progress(a), cCrud.show_error(cCrud.lang("undefined_error"));
      }
    });
  },
  get_coordinates: function(e) {
    $(".cCrud").find("input.xrud-crop-data[name=x]").val(Math.round(e.x)), $(".cCrud").find("input.xrud-crop-data[name=y]").val(Math.round(e.y)), $(".cCrud").find("input.xrud-crop-data[name=x2]").val(Math.round(e.x2)), $(".cCrud").find("input.xrud-crop-data[name=y2]").val(Math.round(e.y2)), $(".cCrud").find("input.xrud-crop-data[name=w]").val(Math.round(e.w)), $(".cCrud").find("input.xrud-crop-data[name=h]").val(Math.round(e.h));
  },
  validation_url: function(e) {
    if (e == "")
      return !0;
    var t = new RegExp(
      "^(http|https|ftp)://([a-zA-Z0-9.-]+(:[a-zA-Z0-9.&amp;%$-]+)*@)*((25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9]).(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9]|0).(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9]|0).(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[0-9])|([a-zA-Z0-9-]+.)*[a-zA-Z0-9-]+.(com|edu|gov|int|mil|net|org|biz|arpa|info|name|pro|aero|coop|museum|[a-zA-Z]{2}))(:[0-9]+)*(/($|[a-zA-Z0-9.,?'\\+&amp;%$#=~_-]+))*$"
    );
    return t.test(e);
  },
  validation_required: function(e, t) {
    return $.trim(e).length >= t;
  },
  validation_pattern: function(e, t) {
    if (e === "")
      return !0;
    switch (t) {
      case "email":
        return reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/, reg.test($.trim(e));
      case "alpha":
        return reg = /^([a-z])+$/i, reg.test($.trim(e));
      case "alpha_numeric":
        return reg = /^([a-z0-9])+$/i, reg.test($.trim(e));
      case "alpha_dash":
        return reg = /^([-a-z0-9_-])+$/i, reg.test($.trim(e));
      case "numeric":
        return reg = /^[\-+]?[0-9]*(\.|\,)?[0-9]+$/, reg.test($.trim(e));
      case "integer":
        return reg = /^[\-+]?[0-9]+$/, reg.test($.trim(e));
      case "decimal":
        return reg = /^[\-+]?[0-9]+(\.|\,)[0-9]+$/, reg.test($.trim(e));
      case "point":
        return reg = /^[\-+]?[0-9]+\.{0,1}[0-9]*\,[\-+]?[0-9]+\.{0,1}[0-9]*$/, reg.test($.trim(e));
      case "natural":
        return reg = /^[0-9]+$/, reg.test($.trim(e));
      default:
        return reg = new RegExp(t), reg.test($.trim(e));
    }
  },
  pattern_callback: function(e, t) {
    var a = $(t).data("pattern");
    if (a) {
      var n = e.which;
      if (n < 32 || e.ctrlKey || e.altKey)
        return !0;
      var d = String.fromCharCode(n);
      switch (a) {
        case "alpha":
          return reg = /^([a-z])+$/i, reg.test(d);
        case "alpha_numeric":
          return reg = /^([a-z0-9])+$/i, reg.test(d);
        case "alpha_dash":
          return reg = /^([-a-z0-9_-])+$/i, reg.test(d);
        case "numeric":
        case "integer":
        case "decimal":
        case "point":
          return reg = /^[0-9\.\,\-+]+$/, reg.test(d);
        case "natural":
          return reg = /^[0-9]+$/, reg.test(d);
      }
    }
    return !0;
  },
  validation_error: !1,
  get_extension: function(e) {
    var t = e.split(".");
    return t[t.length - 1];
  },
  check_fixed_buttons: function() {
    return null;
  },
  block_query: {},
  depend_init: function(e) {
    $(e).off("change.depend");
    var t = {};
    $(e).find(".cCrud-input[data-depend]").each(function() {
      var a = cCrud.get_container(this), n = cCrud.list_controls_data(a, this), d = $(this).data("depend");
      n.task = "depend", n.name = $(this).attr("name"), n.value = $(this).val(), $(a).on("change.depend", '.cCrud-input[name="' + d + '"]', function() {
        cCrud.check_container(this, a) && (n.dependval = $(this).val(), cCrud.depend_query(n, d, a));
      }), d && (t[d] = d);
    }), $.map(t, function(a, n) {
      window.setTimeout(function() {
        $(e).find('.cCrud-input[name="' + a + '"]:not([data-depend])').trigger("change.depend");
      }, 100);
    });
  },
  depend_query: function(e, t, a) {
    if (!cCrud.block_query[e.name + t]) {
      cCrud.block_query[e.name + t] = 1;
      var n = $(a).find('.cCrud-input[name="' + e.name + '"]'), d = n.parent(), o = n.val();
      $(d).trigger("cCrudbeforedepend", [a, e]), $.ajax({
        data: {
          cCrud: e
        },
        type: "post",
        url: cCrud.config("url"),
        success: function(c) {
          n.select2("destroy").remove(), d.css("visibility", "hidden").append(c), n = $(d).find('.cCrud-input[name="' + e.name + '"]'), o !== null && n.val(o), $(d).trigger("cCrudafterdepend", [a, e]), window.setTimeout(function() {
            cCrud.jr_request($(a).find('.cCrud-input[name="' + e.name + '"]')), cCrud.block_query[e.name + t] = 0;
          }, 400), n.select2(), $(d).css("visibility", "visible");
        },
        cache: !1
      });
    }
  },
  init_autosave: function() {
    return !1;
  },
  parse_latlng: function(e) {
    var t = e.split(",");
    if (t.length != 2)
      return null;
    var a = new google.maps.LatLng(parseFloat(t[0]), parseFloat(t[1]));
    return a;
  },
  create_map: function(e, t, a, n) {
    var d = {
      zoom: a,
      center: t,
      mapTypeId: google.maps.MapTypeId[n]
    }, o = new google.maps.Map($(e)[0], d);
    return o;
  },
  place_marker: function(e, t, a, n, d) {
    var o = new google.maps.Marker({
      position: t,
      map: e,
      animation: google.maps.Animation.DROP,
      draggable: !!a
    });
    return n && google.maps.event.addListener(o, "click", function() {
      var c = this, u = new google.maps.InfoWindow({
        maxWidth: 320
      });
      u.setContent('<p class="cCrud-infowinow">' + n + "</p>"), u.open(e, c);
    }), a && $(d).length && (google.maps.event.addListener(o, "dragend", function() {
      $(d).val(this.getPosition().lat() + "," + this.getPosition().lng());
    }), google.maps.event.addListener(e, "click", function(c) {
      o.setPosition(c.latLng), $(d).val(o.getPosition().lat() + "," + o.getPosition().lng());
    })), o;
  },
  move_marker: function(e, t, a, n, d) {
    return t ? t.setPosition(a) : this.place_marker(e, a, n, d), e.setCenter(a), t;
  },
  find_point: function(e, t) {
    return this.geocode({
      address: e
    }, t);
  },
  find_address: function(e, t) {
    return this.geocode({
      latLng: e
    }, t);
  },
  geocode: function(e, t, a) {
    var n = new google.maps.Geocoder();
    n.geocode(e, function(d, o) {
      var c = {};
      if (o == google.maps.GeocoderStatus.OK) {
        for (var u = 0; u < d.length; u++)
          if (d[u].formatted_address && (c[u] = {}, c[u].lat = d[u].geometry.location.lat(), c[u].lng = d[u].geometry.location.lng(), c[u].address = d[u].formatted_address, a))
            return a(c[u]);
        t && t(c);
      }
    });
  },
  map_instances: [],
  marker_instances: [],
  map_init: function(e) {
    cCrud.map_instances = [], $(e).find(".cCrud-map").each(function() {
      var t = this, a = $(t).parent().children('input[data-type="point"]'), n = $(t).parent().children(".cCrud-map-search"), d = cCrud.parse_latlng($(a).val()), o = cCrud.create_map(t, d, $(t).data("zoom"), "ROADMAP"), c = cCrud.place_marker(o, d, $(t).data("draggable"), $(t).data("text"), a);
      $(a).on("keyup", function() {
        var u = cCrud.parse_latlng($(a).val());
        return cCrud.move_marker(o, c, u, $(t).data("draggable"), $(t).data("text")), !1;
      }), $(n).length && $(n).on("keyup", function() {
        var u = $.trim($(n).val());
        return u && cCrud.find_point(u, function(l) {
          cCrud.map_dropdown(n, l, o, c, a, t);
        }), !1;
      }), cCrud.map_instances.push(o), cCrud.marker_instances.push(c);
    });
  },
  map_dropdown: function(e, t, a, n, d, o) {
    var c = $(e).outerWidth(), u = $(e).outerHeight(), l = $(e).offset();
    if ($(e).prev(".cCrud-map-dropdown").remove(), t) {
      var s = '<ul class="cCrud-map-dropdown">';
      $.map(t, function(p) {
        s += '<li data-val="' + p.lat + "," + p.lng + '">' + p.address + "</li>";
      }), s += "</ul>", $(e).before(s), $(e).prev(".cCrud-map-dropdown").offset(l).css({
        marginTop: u + "px",
        minWidth: c + "px"
      }).children("li").on("click", function() {
        var p = cCrud.parse_latlng($(this).data("val"));
        return $(e).val($(this).text()), n = cCrud.move_marker(a, n, p, $(o).data("draggable"), $(o).data("text")), $(d).val(n.getPosition().lat() + "," + n.getPosition().lng()), $(this).parent("ul").remove(), !1;
      });
    }
  },
  map_resize_all: function() {
    if ($(".cCrud-map").length && cCrud.map_instances.length)
      for (i = 0; i < cCrud.map_instances.length; i++) {
        var e = cCrud.map_instances[i], t = cCrud.marker_instances[i];
        google.maps.event.trigger(e, "resize"), e.setZoom(e.getZoom()), e.setCenter(t.position);
      }
  },
  reload: function(e) {
    if (e || (e = "body"), $(e).hasClass("cCrud-ajax"))
      var t = $(e);
    else
      var t = $(e).find(".cCrud-ajax");
    t = t.eq(0), t.each(function() {
      var a = cCrud.list_data(this);
      a.active_tab_id = $(".tab-pane.active.nested").attr("data-label"), cCrud.request(this, a);
    });
  },
  bootstrap_modal: function(e, t) {
    $("#cCrud-modal-window").remove(), $("body").append('<div id="cCrud-modal-window" class="modal fade"><div class="modal-dialog modal-lg"><div class="modal-content"></div></div></div>'), $("#cCrud-modal-window .modal-content").html('<div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button><h4 class="modal-title">' + e + "</h4></div>"), $("#cCrud-modal-window .modal-content").append('<div class="modal-body">' + t + "</div>"), $("#cCrud-modal-window").modal({
      keyboard: !1
    }).on("shown.bs.modal", function(a) {
    }), $('#cCrud-modal-window [data-dismiss="modal"]').on("click", function() {
      return $("#cCrud-modal-window").modal("hide"), $(".simplemodal-close").length && ($(".simplemodal-close").trigger("click"), $("#cCrud-modal-window").remove()), !1;
    }), $("#cCrud-modal-window").on("hidden.bs.modal hidden", function() {
      $("#cCrud-modal-window").remove();
    });
  },
  ui_modal: function(e, t) {
    $("#cCrud-modal-window").remove(), $("body").append('<div id="cCrud-modal-window">' + t + "</div>"), $("#cCrud-modal-window").dialog({
      resizable: !1,
      height: "auto",
      width: "auto",
      modal: !0,
      closeOnEscape: !0,
      close: function(a, n) {
        $("#cCrud-modal-window").remove();
      },
      title: e
    });
  },
  modal: function(e, t) {
    t = "<span>" + t + "</span>", typeof $.fn.modal < "u" ? $(t).first().prop("tagName") == "IMG" ? cCrud.load_image($(t).first().attr("src"), function(a) {
      cCrud.bootstrap_modal(e, t);
    }) : cCrud.bootstrap_modal(e, t) : $(t).first().prop("tagName") == "IMG" ? cCrud.load_image($(t).first().attr("src"), function(a) {
      cCrud.ui_modal(e, t);
    }) : cCrud.ui_modal(e, t);
  },
  base64_modal: function(e, t) {
    cCrud.bootstrap_modal(Base64.decode(e), Base64.decode(t));
  },
  init_tabs: function(e) {
    $(e).find(".cCrud-tabs").length && (typeof $.fn.tab < "u" ? ($(e).find(".cCrud-tabs > ul:first > li > a").on("click", function() {
      return $(this).tab("show"), !1;
    }), $(".cCrud .nav-tabs a").on("shown.bs.tab", function(t) {
      cCrud.map_resize_all();
    })) : $(e).find(".cCrud-tabs").tabs({
      activate: function(t, a) {
        cCrud.map_resize_all();
      }
    }));
  },
  init_tooltips: function(e) {
    $(e).find(".cCrud-tooltip").length && $(e).find(".cCrud-tooltip").tooltip();
  },
  show_alert: function(e) {
    alertify.cCrud || alertify.dialog("cCrud", function() {
      return {
        build: function() {
          this.setHeader(cCrud_config.table_name);
        },
        main: function(a) {
          this.message = a;
        },
        setup: function() {
          return {
            buttons: [{
              text: "Ok",
              key: 27
              /*Esc*/
            }],
            focus: { element: 0 }
          };
        },
        prepare: function() {
          this.setContent(this.message);
        }
      };
    }), alertify.cCrud(e);
  },
  show_error: function(e, t = 5) {
    console.log(e), alertify.error(e, t);
  },
  show_message: function(e, t = 5) {
    console.log(texto), alertify.message(texto, t);
  },
  show_success: function(e, t = 5) {
    console.log(e), alertify.success(e, t);
  },
  show_warning: function(e, t = 5) {
    console.log(e), alertify.warning(e, t);
  },
  show_notify: function(e, t, a = 5) {
    console.log(e), alertify.notify(e, t, a);
  },
  check_message: function(e) {
    if (typeof e == "string")
      var t = $(e).filter(".cCrud-callback-message");
    else
      var t = $(e).find(".cCrud-callback-message");
    $(t).length && t.each(function() {
      var a = this;
      cCrud.check_container(a, e) && (texto = $(a).val(), type = $(a).attr("name"), $(a).attr("data-exception") && (cCrud.exception = !0), type == "alert" ? cCrud.show_alert(texto) : cCrud.show_notify(texto, type), $(a).remove());
    });
  },
  init_nestable: function(e) {
  },
  action: function(e) {
    var t = cCrud.get_container(e), a = cCrud.list_data(t, e);
    $(e).hasClass("cCrud-in-new-window") ? cCrud.new_window_request(t, a) : $(e).hasClass("cCrud-in-modal") ? (cCrud.parent_container = t, cCrud.modal_request(t, a)) : a.task == "save" ? cCrud.validation_error ? cCrud.show_message(t, cCrud.lang("validation_error"), "error") : cCrud.unique_check(t, a, function(n) {
      a.task = "save", cCrud.request(n, a, a.callback);
    }) : cCrud.request(t, a);
  },
  jr_request: function(e) {
    var t = cCrud.get_container(e), a = cCrud.list_data(t, e);
    a.task = "join_relation", a.jr_value = e.val(), a.select2 = "", $.ajax({
      type: "post",
      url: cCrud.config("url"),
      beforeSend: function() {
        cCrud.show_progress(t);
      },
      data: {
        cCrud: a
      },
      dataType: "json",
      beforeSend: function(n, d) {
      },
      success: function(n) {
        for (var d in n) {
          var o = $('.cCrud-input[name="' + d + '"]', t);
          o.data("select2") ? o.select2("destroy").replaceWith(n[d]) : o.replaceWith(n[d]);
        }
      },
      complete: function() {
        cCrud.hide_progress(t), $(document).trigger("cCrudafterjoinrelation", [e.closest(".form-horizontal"), a, status]);
      },
      error: function(n, d, o) {
      },
      cache: !1
    });
  },
  init_select2: function(e) {
    if ($.fn.select2) {
      var t = cCrud.get_container(e);
      $("select:not(.cCrud-columns-select):not(.cCrud-searchdata):not(.not_select2):not(.cCrud-columnsList-select)", t).each(function() {
        var a = $.extend({
          width: "100%"
        }, $(this).data());
        if ($(this).hasClass("select2-ajax")) {
          var n = $(this).closest(".cCrud-ajax"), d = $(this).data("depend"), o = cCrud.list_controls_data(n);
          o.dependval = $('.cCrud-input[name="' + d + '"]').val(), o.name = $(this).data("relationajax"), o.task = "relation_search", $.extend(a, {
            ajax: {
              url: cCrud.config("url") + "/cCrud",
              dataType: "json",
              delay: 250,
              type: "POST",
              beforeSend: function(c, u) {
              },
              data: function(c) {
                return {
                  q: c.term,
                  cCrud: o
                };
              },
              processResults: function(c, u) {
                return {
                  results: c.items
                };
              },
              cache: !1
            },
            minimumInputLength: 2
          });
        }
        $(this).select2(a);
      });
    }
  },
  init_checkbox: function(e) {
  },
  init_mask: function(e) {
    $("input[data-mask]", e).each(function() {
      data = $(this).data();
      var t = $.extend({}, data);
      typeof data.mask == "string" && $(this).mask(data.mask, t);
    });
  },
  init_columns_select: function(e) {
    var t = cCrud.list_data(e);
    t.task == "list" && $(".cCrud-columnsList-select", e).SumoSelect({
      okCancelInMulti: !0,
      selectAll: !0
    });
  }
};
$(document).on("cCrudinit", function() {
  $(".cCrud").length && ($(".cCrud").off("change", "select.cCrud-columnsList-select").on("change", "select.cCrud-columnsList-select", function() {
    var e = cCrud.get_container(this), t = cCrud.list_data(e);
    t.task = "change_columns", t.columns = $(this).val(), cCrud.request(e, t);
  }), $(".cCrud").off("change", ".cCrud-actionlist").on("change", ".cCrud-actionlist", function() {
    var e = cCrud.get_container(this), t = cCrud.list_data(e);
    cCrud.request(e, t);
  }), $(".cCrud").off("change", ".cCrud-daterange").on("change", ".cCrud-daterange", function() {
    var e = $(this).parent();
    $(this).val() ? $(e).find(".cCrud-datepicker-from").data("DateTimePicker") != null ? ($(e).find(".cCrud-datepicker-from").data("DateTimePicker").date(new Date($(this).find("option:selected").data("from") * 1e3)), $(e).find(".cCrud-datepicker-to").data("DateTimePicker").date(new Date($(this).find("option:selected").data("to") * 1e3))) : ($(e).find(".cCrud-datepicker-from").datepicker("update", new Date($(this).find("option:selected").data("from") * 1e3)), $(e).find(".cCrud-datepicker-to").datepicker("update", new Date($(this).find("option:selected").data("to") * 1e3))) : $(e).find(".cCrud-datepicker-from,.cCrud-datepicker-to").val("");
  }), $(".cCrud").off("change", ".cCrud-columns-select").on("change", ".cCrud-columns-select", function() {
    var e = $(this).parent(), t = $(this).children("option:selected").data("type"), a = $(this).children("option:selected").val();
    cCrud.change_filter(t, e, a);
  }), $(".cCrud").off("click", ".cCrud-action").on("click", ".cCrud-action", function() {
    var e = $(this), t = $(this).data("confirm");
    return t ? alertify.confirm(cCrud_config.table_name, t, function() {
      cCrud.action(e);
    }, function() {
    }) : cCrud.action(e), !1;
  }), $(".cCrud").off("click", ".cCrud-toggle-show").on("click", ".cCrud-toggle-show", function() {
    var e = $(this).closest(".cCrud").find(".cCrud-container:first"), t = $(this).hasClass("cCrud-toggle-down");
    return t ? ($(e).stop(!0, !0).delay(100).slideDown(200, function() {
      $(document).trigger("cCrudslidedown"), $(e).trigger("cCrudslidedown");
    }), $(this).closest(".cCrud").find(".cCrud-main-tab").slideUp(200)) : ($(e).stop(!0, !0).slideUp(200, function() {
      $(document).trigger("cCrudslideup"), $(e), z.trigger("cCrudslideup");
    }), $(this).closest(".cCrud").find(".cCrud-main-tab").delay(100).slideDown(200)), !1;
  }), $(".cCrud").off("keypress", ".cCrud-input").on("keypress", ".cCrud-input", function(e) {
    return cCrud.pattern_callback(e, this);
  }), $(".cCrud").off("click", ".cCrud-search-toggle").on("click", ".cCrud-search-toggle", function() {
    return $(this).closest(".cCrud-ajax").find(".cCrud-search-toggle").find(".cCrud-searchdata").focus(), !1;
  }), $(".cCrud").off("keydown", ".cCrud-searchdata").on("keydown", ".cCrud-searchdata", function(e) {
    if (e.which == 13) {
      var t = cCrud.get_container(this), a = cCrud.list_data(t);
      return a.search = 1, a.task = "list", cCrud.request(t, a), !1;
    } else e.which == 27 && ($(this).parent().find("a").hasClass("fa-search") ? $(this).parent().find(".cCrud-searchdata").val() === "" || $(this).parent().find(".cCrud-searchdata").val("") : $(this).parent().find("a").hasClass("fa-times") && $(this).parent().find("a").click());
  }), $(".cCrud").off("change", ".cCrud-upload").on("change", ".cCrud-upload", function() {
    var e = cCrud.get_container(this), t = cCrud.list_data(e);
    return cCrud.upload_file(this, t, e), !1;
  }), $(".cCrud").off("click", ".cCrud-remove-file").on("click", ".cCrud-remove-file", function() {
    var e = cCrud.get_container(this), t = cCrud.list_data(e);
    return cCrud.remove_file(this, t, e), !1;
  }), $(".cCrud").off("click", ".cCrud_modal").on("click", ".cCrud_modal", function() {
    var e = $(this).data("content"), t = $(this).data("header");
    return cCrud.modal(t, e), !1;
  }), $(".cCrud").off("change", ".cCrud-mass-select").on("change", ".cCrud-mass-select", function() {
    $(this).val() == 1 ? cCrud.get_container(this).find(".cCrud-mass-form-group").show(150) : cCrud.get_container(this).find(".cCrud-mass-form-group").hide(150);
  }), $(".cCrud").off("change", 'input.cCrud-mass-checkbox-header[type="checkbox"],input.cCrud-mass-checkbox-footer[type="checkbox"]').on("change", 'input.cCrud-mass-checkbox-header[type="checkbox"],input.cCrud-mass-checkbox-footer[type="checkbox"]', function() {
    $(this).is(":checked") ? $('input.cCrud-mass-checkbox[type="checkbox"]', cCrud.get_container(this)).prop("checked", !0) : $('input.cCrud-mass-checkbox[type="checkbox"]', cCrud.get_container(this)).prop("checked", !1);
  }), $(".cCrud").off("change", ".join_relation").on("change", ".join_relation", function() {
    cCrud.jr_request($(this)), cCrud.depend_init(this);
  }), $(".cCrud-ajax").each(function() {
    cCrud.init_datepicker(this), cCrud.init_datepicker_range($(this).find(".cCrud-columns-select option:selected").data("type"), this), cCrud.depend_init(this), cCrud.map_init(this), cCrud.check_fixed_buttons(), cCrud.init_tooltips(this), cCrud.init_tabs(this), cCrud.check_message(this), cCrud.init_autosave(), cCrud.hide_progress(this), cCrud.init_nestable(this), cCrud.init_select2(this), cCrud.init_checkbox(this), cCrud.init_columns_select(this), cCrud.init_mask(this);
  }));
});
$(document).ready(function() {
  cCrud.init();
});
$(window).on("resize load cCrudslidetoggle", function() {
  cCrud.check_fixed_buttons();
});
$(window).on("load", function() {
  $(".cCrud-ajax").each(function() {
    cCrud.init_texteditor(this);
  });
});
$(document).on("cCrudbeforerequest", function(e, t) {
  cCrud.show_progress(t);
});
$(document).on("cCrudafterrequest", function(e, t) {
  cCrud.init_datepicker(t), cCrud.init_texteditor(t), cCrud.init_datepicker_range($(t).find(".cCrud-columns-select option:selected").data("type"), t), cCrud.depend_init(t), cCrud.map_init(t), cCrud.check_fixed_buttons(), cCrud.init_tooltips(t), cCrud.init_tabs(t), cCrud.check_message(t), cCrud.init_nestable(t), cCrud.init_select2(t), cCrud.init_checkbox(t), cCrud.init_mask(t), cCrud.init_columns_select(t);
});
$(document).on("cCrudafterupload", function(e, t) {
  cCrud.check_message(t);
});
$(document).on("cCrudbeforedepend", function(e, t, a) {
  $('select[name="' + a.name + '"]').parent().find(".select2").remove();
});
$(document).on("cCrudafterdepend", function(e, t, a) {
});
$(document).on("cCrudafterjoinrelation", function(e, t) {
  cCrud.init_datepicker(t), cCrud.init_texteditor(t), cCrud.init_datepicker_range($(t).find(".cCrud-columns-select option:selected").data("type"), t), cCrud.map_init(t), cCrud.check_fixed_buttons(), cCrud.init_tooltips(t), cCrud.init_tabs(t), cCrud.check_message(t), cCrud.init_nestable(t), cCrud.init_select2(t), cCrud.init_checkbox(t), cCrud.init_mask(t), cCrud.init_columns_select(t);
});
$.extend({
  print_window: function(e, t) {
    var a = {};
    $(t).find(".cCrud-data").each(function() {
      a[$(this).attr("name")] = $(this).val();
    }), a.task = "print", $.ajax({
      data: a,
      success: function(n) {
        e.document.open(), e.document.write(n), e.document.close(), $(t).find(".cCrud-data[name=key]:first").val($(e.document).find(".cCrud-data[name=key]:first").val());
        var d = navigator.userAgent.toLowerCase();
        d.indexOf("opera") != -1 ? $(e).load(function() {
          e.print();
        }) : $(e).ready(function() {
          e.print();
        });
      }
    });
  }
});
$.extend({
  createUploadIframe: function(e, t) {
    var a = "jUploadFrame" + e, n = '<iframe id="' + a + '" name="' + a + '" style="position:absolute; top:-9999px; left:-9999px"';
    return window.ActiveXObject && (typeof t == "boolean" ? n += ' src="javascript:false"' : typeof t == "string" && (n += ' src="' + t + '"')), n += " />", $(n).appendTo(document.body), $("#" + a).get(0);
  },
  createUploadForm: function(e, t, a) {
    var n = "jUploadForm" + e, d = "jUploadFile" + e, o = $('<form  action="" method="POST" name="' + n + '" id="' + n + '" enctype="multipart/form-data"></form>');
    if (a)
      for (var c in a.cCrud)
        a.cCrud[c] == "postdata" || $('<input type="hidden" name="cCrud[' + c + ']" value="' + a.cCrud[c] + '" />').appendTo(o);
    var u = $("#" + t), l = $(u).clone();
    return $(u).attr("id", d), $(u).before(l), $(u).appendTo(o), $(o).css("position", "absolute"), $(o).css("top", "-1200px"), $(o).css("left", "-1200px"), $(o).appendTo("body"), o;
  },
  ajaxFileUpload: function(e) {
    e = $.extend({}, $.ajaxSettings, e);
    var t = (/* @__PURE__ */ new Date()).getTime(), a = $.createUploadForm(t, e.fileElementId, typeof e.data > "u" ? !1 : e.data);
    $.createUploadIframe(t, e.secureuri);
    var n = "jUploadFrame" + t, d = "jUploadForm" + t;
    e.global && !$.active++ && $.event.trigger("ajaxStart");
    var o = !1, c = {};
    e.global && $.event.trigger("ajaxSend", [c, e]);
    var u = function(p) {
      var f = document.getElementById(n);
      try {
        f.contentWindow ? (c.responseText = f.contentWindow.document.body ? f.contentWindow.document.body.innerHTML : null, c.responseXML = f.contentWindow.document.XMLDocument ? f.contentWindow.document.XMLDocument : f.contentWindow.document) : f.contentDocument && (c.responseText = f.contentDocument.document.body ? f.contentDocument.document.body.innerHTML : null, c.responseXML = f.contentDocument.document.XMLDocument ? f.contentDocument.document.XMLDocument : f.contentDocument.document);
      } catch {
      }
      if (c || p == "timeout") {
        o = !0;
        var m;
        try {
          if (m = p != "timeout" ? "success" : "error", m != "error") {
            var C = $.uploadHttpData(c, e.dataType);
            e.success && e.success(C, m), e.global && $.event.trigger("ajaxSuccess", [c, e]);
          }
        } catch {
          m = "error";
        }
        e.global && $.event.trigger("ajaxComplete", [c, e]), e.global && !--$.active && $.event.trigger("ajaxStop"), e.complete && e.complete(c, m), $(f).unbind(), setTimeout(function() {
          try {
            $(f).remove(), $(a).remove();
          } catch {
          }
        }, 100), c = null;
      }
    };
    e.timeout > 0 && setTimeout(function() {
      o || u("timeout");
    }, e.timeout);
    try {
      var a = $("#" + d);
      $(a).attr("action", e.url), $(a).attr("method", "POST"), $(a).attr("target", n), a.encoding ? $(a).attr("encoding", "multipart/form-data") : $(a).attr("enctype", "multipart/form-data"), $(a).submit();
    } catch {
    }
    var l = 0, s = navigator.userAgent.toLowerCase();
    return s.indexOf("opera") != -1 ? $("#" + n).load(function() {
      l++, l == 2 && u();
    }) : $("#" + n).on("load", u), {
      abort: function() {
      }
    };
  },
  uploadHttpData: function(r, type) {
    return data = type == "xml" && !type ? r.responseXML : r.responseText, type == "script" && $.globalEval(data), type == "json" && eval("data = " + data), data;
  }
});
$(document).on("mouseenter", ".cCrud-list .cCrud-row", function() {
  $(this).addClass("table-active");
}).on("mouseleave", ".cCrud-list .cCrud-row", function() {
  $(this).removeClass("table-active");
});
