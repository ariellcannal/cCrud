var cCrud = {
	config: function(key) {
		if (cCrud_config[key] !== undefined) {
			return cCrud_config[key];
		} else {
			return key;
		}
	},
	lang: function(key) {
		if (cCrud_config['lang'][key] !== undefined) {
			return cCrud_config['lang'][key];
		} else {
			return key;
		}
	},
	current_task: null,
	after_task: null,
	current_focus: null,
	current_pos: null,
	parent_container: null,
	close_modal: false,
	request: function(container, data, success_callback) {
		$.ajax({
			type: "post",
			url: cCrud.config('url'),
			dataType: "text", // text permite receber tanto HTML quanto JSON
			cache: false,
			data: {
				"cCrud": data
			},
			beforeSend: function() {
				$(document).trigger("cCrudbeforerequest", [container, data]);
				cCrud.close_modal = data.close;
				cCrud.current_task = data.task;
				cCrud.current_focus = $("*:focus");
				cCrud.after_task = data.after;
			},
			success: function(response, textStatus, jqXHR) {
				// Resposta HTML normal
				if (!$('.cCrud_result_validation').lenght) {
					$('body').append($('<div>').attr('class', 'cCrud_result_validation'));
				}
				var validation_container = response;
				cCrud.check_message(validation_container);
				if (!cCrud.exception) {
					if (cCrud.close_modal == true) {
						$("#cCrud-modal-window").modal('hide');
						if (cCrud.parent_container) {
							container = cCrud.parent_container;
							cCrud.parent_container = null;
						}
					cCrud.close_modal = false;
				}
				// Destruir plugins antes de substituir HTML
				cCrud.destroy_plugins(container);
				$(container).html(response);
				// Reinicializa plugins jQuery após atualização AJAX
				cCrud.reinit_plugins(container);
					if (success_callback) {
						success_callback(container);
					}
				}
			},
			error: function(jqXHR, textStatus, errorThrown) {
				cCrud.show_error(cCrud.lang('undefined_error'))
				console.log(jqXHR.statusText);
				console.log(jqXHR.responseText);
			},
			complete: function(jqXHR) {
				$(document).trigger("cCrudafterrequest", [container, data]);
				cCrud.hide_progress(container);
			}
		});
	},
	modal_request: function(container, data) {
		data.is_modal = true;
		var html = cCrud.data2form(data);
		cCrud.bootstrap_modal('Aguarde', '');
		setTimeout(function() {
			el = $("#cCrud-modal-window .modal-content").addClass('cCrud').addClass('cCrud-ajax');
			cCrud.request(el, data, cCrud.reinit);
		}, 500);
	},
	init: function(container) {
		$(document).trigger("cCrudinit");
	},
	new_window_request: function(container, data) {
		var html = cCrud.data2form(data);
		var w = window.open("", "cCrud_request", "scrollbars,resizable,height=500,width=900");
		w.document.open();
		w.document.write(html);
		w.document.close();
		$(w.document.body).find('form').submit();
	},
	data2form: function(data) {
		var html = '<!DOCTYPE HTML><html><head><meta http-equiv="content-type" content="text/html;charset=utf-8" /></head><body>';
		html += '<form method="post" action="' + cCrud.config('url') + '">';
		$.map(data, function(value, key) {
			if (!$.isPlainObject(value)) {
				html += '<input type="hidden" name="cCrud[' + key + ']" value="' + value + '" />';
			}
		});
		html += '</form></body></html>';
		return html;
	},
	unique_check: function(container, data, success_callback) {
		data.unique = {};
		data.task = "unique";
		if ($(container).find('.cCrud-input[data-unique]').length) {
			$(container).find('.cCrud-input[data-unique]').each(function(index, element) {
				data.unique[$(element).attr('name')] = $(element).val();
			});
			$.ajax({
				type: "post",
				url: cCrud.config('url'),
				beforeSend: function() {
					cCrud.show_progress(container);
				},
				data: {
					"cCrud": data
				},
				dataType: "json",
				success: function(response) {
					// $(container).find(".cCrud-data[name=key]:first").val(response.key);
					if (response.error) {
						$(container).find(response.error.selector).parent().parent().addClass('has-error');
						// alert(cCrud.lang('unique_error'));
						cCrud.show_error(cCrud.lang('unique_error'));
						return false;
					}
					if (success_callback) {
						success_callback(container);
					}
				},
				complete: function() {
					cCrud.hide_progress(container);
				},
				error: function(jqXHR, textStatus, errorThrown) {
					console.log(textStatus);
					console.log(jqXHR.responseText);
				},
				cache: false
			});
		} else {
			if (success_callback) {
				success_callback(container);
			}
		}
	},
	show_progress: function(container) {
		$(container).closest(".cCrud").find(".cCrud-overlay").fadeTo(300, 0.6);
	},
	hide_progress: function(container) {
		$(container).closest(".cCrud").find(".cCrud-overlay").css("display", "none");
	},
	get_container: function(element) {
		return $(element).closest(".cCrud-ajax");
	},
	list_data: function(container, element) {
		var data = {};
		cCrud.validation_error = 0;
		cCrud.save_editor_content(container);
		$(container).find(".cCrud-data:not([type='checkbox'])").each(function() {
			if (cCrud.check_container(this, container)) {
				data[$(this).attr("name")] = cCrud.prepare_val(this);
			}
		});
		$(container).find('.cCrud-data[type="checkbox"]:not([disabled])').each(function() {
			if (cCrud.check_container(this, container) && $(this).prop('checked')) {
				data[$(this).attr("name")] = cCrud.prepare_val(this);
			}
		});
		if (element && $.isPlainObject(element)) {
			$.extend(data, element);
		} else if (element) {
			$.extend(data, $(element).data());
		}
		data.postdata = {};
		var validation = data.task == 'save' ? true : false;
		if (validation) {
			$('.is-invalid', container).removeClass('is-invalid');
			$(document).trigger("cCrudbeforevalidate", [container]);
		}
		$('.cCrud-input:not([type="checkbox"],[type="radio"],[disabled])', container).each(function() {
			if (cCrud.check_container(this, container)) {
				var val = cCrud.prepare_val(this);
				data.postdata[$(this).attr("name")] = val;

				var required = $(this).data('required');
				var pattern = $(this).data('pattern');
				var plugin = $(this).data('plugin');
				var validar = $(this).data('validar');

				if (validation && required && !cCrud.validation_required(val, required)) {
					cCrud.field_invalid(this);
				} else if (validation && pattern && plugin != 'formatter' && !cCrud.validation_pattern(val, pattern)) {
					cCrud.field_invalid(this);
				} else if (validation && validar == 'cnpj' && !cCrud.validation_cnpj(val)) {
					cCrud.field_invalid(this);
				} else if (validation && validar == 'url' && !cCrud.validation_url(val)) {
					cCrud.field_invalid(this);
				}
			}
		});
		var group_required = false;
		var has_group_required = false;
		$(container).find('.cCrud-input[group-required="true"]:not([type="checkbox"],[type="radio"],[disabled])').each(function() {
			if (cCrud.check_container(this, container)) {
				has_group_required = true;
				var val = cCrud.prepare_val(this);
				data.postdata[$(this).attr("name")] = val;
				var pattern = $(this).data('pattern');
				if (cCrud.validation_required(val, 1)) {
					group_required = true;
				}
			}
		});
		if (has_group_required && !group_required) {
			$(container).find('.cCrud-input[group-required="true"]:not([type="checkbox"],[type="radio"],[disabled])').each(function() {
				if (cCrud.check_container(this, container)) {
					cCrud.field_invalid(this);
				}
			});
		}
		$(container).find('.cCrud-input[data-type="checkboxes"]:not([disabled])').each(function() {
			if (data.postdata[$(this).attr("name")] === undefined) {
				data.postdata[$(this).attr("name")] = '';
			}
			if (cCrud.check_container(this, container) && $(this).prop('checked')) {
				if (!data.postdata[$(this).attr("name")]) {
					data.postdata[$(this).attr("name")] = cCrud.prepare_val(this);
				} else {
					data.postdata[$(this).attr("name")] += "," + cCrud.prepare_val(this);
				}
			}
		});
		$(container).find('.cCrud-input[type="radio"]:not([disabled])').each(function() {
			if (cCrud.check_container(this, container) && $(this).prop('checked')) {
				data.postdata[$(this).attr("name")] = cCrud.prepare_val(this);
			}
		});
		$(container).find('.cCrud-input[data-type="bool"]:not([disabled])').each(function() {
			if (cCrud.check_container(this, container)) {
				data.postdata[$(this).attr("name")] = $(this).prop('checked') ? 1 : 0;
			}
		});
		$(container).find(".cCrud-searchdata.cCrud-search-active").each(function() {
			if (cCrud.check_container(this, container)) {
				data[$(this).attr("name")] = cCrud.prepare_val(this);
			}
		});
		if (validation) {
			$(document).trigger("cCrudaftervalidate", [container, data]);
		}
		return data;
	},
	field_invalid: function(field) {
		cCrud.validation_error = 1;
		$(field).addClass('is-invalid');
		$(field).closest('.form-group').addClass('is-invalid');
		$('*[href="#' + $(field).closest('.tab-pane').attr('id') + '"]').addClass('is-invalid');
	},
	list_controls_data: function(container, element) {
		var data = {};
		$(container).find(".cCrud-data").each(function() {
			if (cCrud.check_container(this, container)) {
				data[$(this).attr("name")] = cCrud.prepare_val(this);
			}
		});
		return data;
	},
	check_container: function(element, container) {
		return $(element).closest(".cCrud-ajax").attr('id') == $(container).attr('id') ? true : false;
	},
	save_editor_content: function(container) {
		if ($(container).find('.cCrud-texteditor').length) {
			if (typeof tinyMCE !== 'undefined') {
				tinyMCE.triggerSave();
				/*
				 * for (instance in tinyMCE.editors) { if
				 * (tinyMCE.editors[instance] && isNaN(instance * 1)) { if
				 * ($('#' + instance).length) {
				 * tinyMCE.editors[instance].save(); } else {
				 * //tinyMCE.editors[instance].destroy();
				 * //tinyMCE.editors[instance] = null; } } }
				 */
			}
			if (typeof CKEDITOR !== 'undefined') {
				for (instance in CKEDITOR.instances) {
					if ($('#' + instance).length) {
						CKEDITOR.instances[instance].updateElement();
					}
					/*
					 * else { CKEDITOR.instances[instance].destroy(); }
					 */
				}
			}
		}
	},
	prepare_val: function(element) {
		switch ($(element).data("type")) {
			case 'datetime':
			case 'timestamp':
			case 'date':
			case 'time':
			default:
				return $.trim($(element).val());
				break;
		}
	},
	change_filter: function(type, container, fieldname) {
		$(container).find(".cCrud-searchdata").hide().removeClass("cCrud-search-active");
		var name_selector = '';
		switch (type) {
			case 'datetime':
			case 'timestamp':
			case 'date':
			case 'time':
				var fieldtype = 'date';
				break;
			case 'bool':
				var fieldtype = 'bool';
				break;
			case 'select':
			case 'multiselect':
			case 'radio':
			case 'checkboxes':
				var fieldtype = 'dropdown';
				name_selector = '[data-fieldname="' + fieldname + '"]';
				break;
			default:
				var fieldtype = 'default';
				break;
		}
		$(container).find('.cCrud-searchdata[data-fieldtype="' + fieldtype + '"]' + name_selector).show().addClass("cCrud-search-active");
		if (fieldtype == 'date') {
			cCrud.init_datepicker_range(type, container);
		}
	},
	init_datepicker_range: function(type, container) {
		/*
		 * https://github.com/Eonasdan/bootstrap-datetimepicker/
		 */
		if (!$.fn.datetimepicker) {
			console.error('[cCrud] jQuery UI Timepicker Addon não está carregado');
			return;
		}
		if ($(container).find('.cCrud-datepicker-from').data("DateTimePicker") == undefined && $(container).find('.cCrud-datepicker-to').data("DateTimePicker") == undefined) {
			from = $(container).find('.cCrud-datepicker-from').datetimepicker();
			to = $(container).find('.cCrud-datepicker-to').datetimepicker();
			switch (type) {
				case 'time':
					element.datetimepicker({
						format: cCrud_config.time_format,
						useCurrent: false
					});
					break;
				case 'datetime':
				case 'timestamp':
					element.datetimepicker({
						format: cCrud_config.date_format + ' ' + cCrud_config.time_format,
						useCurrent: false
					});
					break;
				case 'date':
					element.datetimepicker({
						format: cCrud_config.date_format,
						useCurrent: false
					});
					break;
				case 'year':
					element.datetimepicker({
						viewMode: 'years',
						format: 'yyyy',
						useCurrent: false
					});
					break;
				default:
					cCrud.link_datetime_fields(from, to);
					break;
			}
		}
	},
	init_datepicker: function(container) {
		/*
		 * https://github.com/Eonasdan/bootstrap-datetimepicker/
		 */
		console.log('[cCrud] init_datepicker called with container:', container);
		if (!$.fn.datetimepicker) {
			console.error('[cCrud] jQuery UI Timepicker Addon não está carregado');
			return;
		}
		var elements = $(container).find(".cCrud-datepicker");
		console.log('[cCrud] Found', elements.length, 'datepicker elements');
		elements.each(function(index) {
			console.log('[cCrud] Processing datepicker element', index, ':', this);
				if ($(this).data("DateTimePicker") == undefined) {
					console.log('[cCrud] Initializing datepicker', index, 'type:', $(this).data("type"));
					var element = $(this);
					var format_id = $(this).data("type");
					switch (format_id) {
						case 'time':
							element.datetimepicker({
								format: cCrud_config.time_format,
								useCurrent: false
							});
							console.log('[cCrud] Time picker initialized');
							break;
						case 'datetime':
						case 'timestamp':
							element.datetimepicker({
								format: cCrud_config.date_format + ' ' + cCrud_config.time_format,
								useCurrent: false
							});
							console.log('[cCrud] Datetime picker initialized');
							break;
						case 'date':
							element.datetimepicker({
								format: cCrud_config.date_format,
								useCurrent: false
							});
							console.log('[cCrud] Date picker initialized');
							break;
						case 'year':
							element.datetimepicker({
								viewMode: 'years',
								format: 'yyyy',
								useCurrent: false
							});
							console.log('[cCrud] Year picker initialized');
							break;
						default:
							var range_start = element.data("rangestart");
							var range_end = element.data("rangeend");
							cCrud.link_datetime_fields(range_start, range_end);
							console.log('[cCrud] Range picker linked');
							break;
					}
				} else {
					console.log('[cCrud] Datepicker', index, 'already initialized, skipping');
				}
		});
	},
	link_datetime_fields: function(field_from, field_to) {
		if (field_from != undefined && field_from.data("DateTimePicker") != undefined && field_to != undefined && field_to.data("DateTimePicker") != undefined) {
			$(field_to).data("DateTimePicker").useCurrent(false)

			$(field_from).on("dp.change", function(e) {
				$(field_to).data("DateTimePicker").minDate(e.date);
			});
			$(field_to).on("dp.change", function(e) {
				$(field_from).data("DateTimePicker").maxDate(e.date);
			});
		}
	},
	init_texteditor: function(container) {
		var elements = $(container).find(".cCrud-texteditor:not(.editor-loaded)");
		if ($(elements).length) {
                        if (cCrud.config('force_editor') || typeof tinyMCE !== 'undefined' || typeof CKEDITOR !== 'undefined') {
                                $(elements).addClass("editor-loaded").addClass("editor-instance");
                                if (typeof tinyMCE !== 'undefined') {
                                        tinyMCE.init({
                                                mode: "textareas",
                                                editor_selector: "editor-instance",
                                                height: "250"
                                        });
                                } else if (typeof CKEDITOR !== 'undefined') {
                                        $('.editor-instance').each(function() {
                                                if ($(this).data('editor-config')) {
                                                        CKEDITOR.replace($(this).get(0), { customConfig: $(this).data('editor-config') });
                                                } else {
                                                        CKEDITOR.replace($(this).get(0));
                                                }
                                        });
                                }
                                $(elements).removeClass("editor-instance");
                        }
                }
        },
	upload_file: function(element, data, container) {
		var upl_container = $(element).closest('.cCrud-upload-container');
		data.field = $(element).data("field");
		data.oldfile = $(upl_container).find('.cCrud-input').val();
		data.task = "upload";
		data.mode = $(element).closest('.cCrud-ajax').find('.cCrud-data[name="task"]').val();
		data.type = $(element).data("type");
		var ext = cCrud.get_extension($(element).val());
		if (data.type == 'image') {
			switch (ext.toLowerCase()) {
				case 'jpg':
				case 'jpeg':
				case 'gif':
				case 'png':
					break;
				default:
					cCrud.show_error(cCrud.lang('image_type_error'));
					$(element).val('');
					return false;
					break;
			}
		}
		$(document).trigger("cCrudbeforeupload", [container, data]);
		cCrud.show_progress(container);
		$.ajaxFileUpload({
			secureuri: false,
			fileElementId: $(element).attr('id'),
			data: {
				"cCrud": data
			},
			url: cCrud.config('url'),
			success: function(out) {
				cCrud.hide_progress(container);
				$(upl_container).replaceWith(out);
				$(document).trigger("cCrudafterupload", [container, data, status]);
				var crop_img = $(out).find("img.cCrud-crop");
				if ($(crop_img).length) {
					cCrud.show_crop_window(crop_img, container);
				}
			},
			error: function() {
				cCrud.hide_progress(container);
				cCrud.show_error(cCrud.lang('undefined_error'));
			}
		});
	},
	show_crop_window: function(crop_img, container) {
		var upl_container = $(container).find('img.cCrud-crop').closest('.cCrud-upload-container');
		$(crop_img).dialog({
			resizable: false,
			height: 'auto',
			width: 'auto',
			modal: true,
			closeOnEscape: false,
			buttons: {
				"OK": function() {
					var data = cCrud.list_data(container, {
						"task": "crop_image"
					});
					$(upl_container).find('.xrud-crop-data').each(function() {
						data[$(this).attr('name')] = $(this).val();
					});
					// data.task = "crop_image";
					$(document).trigger("cCrudbeforeecrop", [container, data]);
					cCrud.show_progress(container);
					$.ajax({
						data: {
							"cCrud": data
						},
						success: function(out) {
							cCrud.hide_progress(container);
							$(upl_container).replaceWith(out);
							$(document).trigger("cCrudaftercrop", [container, data]);
						},
						error: function() {
							cCrud.hide_progress(container);
							cCrud.show_error(cCrud.lang('undefined_error'));
						},
						type: "post",
						url: cCrud.config('url'),
						dataType: "html",
						cache: false,
					});
					$(this).dialog("destroy");
					$(".cCrud-crop").remove();
				}
			},
			close: function(event, ui) {
				var data = cCrud.list_data(container, {
					"task": "crop_image"
				});
				$(upl_container).find('.xrud-crop-data').each(function() {
					data[$(this).attr('name')] = $(this).val();
				});
				// data.task = "crop_image";
				data.w = 0;
				data.h = 0;
				cCrud.show_progress(container);
				$.ajax({
					data: {
						"cCrud": data
					},
					success: function(out) {
						cCrud.hide_progress(container);
						$(upl_container).replaceWith(out);
					},
					error: function() {
						cCrud.hide_progress(container);
						cCrud.show_error(cCrud.lang('undefined_error'));
					},
					type: "post",
					url: cCrud.config('url'),
					dataType: "html",
					cache: false,
				});
				$(this).dialog("destroy");
				$(".cCrud-crop").remove();
			},
			open: function(event, ui) {
				cCrud.load_image(crop_img.attr('src'), function(imageObject) {
					var t_w = parseInt($(crop_img).data('width'));
					var t_h = parseInt($(crop_img).data('height'));
					var ratio = parseFloat($(crop_img).data('ratio'));
					var cropset = {};
					cropset.boxWidth = t_w;
					cropset.boxHeight = t_h;
					if (t_h > 500) {
						cropset.boxHeight = 500;
						cropset.boxWidth = Math.round(t_w * 500 / t_h)
					}
					if (cropset.boxWidth > 550) {
						cropset.boxWidth = 550;
						cropset.boxHeight = Math.round(t_h * 550 / t_w);
					}
					/*
					 * $(crop_img).css({ "width": cropset.boxWidth, "height":
					 * cropset.boxHeight, "min-height": cropset.boxHeight });
					 */
					var left = Math.round(($(window).width() - $(".ui-dialog.ui-widget").width()) / 2);
					var top = Math.round(($(window).height() - $(".ui-dialog.ui-widget").height()) / 2);
					$(".ui-dialog.ui-widget").css({
						"position": "fixed",
						"left": left + "px",
						"top": top + "px"
					});
					cropset.minSize = [50, 50];
					if (ratio) {
						cropset.aspectRatio = ratio;
					}
					cropset.onChange = cCrud.get_coordinates;
					cropset.keySupport = false;
					cropset.trueSize = [t_w, t_h];
					var w1 = t_w / 4;
					var h1 = t_h / 4;
					var w2 = w1 * 3;
					var h2 = h1 * 3;
					cropset.setSelect = [w1, h1, w2, h2];
					cropset.allowSelect = false;
					$(".ui-dialog img.cCrud-crop").Jcrop(cropset);
				});
			}
		});
	},
	load_image: function(url, callback) {
		var imageObject = new Image();
		imageObject.src = url;
		if (imageObject.complete) {
			if (callback) {
				callback(imageObject);
			}
		} else {
			$(document).trigger("startload");
			imageObject.onload = function() {
				$(document).trigger("stopload");
				if (callback) {
					callback(imageObject);
				}
			}
			imageObject.onerror = function() {
				$(document).trigger("stopload");
				if (callback) {
					callback(false);
				}
			}
		}
	},
	remove_file: function(element, data, container) {
		var upl_container = $(element).closest('.cCrud-upload-container');
		data.field = $(element).data("field");
		data.file = $(upl_container).find('.cCrud-input').val();
		data.task = "remove_upload";
		cCrud.show_progress(container);
		$.ajax({
			data: {
				"cCrud": data
			},
			success: function(data) {
				cCrud.hide_progress(container);
				$(upl_container).replaceWith(data);
			},
			type: "post",
			url: cCrud.config('url'),
			dataType: "html",
			cache: false,
			error: function() {
				cCrud.hide_progress(container);
				cCrud.show_error(cCrud.lang('undefined_error'));
			}
		});
	},
	get_coordinates: function(c) {
		$(".cCrud").find("input.xrud-crop-data[name=x]").val(Math.round(c.x));
		$(".cCrud").find("input.xrud-crop-data[name=y]").val(Math.round(c.y));
		$(".cCrud").find("input.xrud-crop-data[name=x2]").val(Math.round(c.x2));
		$(".cCrud").find("input.xrud-crop-data[name=y2]").val(Math.round(c.y2));
		$(".cCrud").find("input.xrud-crop-data[name=w]").val(Math.round(c.w));
		$(".cCrud").find("input.xrud-crop-data[name=h]").val(Math.round(c.h));
	},
	validation_url: function(val) {
		if (val == "")
			return true;
		var urlregex = new RegExp(
			"^(http|https|ftp)\://([a-zA-Z0-9\.\-]+(\:[a-zA-Z0-9\.&amp;%\$\-]+)*@)*((25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9])\.(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9]|0)\.(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9]|0)\.(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[0-9])|([a-zA-Z0-9\-]+\.)*[a-zA-Z0-9\-]+\.(com|edu|gov|int|mil|net|org|biz|arpa|info|name|pro|aero|coop|museum|[a-zA-Z]{2}))(\:[0-9]+)*(/($|[a-zA-Z0-9\.\,\?\'\\\+&amp;%\$#\=~_\-]+))*$");
		return urlregex.test(val);

	},
	validation_required: function(val, length) {
		return $.trim(val).length >= length;
	},
	validation_pattern: function(val, pattern) {
		if (val === '') {
			return true;
		}
		switch (pattern) {
			case 'email':
				reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
				// reg
				// =/^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
				return reg.test($.trim(val));
				break;
			case 'alpha':
				reg = /^([a-z])+$/i;
				return reg.test($.trim(val));
				break;
			case 'alpha_numeric':
				reg = /^([a-z0-9])+$/i;
				return reg.test($.trim(val));
				break;
			case 'alpha_dash':
				reg = /^([-a-z0-9_-])+$/i;
				return reg.test($.trim(val));
				break;
			case 'numeric':
				reg = /^[\-+]?[0-9]*(\.|\,)?[0-9]+$/;
				return reg.test($.trim(val));
				break;
			case 'integer':
				reg = /^[\-+]?[0-9]+$/;
				return reg.test($.trim(val));
				break;
			case 'decimal':
				reg = /^[\-+]?[0-9]+(\.|\,)[0-9]+$/;
				return reg.test($.trim(val));
				break;
			case 'point':
				reg = /^[\-+]?[0-9]+\.{0,1}[0-9]*\,[\-+]?[0-9]+\.{0,1}[0-9]*$/;
				return reg.test($.trim(val));
				break;
			case 'natural':
				reg = /^[0-9]+$/;
				return reg.test($.trim(val));
				break;
			default:
				reg = new RegExp(pattern);
				return reg.test($.trim(val));
				break;
		}
		return true;
	},
	pattern_callback: function(e, element) {
		var pattern = $(element).data('pattern');
		if (pattern) {
			var code = e.which;
			if (code < 32 || e.ctrlKey || e.altKey)
				return true;
			var val = String.fromCharCode(code);
			switch (pattern) {
				case 'alpha':
					reg = /^([a-z])+$/i;
					return reg.test(val);
					break;
				case 'alpha_numeric':
					reg = /^([a-z0-9])+$/i;
					return reg.test(val);
					break;
				case 'alpha_dash':
					reg = /^([-a-z0-9_-])+$/i;
					return reg.test(val);
					break;
				case 'numeric':
				case 'integer':
				case 'decimal':
				case 'point':
					reg = /^[0-9\.\,\-+]+$/;
					return reg.test(val);
					break;
				case 'natural':
					reg = /^[0-9]+$/;
					return reg.test(val);
					break;
			}
		}
		return true;
	},
	validation_error: false,
	get_extension: function(filename) {
		var parts = filename.split('.');
		return parts[parts.length - 1];
	},
	check_fixed_buttons: function() {
		return null;
		$(".cCrud").each(function() {
			if ($(this).find(".cCrud-list:first").width() > $(this).find(".cCrud-list-container:first").width()) {
				var w = $(this).find(".cCrud-actions:not(.cCrud-fix):first").width();
				$(this).find(".cCrud-actions:not(.cCrud-fix):first").css({
					"width": w,
					"min-width": w
				});
				$(this).find(".cCrud-list:first .cCrud-actions.cCrud-fix:not(.cCrud-actions-fixed)").addClass("cCrud-actions-fixed");
			} else
				$(this).find(".cCrud-list:first .cCrud-actions").removeClass("cCrud-actions-fixed");
		});
	},
	block_query: {},
	depend_init: function(container) {
		$(container).off('change.depend');
		var dependencies = {};
		$(container).find('.cCrud-input[data-depend]').each(function() {
			var container = cCrud.get_container(this);
			var data = cCrud.list_controls_data(container, this);
			var depend_on = $(this).data("depend");
			data.task = "depend";
			data.name = $(this).attr('name');
			data.value = $(this).val();
			$(container).on('change.depend', '.cCrud-input[name="' + depend_on + '"]', function() {
				if (cCrud.check_container(this, container)) {
					data.dependval = $(this).val();
					cCrud.depend_query(data, depend_on, container);
				}
			});
			if (depend_on) {
				dependencies[depend_on] = depend_on;
			}
		});
		$.map(dependencies, function(val, key) {
			window.setTimeout(function() {
				$(container).find('.cCrud-input[name="' + val + '"]:not([data-depend])').trigger('change.depend');
			}, 100);
		});
	},
	depend_query: function(data, depend_on, container) {
		if (cCrud.block_query[data.name + depend_on]) {
			return;
		}
		cCrud.block_query[data.name + depend_on] = 1;
		var el = $(container).find('.cCrud-input[name="' + data.name + '"]');
		var parent = el.parent();
		var values = el.val();

		$(parent).trigger("cCrudbeforedepend", [container, data]);
		$.ajax({
			data: {
				"cCrud": data
			},
			type: 'post',
			url: cCrud.config('url'),
			success: function(input) {
				el.select2('destroy').remove();
				parent.css('visibility', 'hidden').append(input);
				el = $(parent).find('.cCrud-input[name="' + data.name + '"]');
				if (values !== null) {
					el.val(values);
				}
				$(parent).trigger("cCrudafterdepend", [container, data]);
				window.setTimeout(function() {
					cCrud.jr_request($(container).find('.cCrud-input[name="' + data.name + '"]'));
					cCrud.block_query[data.name + depend_on] = 0;
				}, 400);
				el.select2();
				$(parent).css('visibility', 'visible');
			},
			cache: false
		});
	},
	init_autosave: function() {
		return false;
		$('input,textarea,select').blur(function() {
			var idleTime = 0;
			setInterval(function() {
				idleTime = idleTime + 1;
				if (idleTime > 5000) {
					// console.log(idleTime);
					var tag = $(':focus')[0].tagName;
					var nome = $(':focus').attr('name');
					var classe = $(':focus').attr('class');
					var style = $(':focus').attr('style');
					var id = $(':focus').attr('id');
					// alert('tag: '+tag+'\nnome: '+nome+'\nclasse:
					// '+classe+'\nstyle: '+style+'\nid: '+id);
					$('a[data-task="save"]').click();
					setTimeout(function() {
						// id = "contatoPr";
						if (id)
							el = $('#' + id).focus();
						else if (nome)
							el = $(tag + '[name="' + nome + '"]').focus();
						else if (style)
							el = $(tag + '[style="' + style + '"]').focus();
						else
							el = $(tag + '[class="' + classe + '"]').focus();
						if (tag == "INPUT")
							$(el).select();
					}, 200);
					idleTime = 0;
				}
			}, 1);
			clearTimeout(autosave);
			var autosave = setTimeout(function() {
			}, 5000);
		})
	},
	parse_latlng: function(string) {
		var coords = string.split(',');
		if (coords.length != 2) {
			return null;
		}
		var LatLng = new google.maps.LatLng(parseFloat(coords[0]), parseFloat(coords[1]));
		return LatLng;
	},
	create_map: function(selector, center, zoom, type) {
		var params = {
			zoom: zoom,
			center: center,
			mapTypeId: google.maps.MapTypeId[type]
		}
		var map = new google.maps.Map($(selector)[0], params);
		return map;
	},
	place_marker: function(map, point, draggable, infowindow, point_field) {
		var marker = new google.maps.Marker({
			position: point,
			map: map,
			animation: google.maps.Animation.DROP,
			draggable: (draggable ? true : false)
		});
		if (infowindow) {
			google.maps.event.addListener(marker, 'click', function() {
				var currentmarker = this;
				var infoWindow = new google.maps.InfoWindow({
					maxWidth: 320
				});
				infoWindow.setContent('<p class="cCrud-infowinow">' + infowindow + '</p>');
				infoWindow.open(map, currentmarker);
			});
		}
		if (draggable && $(point_field).length) {
			google.maps.event.addListener(marker, 'dragend', function() {
				$(point_field).val(this.getPosition().lat() + ',' + this.getPosition().lng());
			});
			google.maps.event.addListener(map, 'click', function(event) {
				// console.log(oMap);
				marker.setPosition(event.latLng);
				$(point_field).val(marker.getPosition().lat() + ',' + marker.getPosition().lng());
			});
		}
		return marker;
	},
	move_marker: function(map, marker, point, dragable, infowindow) {
		if (marker) {
			marker.setPosition(point);
		} else {
			this.place_marker(map, point, dragable, infowindow)
		}
		map.setCenter(point);
		return marker;
	},
	find_point: function(address, callback) {
		return this.geocode({
			address: address
		}, callback);
	},
	find_address: function(point, callback) {
		return this.geocode({
			latLng: point
		}, callback);
	},
	geocode: function(geocoderRequest, callback, callback_single) {
		var geocoder = new google.maps.Geocoder();
		geocoder.geocode(geocoderRequest, function(results, status) {
			// console.log(results);
			var output = {};
			if (status == google.maps.GeocoderStatus.OK) {
				for (var i = 0; i < results.length; i++) {
					if (results[i].formatted_address) {
						// console.log(results[i]);
						output[i] = {};
						output[i].lat = results[i].geometry.location.lat();
						output[i].lng = results[i].geometry.location.lng();
						output[i].address = results[i].formatted_address;
						if (callback_single) {
							return callback_single(output[i]);
						}
					}
				}
				if (callback) {
					callback(output);
				}
			}
		});
	},
	map_instances: [],
	marker_instances: [],
	map_init: function(container) {
		cCrud.map_instances = [];
		$(container).find('.cCrud-map').each(function() {
			var cont = this;
			var point_field = $(cont).parent().children('input[data-type="point"]');
			var search_field = $(cont).parent().children('.cCrud-map-search');
			var point = cCrud.parse_latlng($(point_field).val());
			var map = cCrud.create_map(cont, point, $(cont).data('zoom'), 'ROADMAP');
			var marker = cCrud.place_marker(map, point, $(cont).data('draggable'), $(cont).data('text'), point_field);
			$(point_field).on("keyup", function() {
				var point = cCrud.parse_latlng($(point_field).val());
				cCrud.move_marker(map, marker, point, $(cont).data('draggable'), $(cont).data('text'));
				return false;
			});
			if ($(search_field).length) {
				$(search_field).on("keyup", function() {
					var value = $.trim($(search_field).val());
					if (value) {
						cCrud.find_point(value, function(results) {
							cCrud.map_dropdown(search_field, results, map, marker, point_field, cont);
						});
					}
					return false;
				});
			}
			cCrud.map_instances.push(map);
			cCrud.marker_instances.push(marker);
		});
	},
	map_dropdown: function(element, results, map, marker, point_field, cont) {
		var m_left = $(element).outerWidth();
		var m_top = $(element).outerHeight();
		var pos = $(element).offset();
		$(element).prev(".cCrud-map-dropdown").remove();
		if (results) {
			var list = '<ul class="cCrud-map-dropdown">';
			$.map(results, function(value) {
				list += '<li data-val="' + value.lat + ',' + value.lng + '">' + value.address + '</li>';
			});
			list += '</ul>';
			$(element).before(list);
			$(element).prev(".cCrud-map-dropdown").offset(pos).css({
				"marginTop": m_top + "px",
				"minWidth": m_left + "px"
			}).children('li').on("click", function() {
				var point = cCrud.parse_latlng($(this).data("val"));
				$(element).val($(this).text());
				marker = cCrud.move_marker(map, marker, point, $(cont).data('draggable'), $(cont).data('text'));
				$(point_field).val(marker.getPosition().lat() + ',' + marker.getPosition().lng());
				$(this).parent('ul').remove();
				return false;
			});
		}
	},
	map_resize_all: function() {
		if ($(".cCrud-map").length && cCrud.map_instances.length) {
			for (i = 0; i < cCrud.map_instances.length; i++) {
				var map = cCrud.map_instances[i];
				var marker = cCrud.marker_instances[i];
				google.maps.event.trigger(map, 'resize');
				map.setZoom(map.getZoom());
				map.setCenter(marker.position)
			}
		}
	},
	reload: function(selector_or_object) {
		if (!selector_or_object) {
			selector_or_object = 'body';
		}
		if ($(selector_or_object).hasClass('cCrud-ajax')) {
			var obj = $(selector_or_object);
		} else {
			var obj = $(selector_or_object).find(".cCrud-ajax");
		}
		obj = obj.eq(0);
		obj.each(function() {
			var data = cCrud.list_data(this);
			data.active_tab_id = $('.tab-pane.active.nested').attr('data-label');
			cCrud.request(this, data);
		});
	},
	bootstrap_modal: function(header, content) {
		$("#cCrud-modal-window").remove();
		$("body").append('<div id="cCrud-modal-window" class="modal fade"><div class="modal-dialog modal-lg"><div class="modal-content"></div></div></div>');
		$("#cCrud-modal-window .modal-content").html('<div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button><h4 class="modal-title">' + header + '</h4></div>');
		$("#cCrud-modal-window .modal-content").append('<div class="modal-body">' + content + '</div>');
		$("#cCrud-modal-window").modal({
			keyboard: false
		}).on('shown.bs.modal', function(e) {
		});
		$('#cCrud-modal-window [data-dismiss="modal"]').on("click", function() {
			$("#cCrud-modal-window").modal('hide');
			if ($(".simplemodal-close").length) { // joomla trick
				$(".simplemodal-close").trigger("click");
				$("#cCrud-modal-window").remove();
			}
			return false;
		});
		$('#cCrud-modal-window').on('hidden.bs.modal hidden', function() {
			$("#cCrud-modal-window").remove();
		});
	},
	ui_modal: function(header, content) {
		$("#cCrud-modal-window").remove();
		$("body").append('<div id="cCrud-modal-window">' + content + '</div>');
		$("#cCrud-modal-window").dialog({
			resizable: false,
			height: 'auto',
			width: 'auto',
			modal: true,
			closeOnEscape: true,
			close: function(event, ui) {
				$("#cCrud-modal-window").remove();
			},
			title: header
		});
	},
	modal: function(header, content) {
		content = '<span>' + content + '</span>';
		if (typeof ($.fn.modal) != 'undefined') {
			if ($(content).first().prop("tagName") == 'IMG') {
				cCrud.load_image($(content).first().attr('src'), function(imgObj) {
					cCrud.bootstrap_modal(header, content);
				})
			} else {
				cCrud.bootstrap_modal(header, content);
			}
		} else {
			if ($(content).first().prop("tagName") == 'IMG') {
				cCrud.load_image($(content).first().attr('src'), function(imgObj) {
					cCrud.ui_modal(header, content);
				})
			} else {
				cCrud.ui_modal(header, content);
			}
		}
	},
	base64_modal: function(header, content) {
		cCrud.bootstrap_modal(Base64.decode(header), Base64.decode(content));
	},
	init_tabs: function(container) {
		if ($(container).find('.cCrud-tabs').length) {
			if (typeof ($.fn.tab) != 'undefined') {
				$(container).find('.cCrud-tabs > ul:first > li > a').on("click", function() {
					$(this).tab('show');
					return false;
				});
				$('.cCrud .nav-tabs a').on('shown.bs.tab', function(e) {
					cCrud.map_resize_all();
				});
			} else {
				$(container).find('.cCrud-tabs').tabs({
					activate: function(event, ui) {
						cCrud.map_resize_all();
					}
				});
			}
		}
	},
	init_tooltips: function(container) {
		if ($(container).find('.cCrud-tooltip').length) {
			$(container).find('.cCrud-tooltip').tooltip();
		}
	},
	show_alert: function(texto) {

		if (!alertify.cCrud) {
			alertify.dialog('cCrud', function factory() {
				return {
					build: function() {
						this.setHeader(cCrud_config.table_name);
					},
					main: function(message) {
						this.message = message;
					},
					setup: function() {
						return {
							buttons: [{ text: "Ok", key: 27/*Esc*/ }],
							focus: { element: 0 }
						};
					},
					prepare: function() {
						this.setContent(this.message);
					}
				}
			});
		}
		alertify.cCrud(texto);
	},
	show_error: function(texto, delay = 5) {
		console.log(texto);
		alertify.error(texto, delay);
	},
	show_message: function(text, delay = 5) {
		console.log(texto);
		alertify.message(texto, delay);
	},
	show_success: function(texto, delay = 5) {
		console.log(texto);
		alertify.success(texto, delay);
	},
	show_warning: function(texto, delay = 5) {
		console.log(texto);
		alertify.warning(texto, delay);
	},
	show_notify: function(texto, type, delay = 5) {
		console.log(texto);
		alertify.notify(texto, type, delay);
	},
	check_message: function(container) {
		if (typeof container == "string") {
			var messages = $(container).filter(".cCrud-callback-message");
		} else {
			var messages = $(container).find(".cCrud-callback-message");
		}
		if ($(messages).length) {
			messages.each(function() {
				var message = this;
				if (cCrud.check_container(message, container)) {
					texto = $(message).val();
					type = $(message).attr("name");
					if ($(message).attr("data-exception")) {
						cCrud.exception = true;
					}
					if (type == 'alert') {
						cCrud.show_alert(texto);
					} else {
						cCrud.show_notify(texto, type);
					}
					$(message).remove();
				}
			});
		}
	},
	init_nestable: function(container) {
		return;
		var updateOutput = function(e) {
			var obj = $('#customLists #nestable_list_2').nestable('serialize');
			var val = [];
			for (var i in obj) {
				val.push(obj[i].id);
			}
			$('#customLists #cols').val(window.JSON.stringify(val));
		};
		var updateOutput2 = function(e) {
			var obj = $('#customListsEdit #nestable_list_2').nestable('serialize');
			var val = [];
			for (var i in obj) {
				val.push(obj[i].id);
			}
			$('#customListsEdit #cols').val(window.JSON.stringify(val));
		};
		// activate Nestable for list 1
		$('#customLists #nestable_list_1').nestable({
			group: 1,
			maxDepth: 1
		}).on('change', updateOutput);
		// activate Nestable for list 2
		$('#customLists #nestable_list_2').nestable({
			group: 1,
			maxDepth: 1
		}).on('change', updateOutput);
		// activate Nestable for list 3
		$('#customListsEdit #nestable_list_1').nestable({
			group: 2,
			maxDepth: 1
		}).on('change', updateOutput2);
		// activate Nestable for list 4
		$('#customListsEdit #nestable_list_2').nestable({
			group: 2,
			maxDepth: 1
		}).on('change', updateOutput2);
		// output initial serialised data
		updateOutput($('#customLists #nestable_list_2').data('output', $('#customLists #nestable_list_2_output')));
		updateOutput2($('#customListsEdit #nestable_list_2').data('output', $('#customListsEdit #nestable_list_2_output')));
		var setSaveModal = function(id) {
			$('#' + id + ' .remove').click(function() {
                                $.ajax({
                                        url: cCrud.config('url') + '/remove_custom_view',
                                        data: {
                                                id: $('#' + id + ' #lpe_id').val(),
                                        },
					type: "POST",
					success: function(json, status, jqXHR) {
						// $('#customLists .modal-body').prepend(json);
						$('#' + id + '').modal('hide');
						window.location.reload();
					}
				});
				return false;
			});

			$('#' + id + ' .save').click(function() {
				var defaults = [];
				$('#' + id + ' .custom_list').each(function() {
					if ($('#' + id + ' #lpe_nome_original').val() != $(this).attr('data-label'))
						defaults.push($(this).attr('data-label'));
				});
				if ($('#' + id + ' #cols').val() == "[]") {
					// alert_n('Pelo menos uma coluna deve ser selecionada',
					// 'error', false);
					return false;
				}
				if ($('#' + id + ' #name').val() == "") {
					$('#' + id + ' #head-name').addClass('has-error');
					// alert_n('Informe um nome para a listagem', 'error',
					// false);
					return false;
				}
				var length = defaults.length;
				for (var i = 0; i < length; i++) {
					if ($('#' + id + ' #lpe_id').val() == "" && defaults[i] == $('#' + id + ' #name').val()) {
						// alert_n('Este nome já está em uso.<br/>Por favor,
						// escolha outro.', 'error', false);
						return false;
					}
				}
				var fields = [];
				$('#' + id + ' .filtrosFields').each(function() {
					var i = [$(this).attr('id'), $(this).find('.f').val(), $(this).find('.inp .cCrud-input').val()]
					fields.push(i);
				});
				var fieldsAd = [];
				$('#' + id + ' .filtroAd').each(function() {
					var i = [$(this).attr('id'), $(this).val()]
					fieldsAd.push(i);
				});
                                $.ajax({
                                        url: cCrud.config('url') + '/save_custom_view',
                                        data: {
                                                id: $('#' + id + ' #lpe_id').val(),
                                                nome: $('#' + id + ' #name').val(),
						entidade: $('#' + id + ' #lpe_entidade').val(),
						filtros: fields,
						filtrosAdicionais: fieldsAd,
						colunas: $('#' + id + ' #cols').val()
					},
					type: "POST",
					success: function(json, status, jqXHR) {
						// $('#customLists .modal-body').prepend(json);
						$('#' + id + '').modal('hide');
						$('.custom_list').eq(0).attr('data-label', $('#' + id + ' #name').val()).click();
					}
				});
				return false;
			});
		}
		setSaveModal('customLists');
		setSaveModal('customListsEdit');
	},
	action: function(e) {
		var container = cCrud.get_container(e);
		var data = cCrud.list_data(container, e);
		if ($(e).hasClass('cCrud-in-new-window')) {
			cCrud.new_window_request(container, data);
		} else if ($(e).hasClass('cCrud-in-modal')) {
			cCrud.parent_container = container;
			cCrud.modal_request(container, data);
		} else {
			if (data.task == 'save') {
				if (!cCrud.validation_error) {
					cCrud.unique_check(container, data, function(container) {
						data.task = 'save';
						cCrud.request(container, data, data.callback);
					});
				} else {
					cCrud.show_message(container, cCrud.lang('validation_error'), 'error');
				}
			} else {
				cCrud.request(container, data);
			}
		}
	},
	jr_request: function(e) {
		var container = cCrud.get_container(e);
		var data = cCrud.list_data(container, e);
		data.task = "join_relation";
		data.jr_value = e.val();
		data.select2 = '';
		// console.log(data);
		$.ajax({
			type: "post",
			url: cCrud.config('url'),
			beforeSend: function() {
				cCrud.show_progress(container);
			},
			data: {
				"cCrud": data
			},
			dataType: "json",
			beforeSend: function(jqXHR, settings) {
				// console.log(settings);
				// console.log(data);
			},
			success: function(response) {
				for (var i in response) {
					var obj = $('.cCrud-input[name="' + i + '"]', container);
					if (obj.data('select2')) {
						obj.select2('destroy').replaceWith(response[i]);
					} else {
						obj.replaceWith(response[i]);
					}
				}
			},
			complete: function() {
				cCrud.hide_progress(container);
				$(document).trigger("cCrudafterjoinrelation", [e.closest('.form-horizontal'), data, status]);
			},
			error: function(jqXHR, textStatus, errorThrown) {
				// console.log(textStatus);
				// console.log(jqXHR);
			},
			cache: false
		});
	},
	init_select2: function(e) {
		console.log('[cCrud] init_select2 called with e:', e);
		if (!$.fn.select2) {
			console.error('[cCrud] Select2 plugin not loaded');
			return;
		}
		var container = cCrud.get_container(e);
		console.log('[cCrud] Select2 container:', container);
		var elements = $('select:not(.cCrud-columns-select):not(.cCrud-searchdata):not(.not_select2):not(.cCrud-columnsList-select)', container);
		console.log('[cCrud] Found', elements.length, 'select2 elements');
		elements.each(function(index) {
			console.log('[cCrud] Processing select2 element', index, ':', this);
			var options = $.extend({
				width: '100%'
			}, $(this).data());
			if ($(this).hasClass('select2-ajax')) {
				var container = $(this).closest('.cCrud-ajax');
				var depend_on = $(this).data("depend");
				var dados = cCrud.list_controls_data(container);
				dados.dependval = $('.cCrud-input[name="' + depend_on + '"]').val();
				dados.name = $(this).data('relationajax');
				dados.task = 'relation_search';
				$.extend(options, {
                                        ajax: {
                                                url: cCrud.config('url') + '/cCrud',
                                                dataType: 'json',
                                                delay: 250,
                                                type: 'POST',
						beforeSend: function(jqXHR, settings) {
							// console.log(jqXHR);
						},
						data: function(params) {
							return {
								q: params.term,
								cCrud: dados
							};
						},
						processResults: function(data, page) {
							return {
								results: data.items
							};
						},
						cache: false
					},
					minimumInputLength: 2
				});
				}
				console.log('[cCrud] Initializing select2 on element', index, 'with options:', options);
				$(this).select2(options);
				console.log('[cCrud] Select2 initialized on element', index);
			});
	},
	init_checkbox: function(container) {

	},
	init_mask: function(container) {
		/*
		 * https://github.com/igorescobar/jQuery-Mask-Plugin
		 */
		$('input[data-mask]', container).each(function() {
			data = $(this).data();
			var options = $.extend({}, data);
			if ("string" === typeof data.mask) {
				$(this).mask(data.mask, options);
			}
		});
	},
		init_columns_select: function(container) {
			var data = cCrud.list_data(container);
			if (data.task == 'list') {
				$('.cCrud-columnsList-select', container).SumoSelect({
					okCancelInMulti: true,
					selectAll: true
				});
			}
		},
		/**
		 * Reinicializa todos os plugins jQuery após atualização AJAX.
		 * Chamado automaticamente após cada requisição AJAX que atualiza o HTML.
		 */
	destroy_plugins: function(container) {
		console.log('[cCrud] Destroying plugins in container before HTML replacement');
		
		// Destruir datepickers
		$(container).find('.cCrud-datepicker').each(function() {
			if ($(this).data("DateTimePicker") !== undefined) {
				try {
					$(this).data("DateTimePicker").destroy();
					console.log('[cCrud] Destroyed datepicker');
				} catch(e) {
					console.warn('[cCrud] Error destroying datepicker:', e);
				}
			}
		});
		
		// Destruir select2
		$(container).find('select.select2-hidden-accessible').each(function() {
			try {
				$(this).select2('destroy');
				console.log('[cCrud] Destroyed select2');
			} catch(e) {
				console.warn('[cCrud] Error destroying select2:', e);
			}
		});
		
		// Destruir SumoSelect
		$(container).find('.SumoSelect').each(function() {
			try {
				var selectElement = $(this).prev('select');
				if (selectElement.length && selectElement[0].sumo) {
					selectElement[0].sumo.unload();
					console.log('[cCrud] Destroyed SumoSelect');
				}
			} catch(e) {
				console.warn('[cCrud] Error destroying SumoSelect:', e);
			}
		});
		
		console.log('[cCrud] Plugins destroyed');
	},
	reinit_plugins: function(container) {
		console.log('[cCrud] Reinitializing plugins for container:', container);
		console.log('[cCrud] $.fn.datetimepicker available:', typeof $.fn.datetimepicker);
		console.log('[cCrud] $.fn.select2 available:', typeof $.fn.select2);
		console.log('[cCrud] Datepicker elements found:', $(container).find('.cCrud-datepicker').length);
		console.log('[cCrud] Select2 elements found:', $('select:not(.cCrud-columns-select):not(.cCrud-searchdata):not(.not_select2):not(.cCrud-columnsList-select)', container).length);
		
		// Reinicializa datepickers
		cCrud.init_datepicker(container);
		
		// Reinicializa select2
		cCrud.init_select2(container);
		
		// Reinicializa masks
		cCrud.init_mask(container);
		
		// Reinicializa columns select (SumoSelect)
		cCrud.init_columns_select(container);
		
		console.log('[cCrud] Plugins reinitialized');
	}
	};
/** events */
$(document).on("cCrudinit", function() {
	if ($(".cCrud").length) {
		$(".cCrud").off('change', 'select.cCrud-columnsList-select').on("change", "select.cCrud-columnsList-select", function() {
			var container = cCrud.get_container(this);
			var data = cCrud.list_data(container);
			data.task = 'change_columns';
			data.columns = $(this).val();
			cCrud.request(container, data);
		});
		$(".cCrud").off('change', '.cCrud-actionlist').on("change", ".cCrud-actionlist", function() {
			var container = cCrud.get_container(this);
			var data = cCrud.list_data(container);
			cCrud.request(container, data);
		});
		$(".cCrud").off('change', '.cCrud-daterange').on("change", ".cCrud-daterange", function() {
			var container = $(this).parent();
			if ($(this).val()) {
				if ($(container).find(".cCrud-datepicker-from").data("DateTimePicker") != undefined) {
					$(container).find(".cCrud-datepicker-from").data("DateTimePicker").date(new Date($(this).find('option:selected').data('from') * 1000));
					$(container).find(".cCrud-datepicker-to").data("DateTimePicker").date(new Date($(this).find('option:selected').data('to') * 1000));
				} else {
					$(container).find(".cCrud-datepicker-from").datepicker('update', new Date($(this).find('option:selected').data('from') * 1000));
					$(container).find(".cCrud-datepicker-to").datepicker('update', new Date($(this).find('option:selected').data('to') * 1000));
				}
			} else {
				$(container).find(".cCrud-datepicker-from,.cCrud-datepicker-to").val('');
			}
		});
		$(".cCrud").off('change', '.cCrud-columns-select').on("change", ".cCrud-columns-select", function() {
			var container = $(this).parent();
			var type = $(this).children("option:selected").data('type');
			var fieldname = $(this).children("option:selected").val();
			cCrud.change_filter(type, container, fieldname);
		});
		$(".cCrud").off('click', '.cCrud-action').on("click", ".cCrud-action", function() {
			var element = $(this);
			var confirm_text = $(this).data('confirm');
			if (confirm_text) {
				alertify.confirm(cCrud_config.table_name, confirm_text, function() {
					cCrud.action(element);
				}, function() {

				});
			} else {
				cCrud.action(element);
			}
			return false;
		});
		$(".cCrud").off('click', '.cCrud-toggle-show').on("click", ".cCrud-toggle-show", function() {
			var container = $(this).closest(".cCrud").find(".cCrud-container:first");
			var closed = $(this).hasClass("cCrud-toggle-down");
			if (closed) {
				$(container).stop(true, true).delay(100).slideDown(200, function() {
					$(document).trigger("cCrudslidedown");
					$(container).trigger("cCrudslidedown");
				});
				// $(this).removeClass("cCrud-toggle-down");
				// $(this).addClass("cCrud-toggle-up");
				$(this).closest(".cCrud").find(".cCrud-main-tab").slideUp(200);
			} else {
				$(container).stop(true, true).slideUp(200, function() {
					$(document).trigger("cCrudslideup");
					$(container)
					z.trigger("cCrudslideup");
				});
				// $(this).removeClass("cCrud-toggle-up");
				// $(this).addClass("cCrud-toggle-down");
				$(this).closest(".cCrud").find(".cCrud-main-tab").delay(100).slideDown(200);
			}
			return false;
		});
		$(".cCrud").off('keypress', '.cCrud-input').on("keypress", ".cCrud-input", function(e) {
			return cCrud.pattern_callback(e, this);
		});
		$(".cCrud").off('click', '.cCrud-search-toggle').on("click", ".cCrud-search-toggle", function() {
			$(this).closest(".cCrud-ajax").find(".cCrud-search-toggle").find(".cCrud-searchdata").focus();
			return false;
		});
		$(".cCrud").off('keydown', '.cCrud-searchdata').on("keydown", ".cCrud-searchdata", function(e) {
			if (e.which == 13) { // ENTER
				var container = cCrud.get_container(this);
				var data = cCrud.list_data(container);
				data.search = 1;
				data.task = 'list';
				cCrud.request(container, data);
				return false;
			} else if (e.which == 27) { // ESC
				if ($(this).parent().find("a").hasClass('fa-search')) {
					// ainda não buscou
					if ($(this).parent().find(".cCrud-searchdata").val() === "") {

					} else {
						$(this).parent().find(".cCrud-searchdata").val('');
					}
				} else if ($(this).parent().find("a").hasClass('fa-times')) {
					// ja fez a busca (ja
					// apertou enter)
					$(this).parent().find("a").click();
				}
			}
		});
		$(".cCrud").off('change', '.cCrud-upload').on("change", ".cCrud-upload", function() {
			var container = cCrud.get_container(this);
			var data = cCrud.list_data(container);
			cCrud.upload_file(this, data, container);
			return false;
		});
		$(".cCrud").off('click', '.cCrud-remove-file').on("click", ".cCrud-remove-file", function() {
			var container = cCrud.get_container(this);
			var data = cCrud.list_data(container);
			cCrud.remove_file(this, data, container);
			return false;
		});
		$(".cCrud").off('click', '.cCrud_modal').on("click", ".cCrud_modal", function() {
			var content = $(this).data("content");
			var header = $(this).data("header");
			cCrud.modal(header, content);
			return false;
		});
		$(".cCrud").off('change', '.cCrud-mass-select').on("change", ".cCrud-mass-select", function() {
			if ($(this).val() == 1) {
				cCrud.get_container(this).find('.cCrud-mass-form-group').show(150);
			} else {
				cCrud.get_container(this).find('.cCrud-mass-form-group').hide(150);
			}

		});
		$(".cCrud").off('change', 'input.cCrud-mass-checkbox-header[type="checkbox"],input.cCrud-mass-checkbox-footer[type="checkbox"]').on("change", 'input.cCrud-mass-checkbox-header[type="checkbox"],input.cCrud-mass-checkbox-footer[type="checkbox"]', function() {
			if ($(this).is(':checked')) {
				$('input.cCrud-mass-checkbox[type="checkbox"]', cCrud.get_container(this)).prop("checked", true);
			} else {
				$('input.cCrud-mass-checkbox[type="checkbox"]', cCrud.get_container(this)).prop("checked", false);
			}
		});
		$(".cCrud").off('change', '.join_relation').on("change", ".join_relation", function() {
			cCrud.jr_request($(this));
			cCrud.depend_init(this);
		});
		$(".cCrud-ajax").each(function() {
			cCrud.init_datepicker(this);
			cCrud.init_datepicker_range($(this).find('.cCrud-columns-select option:selected').data('type'), this);
			cCrud.depend_init(this);
			cCrud.map_init(this);
			cCrud.check_fixed_buttons();
			cCrud.init_tooltips(this);
			cCrud.init_tabs(this);
			cCrud.check_message(this);
			cCrud.init_autosave();
			cCrud.hide_progress(this);
			cCrud.init_nestable(this);
			cCrud.init_select2(this);
			cCrud.init_checkbox(this);
			cCrud.init_columns_select(this);
			cCrud.init_mask(this);
			//$(".cCrud-input").first().focus();
		});
	}
	});
	// Usar window.load ao invés de document.ready para garantir que
	// todos os scripts externos (jQuery UI Timepicker, Select2, etc.) estejam carregados
	$(window).on('load', function() {
		console.log('[cCrud] Window loaded, initializing cCrud...');
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
$(document).on("cCrudbeforerequest", function(event, container) {
	cCrud.show_progress(container);
});
$(document).on("cCrudafterrequest", function(event, container) {
	cCrud.init_datepicker(container);
	cCrud.init_texteditor(container);
	cCrud.init_datepicker_range($(container).find('.cCrud-columns-select option:selected').data('type'), container);
	cCrud.depend_init(container);
	cCrud.map_init(container);
	cCrud.check_fixed_buttons();
	cCrud.init_tooltips(container);
	cCrud.init_tabs(container);
	cCrud.check_message(container);
	cCrud.init_nestable(container);
	cCrud.init_select2(container);
	cCrud.init_checkbox(container);
	cCrud.init_mask(container);
	cCrud.init_columns_select(container);
});
$(document).on("cCrudafterupload", function(event, container) {
	cCrud.check_message(container);
});
$(document).on("cCrudbeforedepend", function(event, container, data) {
	$('select[name="' + data.name + '"]').parent().find('.select2').remove();
});
$(document).on("cCrudafterdepend", function(event, container, data) {
});
$(document).on("cCrudafterjoinrelation", function(event, container) {
	cCrud.init_datepicker(container);
	cCrud.init_texteditor(container);
	cCrud.init_datepicker_range($(container).find('.cCrud-columns-select option:selected').data('type'), container);
	cCrud.map_init(container);
	cCrud.check_fixed_buttons();
	cCrud.init_tooltips(container);
	cCrud.init_tabs(container);
	cCrud.check_message(container);
	cCrud.init_nestable(container);
	cCrud.init_select2(container);
	cCrud.init_checkbox(container);
	cCrud.init_mask(container);
	cCrud.init_columns_select(container);
});
//
/** print */
$.extend({
	print_window: function(print_win, cCrud) {
		var data = {};
		$(cCrud).find(".cCrud-data").each(function() {
			data[$(this).attr("name")] = $(this).val();
		});
		data.task = 'print';
		$.ajax({
			data: data,
			success: function(out) {
				print_win.document.open();
				print_win.document.write(out);
				print_win.document.close();
				$(cCrud).find(".cCrud-data[name=key]:first").val($(print_win.document).find(".cCrud-data[name=key]:first").val());
				var ua = navigator.userAgent.toLowerCase();
				if ((ua.indexOf("opera") != -1)) { // opera fix
					$(print_win).load(function() {
						print_win.print();
					});
				} else {
					$(print_win).ready(function() {
						print_win.print();
					});
				}
			}
		});
	}
});
// 
/** upload */
$.extend({
	createUploadIframe: function(id, uri) {
		var frameId = 'jUploadFrame' + id;
		var iframeHtml = '<iframe id="' + frameId + '" name="' + frameId + '" style="position:absolute; top:-9999px; left:-9999px"';
		if (window.ActiveXObject) {
			if (typeof uri == 'boolean') {
				iframeHtml += ' src="' + 'javascript:false' + '"';
			} else if (typeof uri == 'string') {
				iframeHtml += ' src="' + uri + '"';
			}
		}
		iframeHtml += ' />';
		$(iframeHtml).appendTo(document.body);
		return $('#' + frameId).get(0);
	},
	createUploadForm: function(id, fileElementId, data) {
		var formId = 'jUploadForm' + id;
		var fileId = 'jUploadFile' + id;
		var form = $('<form  action="" method="POST" name="' + formId + '" id="' + formId + '" enctype="multipart/form-data"></form>');
		if (data) {
			for (var i in data.cCrud) {
				if (data.cCrud[i] == 'postdata') {
					/*
					 * for (var j in data.cCrud.postdata) { $('<input
					 * type="hidden" name="cCrud[postdata][' + j + ']" value="' +
					 * data.cCrud.postdata[j] + '" />').appendTo(form); }
					 */
				} else
					$('<input type="hidden" name="cCrud[' + i + ']" value="' + data.cCrud[i] + '" />').appendTo(form);
			}
		}
		var oldElement = $('#' + fileElementId);
		var newElement = $(oldElement).clone();
		$(oldElement).attr('id', fileId);
		$(oldElement).before(newElement);
		$(oldElement).appendTo(form);
		$(form).css('position', 'absolute');
		$(form).css('top', '-1200px');
		$(form).css('left', '-1200px');
		$(form).appendTo('body');
		return form;
	},
	ajaxFileUpload: function(s) {
		s = $.extend({}, $.ajaxSettings, s);
		var id = new Date().getTime();
		var form = $.createUploadForm(id, s.fileElementId, (typeof (s.data) == 'undefined' ? false : s.data));
		var io = $.createUploadIframe(id, s.secureuri);
		var frameId = 'jUploadFrame' + id;
		var formId = 'jUploadForm' + id;
		if (s.global && !$.active++) {
			$.event.trigger("ajaxStart");
		}
		var requestDone = false;
		var xml = {};
		if (s.global)
			$.event.trigger("ajaxSend", [xml, s]);
		var uploadCallback = function(isTimeout) {
			var io = document.getElementById(frameId);
			try {
				if (io.contentWindow) {
					xml.responseText = io.contentWindow.document.body ? io.contentWindow.document.body.innerHTML : null;
					xml.responseXML = io.contentWindow.document.XMLDocument ? io.contentWindow.document.XMLDocument : io.contentWindow.document;
				} else if (io.contentDocument) {
					xml.responseText = io.contentDocument.document.body ? io.contentDocument.document.body.innerHTML : null;
					xml.responseXML = io.contentDocument.document.XMLDocument ? io.contentDocument.document.XMLDocument : io.contentDocument.document;
				}
			} catch (e) {
			}
			if (xml || isTimeout == "timeout") {
				requestDone = true;
				var status;
				try {
					status = isTimeout != "timeout" ? "success" : "error";
					if (status != "error") {
						var data = $.uploadHttpData(xml, s.dataType);
						if (s.success)
							s.success(data, status);
						if (s.global)
							$.event.trigger("ajaxSuccess", [xml, s]);
					} else {
					}
				} catch (e) {
					status = "error";
				}
				if (s.global)
					$.event.trigger("ajaxComplete", [xml, s]);
				if (s.global && !--$.active)
					$.event.trigger("ajaxStop");
				if (s.complete)
					s.complete(xml, status);
				$(io).unbind();
				setTimeout(function() {
					try {
						$(io).remove();
						$(form).remove();
					} catch (e) {
					}
				}, 100);
				xml = null
			}
		};
		if (s.timeout > 0) {
			setTimeout(function() {
				if (!requestDone)
					uploadCallback("timeout");
			}, s.timeout);
		}
		try {
			var form = $('#' + formId);
			$(form).attr('action', s.url);
			$(form).attr('method', 'POST');
			$(form).attr('target', frameId);
			if (form.encoding) {
				$(form).attr('encoding', 'multipart/form-data');
			} else {
				$(form).attr('enctype', 'multipart/form-data');
			}
			$(form).submit();
		} catch (e) {
		}
		var ttt = 0;
		var ua = navigator.userAgent.toLowerCase();
		if ((ua.indexOf("opera") != -1)) { // opera fix
			$('#' + frameId).load(function() {
				ttt++;
				if (ttt == 2) {
					uploadCallback();
				}
			});
		} else {
			$('#' + frameId).on("load", uploadCallback);
		}
		return {
			abort: function() {
			}
		};
	},
	uploadHttpData: function(r, type) {
		data = (type == "xml" && !type) ? r.responseXML : r.responseText;
		if (type == "script")
			$.globalEval(data);
		if (type == "json")
			eval("data = " + data);
		return data;
	}
});
// Destaque das linhas ao passar o mouse, estilo Notion
$(document).on('mouseenter', '.cCrud-list .cCrud-row', function () {
    $(this).addClass('table-active');
}).on('mouseleave', '.cCrud-list .cCrud-row', function () {
    $(this).removeClass('table-active');
});


