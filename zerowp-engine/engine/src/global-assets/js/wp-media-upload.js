;(function( $ ) {

	"use strict";

	$.fn.Zwe_Media_Upload = function( options ) {

		if (this.length > 1){
			this.each(function() {
				$(this).Zwe_Media_Upload(options);
			});
			return this;
		}

		// Defaults
		var settings = $.extend({
			addSelector: '.add-media',
			mediaContainer: '.media-container',
			sortableClass: '.media-sortable',
			deleteSelector: '.media-delete',
			itemSelector: '.media-item',
			frameMethod: 'select', //select or post
			templateContainer: '.item-template',
		}, options );

		// Cache current instance
		var plugin = this;

		//Plugin go!
		var init = function() {
			plugin.build();
		}

		// Build structure
		this.build = function() {
			var self = false;
			var frame;

			var Zwe_MediaObject = {

				isMultiple: function(){
					return ( plugin.data('multiple') !== 'no' ) ? plugin.data('multiple') : false;
				},

				openFrame: function(){
					plugin.on( 'click', settings.addSelector, function( event ){
						event.preventDefault();

						// If the media frame already exists, reopen it.
						if ( frame ) {
							frame.open();
							return;
						}

						// Create a new media frame
						frame = self.createMediaFrame();

						// When an image is selected in the media frame...
						frame.on( 'select', function() {

							// Get media attachment details from the frame state
							var attachments = frame.state().get('selection').toJSON();

							self.appendMedia( attachments );
							self.setButtonText();

							console.log( attachments );

						});

						// Finally, open the modal on click
						frame.open();

					});
				},

				// Create a media frame
				createMediaFrame: function(){

					var _defaults = {
						frame: settings.frameMethod,
						title: plugin.data('frame-title'),
						button: {
							text: plugin.data('frame-button-label'),
						},
						multiple: true  // Set to true to allow multiple files to be selected
					};

					if( plugin.data( 'mm' ) ){
						_defaults = $.extend( _defaults, {
							library: {
								type: plugin.data( 'mm' )
							}
						} );
					}

					if( ! self.isMultiple() ){
						_defaults = $.extend( _defaults, {
							multiple: false
						} );
					}

					return wp.media( _defaults );
				},

				appendMedia: function( attachments ){
					var _container = plugin.find( settings.mediaContainer ),
					_template      = self.getMediaTemplate();

					$.each( attachments, function( index, _file ){
						if( ! _container.find('[data-item-id="'+ _file.id +'"]').length ){
							if( false === self.isMultiple() ){
								_container.html( self.parseTemplate( _template, _file ) );
							}
							else{
								_container.append( self.parseTemplate( _template, _file ) );
							}
							plugin.trigger( 'media:add' );
						}
					});
				},

				getMediaTemplate: function(){
					return plugin.find( settings.templateContainer ).html();
				},

				parseTemplate: function( _template, _file ){
					var base_name = plugin.data('upload-base-name');
					_template = self.bulkReplace( _template,
						[
							'__input_base_name__',
							'__media_id__',
							'__media_url__',
							'__media_title__',
							'__media_size__',
							'__media_icon__',
							'disabled="disabled"'
						],
						[
							base_name,
							_file.id,
							_file.url,
							_file.filename,
							_file.filesizeHumanReadable,
							self.basename( _file.icon, '.png' ),
							''
						]
					);

					return _template;
				},

				deleteItem: function(){
					plugin.on( 'click', settings.itemSelector +' '+ settings.deleteSelector, function(){
						var _item = $(this).parents( settings.itemSelector );
						_item.slideUp(200, function(){
							_item.remove();
							plugin.trigger('media:remove');
						});
					});
				},

				setButtonText: function(){
					var _add_btn  = plugin.find( settings.addSelector ),
					_change       = _add_btn.data('change'),
					_more         = _add_btn.data('more'),
					_add_singular = _add_btn.data('add-singular'),
					_add_plural   = _add_btn.data('add-plural'),
					_items_added  = plugin.find( settings.mediaContainer ).find( settings.itemSelector );


					if( _items_added.length > 0 ){
						if( false === self.isMultiple() ){
							_add_btn.children('.text').text( _change );
						}
						else{
							_add_btn.children('.text').text( _more );
						}
					}
					else{
						if( false === self.isMultiple() ){
							_add_btn.children('.text').text( _add_singular );
						}
						else{
							_add_btn.children('.text').text( _add_plural );
						}
					}
				},

				makeSortable: function(){
					plugin.find( settings.mediaContainer + settings.sortableClass ).sortable({
						items: settings.itemSelector,
						placeholder: 'sortable-placeholder',
						cancel: settings.deleteSelector,
					});
				},

				bulkReplace: function( str, findArray, replaceArray ){
					var i, regex = [], map = {};

					for( i=0; i<findArray.length; i++ ){
						regex.push( findArray[i].replace('[-[\]{}()*+?.\\^$|#,]','\\$0') );
						map[findArray[i]] = replaceArray[i];
					}

					regex = regex.join('|');
					str   = str.replace( new RegExp( regex, 'g' ), function(matched){
						return map[matched];
					});

					return str;
				},

				basename: function(path, suffix) {
					// source: http://locutus.io/php/basename/

					var b = path;
					var lastChar = b.charAt(b.length - 1);

					if (lastChar === '/' || lastChar === '\\') {
						b = b.slice(0, -1);
					}

					b = b.replace(/^.*[\/\\]/g, '');

					if (typeof suffix === 'string' && b.substr(b.length - suffix.length) === suffix) {
						b = b.substr(0, b.length - suffix.length);
					}

					return b;
				},

				/*
				-------------------------------------------------------------------------------
				Construct plugin
				-------------------------------------------------------------------------------
				*/
				__construct: function(){
					self = this;

					self.openFrame();
					self.deleteItem();
					self.makeSortable();
					self.setButtonText();

					plugin.on( 'media:add, media:remove', function(){
						self.setButtonText();
					} );

					return this;
				}

			};

			/*
			-------------------------------------------------------------------------------
			Rock it!
			-------------------------------------------------------------------------------
			*/
			Zwe_MediaObject.__construct();

		}

		//Plugin go!
		init();
		return this;

	};

})(jQuery);