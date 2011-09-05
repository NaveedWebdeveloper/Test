#
# Top Navigation mit Layer
#
naviTop = HMENU
naviTop {
	special = directory
	special.value = 5
	1 = TMENU
	1 {
		expAll = 1
		wrap = <ul id="nav">|</ul>
		noBlur = 1
		NO = 1
		NO.stdWrap.htmlSpecialChars = 1
		NO.wrapItemAndSub = <li>|</li>
		ACT = 1
		ACT.stdWrap.htmlSpecialChars = 1
		ACT.wrapItemAndSub = <li class="act">|</li>
	}
	2 < .1
	2 {
		wrap = <ul>|</ul>
		IFSUB < ACT
		IFSUB.wrapItemAndSub = <li class="menuparent">|</li>
		ACTIFSUB < .IFSUB
	}
	3 < .2
	4 < .2
}
