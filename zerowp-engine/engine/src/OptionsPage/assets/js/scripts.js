;(function( $ ) {

	"use strict";

	$.fn.Zwe_OptionsPage = function( options ) {

		if (this.length > 1){
			this.each(function() {
				$(this).Zwe_OptionsPage(options);
			});
			return this;
		}

		// Defaults
		var settings = $.extend({}, options );

		// Cache current instance
		var plugin = this;

		//Plugin go!
		var init = function() {
			plugin.build();
		}

		// Build structure
		this.build = function() {
			var self = false;

			var Zwe_OptionsPage_Base = {

				/*
				-------------------------------------------------------------------------------
				Form AJAX processing
				-------------------------------------------------------------------------------
				*/
				ajaxProcess: function(){
					plugin.on( 'submit', 'form', function( event ){
						event.preventDefault();

						self.clearErrors();
						self.disableButton();

						var _form = $(this);
						var _form_data = _form.serialize();
						var _form_action = _form.find('input[name="_form_action"]').val();

						$.ajax({
							method: _form.attr('method'),
							url: ajaxurl,
							data: {
								'action': _form_action,
								'form': _form_data,
							},
							success: function( response ){
								if( response != 0 ){
									response = JSON.parse( response );
									console.log( response );
									if( response.status ){
										if( 'fail' == response.status ){
											self.msgError( response.message );
											if( response.errors ){
												$.each( response.errors, function( _index, _value ){
													self.fieldError( _index, _value );
												});
											}
										}
										else if( 'success' == response.status ){
											self.msgSuccess( response.message );
										}
									}
								}
								else{
									self.msgError( 'Cheating?!' );
								}
							},
							complete: function(){
								self.enableButton();
							},
						});
					});

				},

				/*
				-------------------------------------------------------------------------------
				Form message
				-------------------------------------------------------------------------------
				*/
				fieldError: function( _field_id, _error_message ){
					var _icon = '<span class="dashicons dashicons-warning"></span>',
					_class = 'zwe-form-field-error';

					$('[data-form-field-id="'+ _field_id +'"]').append( '<div class="'+ _class +'">'+ _icon +' '+ _error_message +'</div>' );
				},

				formMessage: function( _response_message, _message_type ){
					var _msg = ( _response_message && '' !== _response_message ) ? _response_message : false;
					plugin.find('#submit').after('<div class="zwe-form-message '+ _message_type +'">'+ _msg +'</div>');
				},

				msgError: function( _response_message ){
					self.formMessage( _response_message, 'is-error');
				},

				msgSuccess: function( _response_message ){
					self.formMessage( _response_message, 'is-success');
				},

				clearErrors: function(){
					plugin.find('form .zwe-form-message').remove();
					plugin.find('form .zwe-form-field-error').remove();
				},

				/*
				-------------------------------------------------------------------------------
				Form button
				-------------------------------------------------------------------------------
				*/
				disableButton: function(){
					plugin.find( '#submit' ).attr('disabled', true);
				},

				enableButton: function(){
					plugin.find( '#submit' ).attr('disabled', false);
				},

				/*
				-------------------------------------------------------------------------------
				Form Rows
				-------------------------------------------------------------------------------
				*/
				getFormRows: function(){
					return plugin.find( 'form .form-row' );
				},
				getChildrenFormRows: function(){
					return plugin.find( 'form .form-row[data-form-parent-field-id]' );
				},

				hideFormRow: function(){
					$.each( self.getChildrenFormRows(), function(){
						var _t       = $(this),
						parent_name  = _t.data('form-parent-field-id'),
						needed_value = _t.data('form-parent-field-value'),
						parent_value = plugin.find( '[name="'+ parent_name +'"]' ).val();

						if( needed_value !== parent_value ){
							_t.hide();
						}

					});
				},

				collapseFormRow: function(){
					plugin.find( 'form [data-can-be-parent]' ).on( 'keydown keyup change', function(){
						var _t = $(this),
						_name = _t.attr('name'),
						_val = _t.val(),
						child_fields = plugin.find( 'form .form-row[data-form-parent-field-id="'+ _name +'"]');

						$.each( child_fields, function(){
							var child   = $(this),
							child_value = child.data('form-parent-field-value');

							self.childCollapse( child, child_value, _val );
						});
					});
				},

				childCollapse: function( child, child_data_value, required_value ){
					if( child_data_value == required_value ){
						child.show();
					}
					else{
						child.hide();
					}
				},

				/*
				-------------------------------------------------------------------------------
				Construct plugin
				-------------------------------------------------------------------------------
				*/
				__construct: function(){
					self = this;

					self.ajaxProcess();
					self.hideFormRow();
					self.collapseFormRow();

					return this;
				}

			};

			/*
			-------------------------------------------------------------------------------
			Rock it!
			-------------------------------------------------------------------------------
			*/
			Zwe_OptionsPage_Base.__construct();

		}

		//Plugin go!
		init();
		return this;

	};


$(document).ready(function(){
	$('#zwe-options-page-container').Zwe_OptionsPage();
});

})(jQuery);