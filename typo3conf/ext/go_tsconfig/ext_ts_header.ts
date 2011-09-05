#
# declare rendered Header
#
goheader1 = IMAGE
goheader1 {
	stdWrap {
		wrap = <h1>|</h1>
		typolink.parameter.field = header_link
	}
	altText.data = field:header
	file = GIFBUILDER
	file {
		XY = [10.w],[10.h]+5
		format = png
		backColor = #FFFFFF
		transparentColor = #FFFFFF
		
		10 = TEXT
		10 {
			text.data = field:header
			text.data.current = 1
			breakWidth = 500
			fontFile = fileadmin/templates/fonts/MyriadPro-Regular.otf
			fontColor = #000000
			fontSize = 24
			offset = 0,20
			niceText = 1
			spacing = 0
		}
	}
}

goheader2 < goheader1
goheader2.stdWrap.wrap = <h2 class="h2">|</h2>
goheader2.file.10.fontSize = 20
goheader2.file.10.offset = 0,17

goheader3 < goheader1
goheader3.stdWrap.wrap = <h3 class="h3">|</h3>
goheader3.file.10.fontSize = 18
goheader3.file.10.offset = 0,15

goheader4 < goheader1
goheader4.stdWrap.wrap = <h4 class="h4">|</h4>
goheader4.file.10.fontSize = 14
goheader4.file.10.offset = 0,12

goheader5 < goheader1
goheader5.stdWrap.wrap = <h5 class="h5">|</h5>
goheader5.file.10.fontSize = 12
goheader5.file.10.offset = 0,10

lib.stdheader.10.setCurrent.br = 1
lib.stdheader.10.1 < goheader1
lib.stdheader.10.2 < goheader2
lib.stdheader.10.3 < goheader3
lib.stdheader.10.4 < goheader4
lib.stdheader.10.5 < goheader5

#
# declare rendered SubHeader
#
gosubheader1 =< goheader3
gosubheader1.stdWrap.typolink >
gosubheader1.altText.data = field:subheader
gosubheader1.file.10.text.data = field:subheader

gosubheader2 =< goheader4
gosubheader2.stdWrap.typolink >
gosubheader2.altText.data = field:subheader
gosubheader2.file.10.text.data = field:subheader

gosubheader3 =< goheader5
gosubheader3.stdWrap.typolink >
gosubheader3.altText.data = field:subheader
gosubheader3.file.10.text.data = field:subheader

lib.stdsubheader = CASE
lib.stdsubheader.1 < gosubheader1
lib.stdsubheader.2 < gosubheader2
lib.stdsubheader.3 < gosubheader3
lib.stdsubheader.setCurrent.field = subheader
lib.stdsubheader.setCurrent.br = 1
lib.stdsubheader.setCurrent.htmlSpecialChars = 1
lib.stdsubheader.key.field = layout
lib.stdsubheader.key.ifEmpty = 1
lib.stdsubheader.key.ifEmpty.override.value = 1
lib.stdsubheader.if.isTrue.field = subheader
lib.stdsubheader.stdWrap.dataWrap = <div class="csc-subheader csc-subheader-n{cObj:parentRecordNumber}">|</div>

#
# Subheader for header
#
tt_content.header.20 >
tt_content.header.20 =< lib.stdsubheader
#
# Subheader for textpic
#
#tt_content.textpic.15 =< lib.stdsubheader

