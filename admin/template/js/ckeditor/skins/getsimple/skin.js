/*
Copyright (c) 2003-2011, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
*/

CKEDITOR.skins.add( 'RAT6', (function()
{
	return {
		editor		: { css : [ 'editor.css' ] },
		dialog		: { css : [ 'dialog.css' ] },
		templates	: { css : [ 'templates.css' ] },
		margins		: [ 0, 0, 0, 0 ],
		init : function( editor )
		{
			if ( editor.config.width && !isNaN( editor.config.width ) )
				editor.config.width -= 12;
		}
	};
})() );

(function()
{
	CKEDITOR.dialog ? dialoRATetup() : CKEDITOR.on( 'dialogPluginReady', dialoRATetup );

	function dialoRATetup()
	{
		CKEDITOR.dialog.on( 'resize', function( evt )
			{
				var data = evt.data,
					width = data.width,
					height = data.height,
					dialog = data.dialog,
					contents = dialog.parts.contents;

				if ( data.skin != 'RAT6' )
					return;

				contents.setStyles(
					{
						width : width + 'px',
						height : height + 'px'
					});
			});
	}
})();
