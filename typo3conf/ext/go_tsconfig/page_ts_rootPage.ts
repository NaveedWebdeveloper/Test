TCEMAIN.permissions {
    # User can do anything (default):
  user = 31

    # Group can do anything
    # (normally "delete" is disabled)
  group = 31

    # Default groupId for new sites
  groupid = 1

    # Everybody can at least see the page
    # (normally everybody can do nothing)
  everybody = show
}


#Alex @ Gosign
RTE.default.proc.allowedClasses = link1,color
RTE.default.init.content_css = fileadmin/templates/css/mce.css
RTE.default.useFEediting = 1
RTE.default.init {
	theme_advanced_buttons1=bold,italic,underline,|,justifyleft,justifycenter,justifyright,justifyfull,|,bullist,|,indent,outdent,|,sub,sup,|,link,unlink,|,copy,cut,pastetext,|,code,|,undo,redo
	theme_advanced_buttons2=
	theme_advanced_buttons3=
	theme_advanced_buttons4=
	plugins=contextmenu,inlinepopups,safari,tabfocus,searchreplace,advlink,typo3filemanager,advimage,table,paste
	
	theme_advanced_statusbar_location = bottom
	width = 600
	height = 380
	fix_table_elements = true
	# you could override the following option if you don't want to insert links.
	file_browser_callback = typo3filemanager
	
	# Additional config options for the "styleselect" button.
	
	# This option should contain a semicolon separated list of class titles and class names separated by =. The titles will be presented to the user in the styles dropdown list and the class names will be inserted. If this option is not defined, TinyMCE imports the classes from the content_css.
	#theme_advanced_styles = Link=link1;
	
	
	# Additional config options for the "forecolor" button.
	
	# This option enables you to specify the default foreground color.
	theme_advanced_default_foreground_color  = #FF00FF
	
	# This option controls the colors shown in the palette of colors displayed by the text color button. The default is a palette of 40 colors. It should contain a comma separated list of color values to be presented.
	theme_advanced_text_colors = FF00FF,FFFF00,000000
	
	# This option enables you to disable the "more colors" link for the text and background color menus.
	theme_advanced_more_colors = false
	
	
	# Additional config options for the "backcolor" button.
	
	# This option enables you to specify the default background color.
	theme_advanced_default_background_color  = #FF00FF
	
	# This option controls the colors shown in the palette of background colors displayed by the background color button. The default is a palette of 40 colors. It should contain a comma separated list of color values to be presented.
	theme_advanced_background_colors = FF00FF,FFFF00,000000
	
	# This option enables you to disable the "more colors" link for the text and background color menus.
	theme_advanced_more_colors = false
	
	
	# Additional config options for the "link" button.
	
	# This option should contain a semicolon separated list of class titles and class names separated by =. The titles are the ones that get presented to the user in the styles drop down list and and the class names is the classes that gets inserted.
	advlink_styles =
	# This option should contain a semicolon separated list of link target titles and target names separated by =. The titles are the ones that get presented to the user in the link target drop down list and and the target names is the target that gets inserted as a target attribute.
	
	#theme_advanced_link_targets = Some frame=someframe;Some other frame=otherframe
	
	
	# Additional config options for the "image" button.
	
	# This option enables you to control if the image dimensions should be updated with new data if the image src field is changed. This option is enabled by default.
	advimage_update_dimensions_onchange = true
	
	
	# Additional config options for the "tabfocus" plugin.
	
	# This option enables you to specify an element ID to focus, when the user pressed the tab key inside the editor. You can also use the special ":prev" and ":next" values. It will then places the focus on either the previous or next input element placed before/after the TinyMCE instance in the DOM.
	# Move focus to next element in DOM
	#tabfocus_elements = :prev,:next
}

###	Added by alex,
### keine (Kopie 1) vor die kopierten Elemente schreiben
TCEMAIN.table.tt_content {
	disablePrependAtCopy = 1
	#disableHideAtCopy = 1
}

## Layout Typen umbenennen
#TCEFORM.tt_content.header_layout.altLabels.1 = LLL:EXT:go_tsconfig/locallang.xml:layout.goheader1
## Layout Typen ausblenden
#TCEFORM.tt_content.header_layout.removeItems = 0,1,2,3,4,5,100
#100 für Hidden/Verborgen