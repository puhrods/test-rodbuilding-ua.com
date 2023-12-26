// LIVEOPENCART: Related Options
	
(function ($) { $.fn.liveopencart_RelatedOptions = function(p_params){
	
	$this = this;
	
	var inst = {
		
		each : function(collection, fn){
			for ( var i_item in collection ) {
				if ( !collection.hasOwnProperty(i_item) ) continue;
				if ( fn(collection[i_item], i_item) === false ) {
					return;
				}
			}
		},

		getSetting : function(p_setting_name, p_default) {
			if ( typeof(inst.ro_settings[p_setting_name]) != 'undefined' ) {
				if ( inst.ro_settings[p_setting_name] !== '0' ) {
					return inst.ro_settings[p_setting_name];
				} else {
					return 0;
				}
			} else {
				return p_default;
			}
		},
		
		bind : function(trigger_name, event_function) {
			inst.parentBlock.on(trigger_name, event_function);
		},
		trigger : function(trigger_name, values) {
			inst.getBlockOfOptions().trigger(trigger_name, values);
		},
		
		initRO : function(ro_init_cnt) {
			
			if ( !inst.ro_data || inst.ro_data == {} || inst.ro_data == [] ) {
				return; // no related options
			}
			
			// add specifically displayed options (quantity input/select per option value)
			inst.getOptionElement('[data-quantity-per-option][data-product-option-id]').each(function(){
				var product_option_id = $(this).attr('data-product-option-id');
				if ( $.inArray(product_option_id, inst.ro_product_options) !=-1 ) {
					inst.input_quantity_per_options.push( product_option_id );
				}
			});
			
			// assign for global variable
			inst.options_step_by_step = inst.getOptionsAsStepsFromHTML();
			
			if ( !inst.options_step_by_step.length && !ro_init_cnt ) {
				// in some themes page options may be not available on this stage, so recall init on document.ready
				$().ready( function() {
					inst.initRO(1);
				});
				return;
			}
			
			
			inst.each(inst.options_step_by_step, function(option_step){ // we need all options, even not visible on this action
				inst.all_values_of_options[option_step] = inst.getAllValuesOfProductOption(option_step);
			});
		
			inst.getOptionElement('select[name^="'+inst.option_prefix+'"]').change(function(){
				inst.controlAccessToValuesOfAllOptions();
			});
			
			inst.getOptionElement('input[type=radio][name^="'+inst.option_prefix+'"]').change(function(){
				inst.controlAccessToValuesOfAllOptions();
			});
			
			inst.controlAccessToValuesOfAllOptions();
			
			$().ready( function() {
			
				if ( typeof(inst.ros_to_select) != 'undefined' && inst.ros_to_select && inst.ros_to_select.length) {
					let ro_id_last = false;
					if ( !inst.poip_ov ) {
					
						inst.each(inst.ros_to_select, function(ro_id){
							ro_id_last = ro_id;
							inst.setSelectedCombination(ro_id, true); // without limitaions
						});
						if (ro_id_last) {
							inst.setSelectedCombination(ro_id_last); /// with limitations
						}
					}
					
				} else {
			
					// if there's filter and it's equal to related options model - this related options combination should be selected
					if (!inst.setSelectedCombinationByModel(inst.filter_name)) { // if there's not filter relevant to related options 
						// if any value is selected - all values should be reselected (to be relevant to available related options)
						// commented out (should not be used?)
						//if ( inst.poip_ov ) {
						//	setTimeout(function () {
						//		ro_use_first_values();
						//	}, 1); // if any combination is selected (may be other extension), check it and change if it's not relevant to available related options
						//}
					}
					inst.controlAccessToValuesOfAllOptions();
					
					inst.initialAutoSelect();
					
				}
			
			});
			
			inst.trigger('init_after.ro');
			
		},
		
		getOptionsAsSteps : function() {
			
			let steps = [];
			inst.each(inst.options_step_by_step, function(product_option_id){
				// skip options hidden by pcop
				if ( typeof(pcop_front) != 'undefined' && typeof(pcop_front.getHiddenOptions) == 'function' ) {
					if ( $.inArray(product_option_id, pcop_front.getHiddenOptions()) == -1 ) {
						steps.push(product_option_id);
					}
				} else {
					steps.push(product_option_id);
				}
				
			});
			
			return steps;
		},
		
		getQuantityInput : function() {
			return inst.getElement('#input-quantity, input[name="quantity"]');
		},
		
		setOptionValue : function(product_option_id, product_option_value_id) {
			
			var $touched_elems = '';
			if ( inst.getOptionElement('select[name="'+inst.option_prefix+'['+product_option_id+']"]').length > 0) {
				
				$touched_elems = inst.getOptionElement('[name="'+inst.option_prefix+'['+product_option_id+']"]').val(product_option_value_id);
				
			} else if ( inst.getOptionElement('input:radio[name="'+inst.option_prefix+'['+product_option_id+']"]').length > 0) {
				var radio_elems = inst.getOptionElement('input:radio[name="'+inst.option_prefix+'['+product_option_id+']"]');
				radio_elems.prop('checked', false);
				
				if ( product_option_value_id ) {
					var radio_elem = inst.getOptionElement('input:radio[name="'+inst.option_prefix+'['+product_option_id+']"][value='+product_option_value_id+']');
					radio_elem.prop('checked', true);
				}
				
				$touched_elems = radio_elems;
			}
			
			inst.trigger('setOptionValue_after.ro', [product_option_id, product_option_value_id, $touched_elems]);
			
			// Product Option Image PRO compatibility
			if ( typeof(poipExternalOptionChange)=='function' ) {
				poipExternalOptionChange();
			}
			if ( typeof(poip_product)!='undefined' && typeof(poip_product.externalOptionChange) == 'function' ) {
				poip_product.externalOptionChange();
			}
			
		},
		
		getBlockOfOptions : function() {
			return inst.parentBlock;
		},
		
		getOptionElement : function(selector) {
			return inst.getBlockOfOptions().find(selector);
		},
		
		getProductOptionIdFromName : function(name) {
			return name.substr(inst.option_prefix_length+1, name.indexOf(']')-(inst.option_prefix_length+1) );
		},
		
		getElement : function(selector) {
			return inst.parentBlock.find(selector);
		},
    
		getValuesOfSelectOption : function(param_product_option_id) {
			if ( $.isEmptyObject(inst.all_select_ov) ) {
				inst.getOptionElement('select[name^="'+inst.option_prefix+'["]').each( function (si, sel_elem) {
					var product_option_id = inst.getProductOptionIdFromName(sel_elem.name);
					
					inst.all_select_ov[product_option_id] = [];
					
					$.each(sel_elem.options, function (oi, op_elem) {
						inst.all_select_ov[product_option_id].push(op_elem.value);
					});
					
				} );
			}
			return inst.all_select_ov[param_product_option_id];
		},
		
		getCurrentROIds : function(options_values) {
			var ro_ids = [];
			inst.each(inst.ro_data, function(ro_dt){
			//for (var i in inst.ro_data) {
			//	if ( !inst.ro_data.hasOwnProperty(i) ) continue; 
			//
			//	var ro_dt = inst.ro_data[i];
		
				var all_ok;
				inst.each(ro_dt.ro, function(ro_comb){
					let ro_id = ro_comb.relatedoptions_id;
				//for(var ro_id in ro_dt.ro) {
				//	if ( !ro_dt.ro.hasOwnProperty(ro_id) ) continue; 
					
					all_ok = true;
					inst.each(ro_comb.options, function(ro_option, product_option_id){
					//for(var product_option_id in ro_comb.options) {
					//	if ( !ro_comb.options.hasOwnProperty(product_option_id) ) continue; 
						
						if (!(product_option_id in options_values && options_values[product_option_id]==ro_option)) {
							all_ok = false;
						}
					});
					if (all_ok) ro_ids.push(ro_id); 
				});
			});
			return ro_ids;
		},
		
		arrayIntersection : function(arr1, arr2) {
			var new_arr = [];
			for (let i in arr1) {
				if ( arr1.hasOwnProperty(i) ) {
					if ($.inArray(arr1[i], arr2) != -1) {
						new_arr.push(arr1[i]);
					}
				}
			}
			return new_arr;
		},
		
		arrayCopy : function(arr) {
			var new_arr = [];
			for (let i in arr) {
				if ( arr.hasOwnProperty(i) ) {
					if ( $.isArray(arr[i]) ) {
						new_arr[i] = inst.arrayCopy(arr[i]);
					} else {
						new_arr[i] = arr[i];
					}
				}
			}
			return new_arr;
		},
		
		getAllValuesOfProductOption : function(product_option_id) {
			var values = [];
			if ( inst.getOptionElement('select[name="'+inst.option_prefix+'['+product_option_id+']"]').length) {
				var select_options = inst.getValuesOfSelectOption(product_option_id);
				inst.each(select_options, function(select_option){
				//for (var i=0;i<select_options.length;i++) {
					if (select_option) {
						values.push(select_option);
					}
				});
			} else if ( inst.getOptionElement('input[type=radio][name="'+inst.option_prefix+'['+product_option_id+']"]').length) {
				inst.getOptionElement('input[type=radio][name="'+inst.option_prefix+'['+product_option_id+']"]').each(function(){
					values.push($(this).val());
				});
			}
			// add specifically displayed options (quantity input/select per option value)
			if ( $.inArray(product_option_id, inst.input_quantity_per_options) != -1 ) {
				$('[name^="quantity_per_option['+product_option_id+']["]').each(function(){
					var product_option_value_id = $(this).attr('data-value');
					if ( product_option_value_id ) {
						values.push(product_option_value_id);
					}
				});	
			}
			return values;
		},
		
		getOptionsWithDirectRelations : function(prodict_option_id) {
		
			if ( inst.linked_options_cache[prodict_option_id] ) {
				return inst.linked_options_cache[prodict_option_id];
			} else {
		
				var linked_options = [];
				inst.each(inst.ro_data, function(ro_dt){
				//for (var i in inst.ro_data) {
					if ( $.inArray(prodict_option_id, ro_dt.options_ids)!=-1 ) {
						inst.each(ro_dt.options_ids, function(current_option_id){
						//for ( var j in inst.ro_data[i].options_ids ) {
						//	current_option_id = inst.ro_data[i].options_ids[j];
							if ( current_option_id != prodict_option_id && $.inArray(current_option_id, linked_options) ) {
								linked_options.push(current_option_id);
							}
						});
					}
				});
				inst.linked_options_cache[prodict_option_id] = linked_options;
				return linked_options;
			}
		},
		
		// get available options values
		// option_id - (product_option_id)
		// param_options_values - current options values (selected) - only for related options
		// param_skip_ropv_ids - don't make values addition for this related options combinations
		getAccessibleOptionValues : function(option_id, param_options_values, param_skip_options) {
			
			// make copies od arrays
			let options_values = inst.arrayCopy(param_options_values);
			let skip_options = inst.arrayCopy(param_skip_options);
			skip_options.push(option_id);
			
			let linked_options = inst.getOptionsWithDirectRelations(option_id);
			
			inst.each(linked_options, function(current_option_id){
			//for ( var i in linked_options ) {
			//	current_option_id = linked_options[i];
			//for (var current_option_id in options_values) {
				if ( options_values[current_option_id] && !options_values[current_option_id].length && current_option_id != option_id) {
					if ( $.inArray(current_option_id, skip_options) == -1 ) {
						options_values[current_option_id] = inst.getAccessibleOptionValues(current_option_id, options_values, skip_options);
					}
				}
			});
			
			let common_accessible_values = false;
			
			let possible_current_option_values = inst.all_values_of_options[option_id];
			
			inst.each(inst.ro_data, function(ro_dt){
			
				if ($.inArray(option_id, ro_dt.options_ids)==-1) {
					return;
				}
				
				let accessible_values = [];
				let ro_combs = ro_dt.ro;
				
				let options_for_check = []; // optimization
				inst.each(options_values, function(values, current_option_id){
				//for(var current_option_id in options_values) {
					if (current_option_id != option_id && values.length && $.inArray(current_option_id, skip_options) == -1 && $.inArray(current_option_id, ro_dt.options_ids)!=-1) {
						options_for_check.push(current_option_id);
					}
				});
				
				if (!options_for_check.length) {
					if (ro_dt.options_ids.length == 1) { // combination contains only one option (this option)
						inst.each(ro_combs, function(ro_comb){
						//for(var ro_id in ro_combs) {
						//	if( !ro_combs.hasOwnProperty(ro_id) ) continue;
							if ( ro_comb.options[option_id] && $.inArray(ro_comb.options[option_id], accessible_values) == -1 ) {
								accessible_values.push(ro_comb.options[option_id]);
							}
						});
					} else {
						accessible_values = inst.arrayCopy(possible_current_option_values);
					}
				} else {
					inst.each(ro_combs, function(ro_comb){
						
						all_ok = true;
						inst.each(options_for_check, function(current_option_id){
							if ( $.inArray(ro_comb.options[current_option_id], options_values[current_option_id]) == -1  ) {
								all_ok = false;
							}
							if (!all_ok) {
								return false;
							}
						});
						
						if (all_ok && ($.inArray(ro_comb.options[option_id], accessible_values) == -1 )) {
							accessible_values.push(ro_comb.options[option_id]);
							if (possible_current_option_values.length == accessible_values.length) { // optimization
								return false;
							}
						}
					});
				}
				
				if (common_accessible_values === false) {
					common_accessible_values = accessible_values;
				} else {
					common_accessible_values = inst.arrayIntersection(common_accessible_values, accessible_values);
				}
			});
			
			return common_accessible_values;
		},
		
		// only for options with values
		// returns array of accessible values
		controlAccessToValuesOfOption : function(param_options_values, option_id) {
			
			var options_values = [];
			inst.each(param_options_values, function(product_option_value_id, product_option_id){
			//for (var product_option_id in param_options_values) {
				options_values[product_option_id] = [];
				if (param_options_values[product_option_id]) {
					options_values[product_option_id].push(product_option_value_id);
				}
			});
			var skip_ropv_ids = [];
			var accessible_values = inst.getAccessibleOptionValues(option_id, options_values, skip_ropv_ids);
			
			inst.setAccessibleOptionValues(option_id, accessible_values);
			
			return accessible_values;
		},
		
		toggleOptionElement : function(option_id, toggle_flag) {
			inst.getOptionElement('#input-option'+option_id).parent().toggle(toggle_flag); 
			if ( inst.getOptionElement('#input-option'+option_id).parent().is('div.select') && inst.getOptionElement('#input-option'+option_id).parent().parent().is('div.form-group') ) { // additional compatibility (fastor theme or custom modification)
				inst.getOptionElement('#input-option'+option_id).parent().parent().toggle(toggle_flag);
			}
			inst.getOptionElement('label[for="input-option'+option_id+'"]').toggle(toggle_flag);
		},
		
		optionIsAccessible : function(option_id) {
			if ( inst.getOptionElement('select[name="'+inst.option_prefix+'['+option_id+']"]').length) {
				return inst.getOptionElement('select[name="'+inst.option_prefix+'['+option_id+']"] option[value][value!=""]:not(:disabled)').length ? true : false;
			} else if ( inst.getOptionElement('input[type=radio][name="'+inst.option_prefix+'['+option_id+']"]').length) {
				return inst.getOptionElement('input[type=radio][name="'+inst.option_prefix+'['+option_id+']"]:not(:disabled)').length ? true : false;
			} else if ( inst.getOptionElement('input[name^="quantity_per_option['+option_id+']["]').length) {
				return inst.getOptionElement('input[name^="quantity_per_option['+option_id+']["]:not(:disabled)').length ? true : false;
			}
		},
		
		hideInaccessibleOptionIfNeeded : function(option_id) {
			if (inst.ro_settings && inst.ro_settings.hide_option) {
				inst.toggleOptionElement(option_id, inst.optionIsAccessible(option_id));
			}
		},
		
		makeInaccessibleOptionsNotRequired : function(option_id) {
			if (inst.ro_settings && inst.ro_settings.unavailable_not_required) {
				var current_ids = [];
				if ($('#ro_not_required').length) {
					current_ids = $('#ro_not_required').val().split(',');
				} else {
					$('#product').append('<input type="hidden" name="ro_not_required" id="ro_not_required" value="">');
				}
				var new_ids = [];
				inst.each(current_ids, function(current_id){
				//for (var i in current_ids) {
					if (current_id != option_id) {
						new_ids.push(current_id);
					}
				});
				if (!inst.optionIsAccessible(option_id)) {
					new_ids.push(option_id);
				}
				$('#ro_not_required').val( new_ids.toString());
			}
		},
		
		setAccessibleOptionValues : function(option_id, accessible_values) {
			
			var current_value = ( inst.getOptionElement('input[type=radio][name="'+inst.option_prefix+'['+option_id+']"]:checked').val() || inst.getOptionElement('select[name="'+inst.option_prefix+'['+option_id+']"]').val());
		
			if ( inst.getOptionElement('select[name="'+inst.option_prefix+'['+option_id+']"]').length) {
			
				if (current_value && $.inArray(current_value, accessible_values)==-1) {
					inst.getOptionElement('select[name="'+inst.option_prefix+'['+option_id+']"]').val("");
				}
				
				if (inst.hide_inaccessible) {
				
					let select_options = inst.getValuesOfSelectOption(option_id);
					//select_options = inst.all_select_ov[inst.option_prefix+"["+option_id+"]"];
					inst.each(select_options, function(select_option){
					//for (let i=0;i<select_options.length;i++) {
						if (select_option) {
							option_value_disabled = ($.inArray(select_option,accessible_values) == -1);
							// hiding options for IE
							inst.getOptionElement('select[name="'+inst.option_prefix+'['+option_id+']"]').toggleOption(select_option, !option_value_disabled);
							inst.trigger( 'setAccessibleOptionValues_selectToggle_after.ro', [option_id, select_option, option_value_disabled] );
						}
					});
					
				} else {
				
					let select_options = inst.getOptionElement('select[name="'+inst.option_prefix+'['+option_id+']"]')[0].options;
					inst.each(select_options, function(select_option){
					//for (let i=0;i<select_options.length;i++) {
						if (select_option.value) {
							option_value_disabled = ($.inArray(select_option.value,accessible_values) == -1);
							select_option.disabled = option_value_disabled;
							if (option_value_disabled) {
								$(select_option).addClass('ro_option_disabled');
							} else {
								$(select_option).removeClass('ro_option_disabled');
							}
							inst.trigger( 'setAccessibleOptionValues_selectEnableDisable_after.ro', [option_id, select_option.value, option_value_disabled] );
						}
					});
				}
				
				inst.trigger( 'setAccessibleOptionValues_select_after.ro', [option_id, accessible_values] );
				
			} else if ( inst.getOptionElement('input[type=radio][name="'+inst.option_prefix+'['+option_id+']"]').length) {	
				
				if (current_value && $.inArray(current_value, accessible_values)==-1) {
				
					var elem_to_uncheck = inst.getOptionElement('input[type=radio][name="'+inst.option_prefix+'['+option_id+']"]:checked');
					
					if ( elem_to_uncheck.length ) {
						elem_to_uncheck.prop('checked', false);
					}
					
					inst.trigger( 'setAccessibleOptionValues_radioUncheck_after.ro', [elem_to_uncheck] );
				}
				
				$radio_options = inst.getOptionElement('input[type=radio][name="'+inst.option_prefix+'['+option_id+']"]');
				$radio_options.each(function(){
				//for (var i=0;i<radio_options.length;i++) {
					let $radio_option = $(this);
					
					option_value_disabled = ($.inArray($radio_option.val(), accessible_values) == -1);
					
					$radio_option.prop('disabled', option_value_disabled); // hidden should be disabled too
					
					if (inst.hide_inaccessible) {
					
						if ( typeof(inst.custom_radioToggle) != 'undefined' && inst.custom_radioToggle( $radio_option, option_value_disabled ) ) {
							// do nothing
						} else {
							$radio_option.parent().parent().toggle(!option_value_disabled);
							$radio_option.toggle(!option_value_disabled);
						}
						
						// style change for padding change
						if (option_value_disabled) {
							if ($radio_option.parent().parent().hasClass('radio')) {
								$radio_option.parent().parent().removeClass('radio');
								$radio_option.parent().parent().addClass('_radio_ro');
							}
						} else {
							if ($radio_option.parent().parent().hasClass('_radio_ro')) {
								$radio_option.parent().parent().removeClass('_radio_ro');
								$radio_option.parent().parent().addClass('radio');
							}
						}
						
						inst.trigger( 'setAccessibleOptionValues_radioToggle_after.ro', [ option_id, $radio_option, option_value_disabled ] );
						
					} else {
						
						if ( typeof(inst.custom_radioEnableDisable) != 'undefined' && inst.custom_radioEnableDisable( $radio_option, option_value_disabled ) ) {
							// do nothing
						} else {
							
							if (option_value_disabled) {
								$radio_option.parent().fadeTo("fast", 0.1);
							} else {
								$radio_option.parent().fadeTo("fast", 1);
							}
							
						}
						
						inst.trigger( 'setAccessibleOptionValues_radioEnableDisable_after.ro', [ option_id, $radio_option, option_value_disabled ] );
						
					}
				//}
				});
				
			} else if ( $.inArray(option_id, inst.input_quantity_per_options) != -1 ) { // for specifically displayed options (quantity input/select per option value)
				
				inst.getOptionElement('[name^="quantity_per_option['+option_id+']["]').each(function(){
					var $qpo_input = $(this);
					var product_option_value_id = qpo_getPOVIdByName( $qpo_input.attr('name') );
					//var product_option_value_id = $qpo_input.attr('data-value');
					if ( product_option_value_id ) {
						option_value_disabled = ( $.inArray(product_option_value_id, accessible_values) == -1 );
						if ( option_value_disabled ) {
							$qpo_input.val('');
						} else if ( $qpo_input.prop('disabled') ) { // re-enable the input - place default values
							$qpo_input.val( $qpo_input.attr('data-default-value' || '0') );
						}
						$qpo_input.prop('disabled', option_value_disabled);
						var $option_value_container = $qpo_input.closest('tr');
						if ( !$option_value_container.length ) {
							$option_value_container = $qpo_input.closest('div');
						}
						if (inst.hide_inaccessible) {
							$option_value_container.toggle(!option_value_disabled);
						} else {
							if ( option_value_disabled ) {
								$option_value_container.fadeTo("fast", 0.1);
							} else {
								$option_value_container.fadeTo("fast", 1);
							}
						}
					}
				});
				
			}
			
			inst.hideInaccessibleOptionIfNeeded(option_id);
			inst.makeInaccessibleOptionsNotRequired(option_id);
		},
		
		getOptionValues : function() {
			
			var options_values = {};
		
			inst.getOptionElement('select[name^="'+inst.option_prefix+'["], input[type=radio][name^="'+inst.option_prefix+'["]').each(function(){
				option_id = inst.getProductOptionIdFromName( $(this).attr('name') );
				
				if ($.inArray(option_id,inst.ro_product_options) != -1) {
					
					if ( typeof(options_values[option_id]) == 'undefined' ) {
						options_values[option_id] = 0;
					}
					
					if ( $(this).find('option[value]').length ) { // select
						options_values[option_id] = $(this).val();
					} else { // radio
						if ( $(this).is(':checked') ) {
							options_values[option_id] = $(this).val();
						}
					}
					
				}
			});
			
			// add specifically displayed options (quantity input/select per option value)
			inst.each(inst.input_quantity_per_options, function(product_option_id){
			//for (var i_input_quantity_per_options in inst.input_quantity_per_options) {
			//	if ( !inst.input_quantity_per_options.hasOwnProperty(i_input_quantity_per_options) ) continue;
			//	var product_option_id = inst.input_quantity_per_options[i_input_quantity_per_options];
				
				options_values[product_option_id] = 0;
			});
			
			return options_values;
		},
    
		getSelectedRelatedOptions : function(){
			let related_options = inst.getOptionValues();
			let selected_related_options = {};
			inst.each(related_options, function(pov_id, po_id){
				if (pov_id) {
					selected_related_options[po_id] = pov_id;
				}
			});
			return selected_related_options;
		},
		
		setSelectedCombination : function(ro_id, skip_access) {
			
			let options_to_set = {};
			//var selected_povs = [];
			if (inst.ro_data) {
				inst.each(inst.ro_data, function(ro_dt){
				//for (var i in inst.ro_data) {
					if (ro_dt.ro[ro_id]) {
						
						inst.each(ro_dt.ro[ro_id].options, function(pov_id, po_id){
							options_to_set[po_id] = pov_id;
						//for (var product_option_id in ro_dt.ro[ro_id].options) {
							//inst.setOptionValue(po_id, pov_id);
							//selected_povs.push(ro_dt.ro[ro_id].options[po_id]);
						});
						
						return false;
					}
				});
			}

			if ( !$.isEmptyObject(options_to_set) ) { // we should set option values in the order of options on the page (to make it working with the step-by-step mode)
				inst.each(inst.getOptionsAsSteps(), function(po_id){
				//inst.each(inst.options_step_by_step, function(po_id){
					if ( options_to_set[po_id] ) {
						inst.setOptionValue(po_id, options_to_set[po_id]);
						if ( inst.step_by_step && inst.hide_inaccessible ) { // control access, otherwise next option may do not have values to make selected at all
							inst.controlAccessToValuesOfAllOptions();
						}
					}
				});
			}
		
			// access should be checked also for step by step because there may be selects with removed options
			if ( typeof(skip_access) == 'undefined' || !skip_access || (inst.step_by_step && inst.hide_inaccessible) ) { 
	
				inst.controlAccessToValuesOfAllOptions();
				
				inst.trigger('setSelectedCombination_withAccessControl_after.ro');
				
			}
			
			inst.executeFunctionsFromOtherExtensionsOnOptionChange();
		},
		
		executeFunctionsFromOtherExtensionsOnOptionChange : function() {
			
			// Parent-child options - compatibility
			if ( typeof(pcop_front) != 'undefined' && typeof(pcop_front.checkVisibility) == 'function' ) {
				pcop_front.checkVisibility();
			}
			
			// very old Product Option Image PRO compatibility
			if ( typeof(poipExternalOptionChange)=='function' ) {
				poipExternalOptionChange();
			}
			// old Product Option Image PRO compatibility
			if ( typeof(poip_product)!='undefined' && typeof(poip_product.externalOptionChange) == 'function' ) {
				poip_product.externalOptionChange();
			}
			
			if ( typeof(poip_common)!='undefined' && poip_common && typeof(poip_common.externalOptionChangeEvent) == 'function' ) {
				poip_common.externalOptionChangeEvent( inst.getBlockOfOptions() );
			}
			
			// Live Price OLD
			if ( typeof(liveprice_recalc) == 'function' ) {
				liveprice_recalc(100);
			}
			
			// Live Price NEW
			if ( window.liveopencart && window.liveopencart.live_price_instances ) {
				var lp_instances = window.liveopencart.live_price_instances;
				if ( Array.isArray(lp_instances) && lp_instances.length ) {
					inst.each(lp_instances, function(lp_instance){
					//for ( var i_lp_instances in lp_instances ) {
					//	if ( !lp_instances.hasOwnProperty(i_lp_instances) ) continue;
					//	var lp_instance = lp_instances[i_lp_instances];
						lp_instance.updatePrice(100);
					});
				}
			}
			
			$(document).trigger('external_option_change'); // io supported
		},
		
		setSelectedCombinationByModel : function(model) {
			if (model && inst.ro_data) {
				inst.each(inst.ro_data, function(ro_dt){
				inst.each(ro_dt.ro, function(ro_comb, ro_id){
					//for (var ro_id in ro_dt.ro) {
						if (ro_comb.model && ro_comb.model.toLowerCase() == model.toLowerCase()) {
							ro_set_selected_combination(ro_id);
							return true;
						}
					});
				});
			}
			return false;
		},
		
		// for step-by-step way
		getOptionsAsStepsFromHTML : function() {
			var options_steps = [];
			
			inst.getOptionElement('input[name^="'+inst.option_prefix+'["], select[name^="'+inst.option_prefix+'["]').each(function(){
			
				let product_option_id = inst.getProductOptionIdFromName( inst.getOptionElement(this).attr('name'));
				
				if ($.inArray(product_option_id, inst.ro_product_options) != -1) {
					if ($.inArray(product_option_id, options_steps) == -1) {
						options_steps.push(product_option_id);
					}
				}
				
			});
			
			// add specifically displayed options (quantity input/select per option value)
			inst.each(inst.input_quantity_per_options, function(product_option_id){
			//for (var i_input_quantity_per_options in inst.input_quantity_per_options) {
			//	if ( !inst.input_quantity_per_options.hasOwnProperty(i_input_quantity_per_options) ) continue;
			//	let product_option_id = inst.input_quantity_per_options[i_input_quantity_per_options];
				options_steps.push(product_option_id);
			});
			
			return options_steps;
		},
		
		controlAccessToValuesOfAllOptions : function() {
		
			if (!inst.ro_data || !Object.keys(inst.ro_data).length) return;
			
			if (inst.step_by_step) {
			
				var prev_options_values = {};
				var prev_options = [];
				var option_accessible_values = [];
				var one_prev_value_is_not_set = false;
				
				
				inst.each(inst.getOptionsAsSteps(), function(option_step, i){
				//inst.each(inst.options_step_by_step, function(option_step, i){
				//for (let i=0;i<inst.options_step_by_step.length;i++) {
				
					if (i>0) {
						// if previous option value is selected or if previous option has no available values
						if (!one_prev_value_is_not_set) {
							// limitaion on previous
							option_accessible_values = inst.controlAccessToValuesOfOption(prev_options_values, option_step);
							
						} else {
							// disable all
							inst.setAccessibleOptionValues(option_step, []);
							option_accessible_values = [];
						}
					}
					
					prev_options.push( ( inst.getOptionElement('input[type=radio][name="'+inst.option_prefix+'['+option_step+']"]:checked').val() || inst.getOptionElement('select[name="'+inst.option_prefix+'['+option_step+']"]').val()) );
					prev_options_values[option_step] = prev_options[prev_options.length-1];
					
					if ((option_accessible_values.length || i==0) && !prev_options[i] ) { // option has available values, but none of them is selected
						one_prev_value_is_not_set = true;
					}
					
				});
			
			} else {
			
				//var options_keys = [];
				var options_values = inst.getOptionValues();
				var options_keys = Object.keys(options_values);
				
				inst.each(options_keys, function(option_key){
				//for (let i=0;i<options_keys.length;i++) {
					inst.controlAccessToValuesOfOption(options_values, option_key);
				});
				
			}
			
			inst.checkAutoSelect();
			
			inst.trigger('controlAccessToValuesOfAllOptions_after.ro');
			
		},
		
		// autoselection for last available option value & first available always
		checkAutoSelect : function() {
		
			if (inst.auto_select_last || inst.auto_select_first_always) {
				
				inst.each(inst.getOptionsAsSteps(), function(product_option_id){
				//inst.each(inst.options_step_by_step, function(product_option_id){
				//for (var i in inst.options_step_by_step) {
					
					//var product_option_id = inst.options_step_by_step[i];
					
					if ( inst.getOptionElement('select[name="'+inst.option_prefix+'['+product_option_id+']"]').length ) {
						
						var options_elems = inst.getOptionElement('select[name="'+inst.option_prefix+'['+product_option_id+']"]').find('option[value][value!=""]:not(:disabled)');
						
						if ( inst.auto_select_last && options_elems.length == 1 && !$(options_elems[0]).is(':selected')	|| ( inst.auto_select_first_always && options_elems.length && !options_elems.filter(':selected').length ) ) {
						
							let product_option_value_id = $(options_elems[0]).val();
							
							inst.setOptionValue(product_option_id, product_option_value_id);
							$(options_elems[0]).parent().change();
							return;
						}
						
					} else if ( inst.getOptionElement('input:radio[name="'+inst.option_prefix+'['+product_option_id+']"]').length ) {
					
						var radio_elems = inst.getOptionElement('input:radio[name="'+inst.option_prefix+'['+product_option_id+']"]:not(:disabled)');
						
						if ( inst.auto_select_last && radio_elems.length == 1 && !$(radio_elems[0]).is(':checked') || ( inst.auto_select_first_always && radio_elems.length && !radio_elems.filter(':checked').length ) ) {
							
							let product_option_value_id = $(radio_elems[0]).val();
							
							inst.setOptionValue(product_option_id, product_option_value_id);
							
							$(radio_elems[0]).change();
							return;
							
						}
					}
				});
			}
		},
		
		// autorelection for first values (initial)
		initialAutoSelect : function() {
			
			if (inst.ro_settings && inst.ro_settings.select_first && inst.ro_settings.select_first == 1) {
				inst.each(inst.getOptionsAsSteps(), function(product_option_id){
				//inst.each(inst.options_step_by_step, function(product_option_id){
					
					//for (var i in inst.options_step_by_step) {
					//	if (!inst.options_step_by_step.hasOwnProperty(i)) continue;
					//	
					//	var product_option_id = inst.options_step_by_step[i];
						
					if ( inst.getOptionElement('select[name="'+inst.option_prefix+'['+product_option_id+']"]').length ) {
						
						let elem = inst.getOptionElement('select[name="'+inst.option_prefix+'['+product_option_id+']"]');
						if ( !elem.val() ) {
						
							let elem_option = elem.find('option[value][value!=""]:not(:disabled)');
							if (elem_option.length) {
							
								inst.setOptionValue(product_option_id, elem_option.val() );
							
								elem.change();
							}
							
						}
						
					} else if ( inst.getOptionElement('input:radio[name="'+inst.option_prefix+'['+product_option_id+']"]').length ) {
						
						if ( !inst.getOptionElement('input:radio[name="'+inst.option_prefix+'['+product_option_id+']"]:checked').length ) {
							let elem = inst.getOptionElement('input:radio[name="'+inst.option_prefix+'['+product_option_id+']"]:not(:disabled):first');
							if (elem.length) {
								
								inst.setOptionValue(product_option_id, elem.attr('value') );
								
								elem.change();
								
							}
						}
					}
				
				
				});
			}
		},
		
		
	};
	
	inst.parentBlock = this;
	
	var params = $.extend( {
		'ro_settings' 	: {},
		'ro_texts'			: {},
		'ro_data' 			: false,
		'ro_theme_name' : '',
		'ros_to_select' : '',
		'poip_ov' 			: '',
		'filter_name'		: '',
		'product_id'		: 0,
	}, p_params);
	inst.ro_settings 		= params.ro_settings;
	inst.ro_theme_name 		= params.ro_theme_name;
	inst.ro_texts 			= params.ro_texts;
	inst.ro_data 			= params.ro_data;
	inst.ros_to_select 		= params.ros_to_select;
	inst.poip_ov 			= params.poip_ov;
	inst.filter_name 		= params.filter_name;
	inst.product_id 		= params.product_id;
	
	// settings
	inst.hide_inaccessible 	= inst.getSetting('hide_inaccessible');
	inst.step_by_step 		= inst.getSetting('step_by_step');
	
	inst.auto_select_last 	= inst.getSetting('select_first') == 2;
	inst.auto_select_first_always = inst.getSetting('select_first') == 3;
	
	inst.option_prefix = "option";
	if ( !$('[name^="option["]').length ) {
		if ( $('[name^="option_oc["]').length ) { // mijoshop
			inst.option_prefix = "option_oc";
		}
	}
	inst.option_prefix_length = inst.option_prefix.length;
	
	// data
	inst.ro_product_options = [];
	if ( inst.ro_data ) {
		inst.each(inst.ro_data, function(ro_dt){
		//for ( var i_ro_data in inst.ro_data ) {
		//	if ( !inst.ro_data.hasOwnProperty(i_ro_data) ) continue;
			var option_ids = ro_dt.options_ids;
			inst.each(option_ids, function(product_option_id){
			//for ( var i_option_ids in option_ids ) {
			//	if ( !option_ids.hasOwnProperty(i_option_ids) ) continue;
			//	var product_option_id = option_ids[i_option_ids];
				if ( $.inArray(product_option_id, inst.ro_product_options) == -1 ) {
					inst.ro_product_options.push(product_option_id);
				}
			});
		});
	}
	//var ro_exists = false;
	
	// variables 
	inst.linked_options_cache = {}; // cache
	inst.all_select_ov = {}; // selects cache
	inst.input_quantity_per_options = [];
	inst.ro_stock_control_last_call = '';
	inst.options_types = [];
	inst.options_step_by_step = [];
	inst.all_values_of_options = [];
		
	//inst.initRO(); // init should be started from outside, after assigning all additional functions/triggers
	
	$this.data('liveopencart_relatedOptions', inst);
	
	if ( !window.liveopencart ) {
		window.liveopencart = {};
	}
	if ( !window.liveopencart.related_options_instances ) {
		window.liveopencart.related_options_instances = [];
	}
	window.liveopencart.related_options_instances.push(inst);
	
	return inst;

}; })(jQuery);

	
	

	