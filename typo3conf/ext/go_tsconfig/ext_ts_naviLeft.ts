#
# Left Navigation
#
naviLeft = HMENU
naviLeft {
	special = directory
	special.value = 11
	1 = TMENU
	1 {
		wrap = <ul id="nav_left">|</ul>
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
		wrap = <ul class="sub_left">|</ul>
		ACT.wrapItemAndSub = <li class="sub_act">|</li>
		IFSUB < ACT
		IFSUB.wrapItemAndSub = <li class="menuparent">|</li>
		ACTIFSUB < .IFSUB
	}
	3 < .2
	4 < .2
}
