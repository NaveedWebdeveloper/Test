function myCustomCleanup(type, value)
{
	switch(type) {
		case 'get_from_editor':
			while(value.substring(value.length-6, value.length) == '<br />')
				value = value.substring(0, value.length-6);
			break;
		}
	return value;
}

tinyMCE.init({
	height : '101', // average value (Firefox 99; IE 101; Opera9 103)

	mode : 'textareas',
	cleanup : true,
	entity_encoding : 'none',
	force_p_newlines : false,
	cleanup_callback : 'myCustomCleanup',
	convert_newlines_to_brs : true,

	theme : 'advanced',
	theme_advanced_buttons1 : 'bold,italic,underline,strikethrough,separator,forecolor,backcolor,charmap,separator,bullist,numlist,separator,code,fullscreen',
	theme_advanced_buttons2 : '',
	theme_advanced_buttons3 : '',
	theme_advanced_toolbar_location : 'top',
	theme_advanced_toolbar_align : 'left',
	theme_advanced_path : false,
	theme_advanced_statusbar_location : 'bottom',
	theme_advanced_resizing : true,
	theme_advanced_resize_horizontal : false,
	plugins : 'fullscreen'
});
