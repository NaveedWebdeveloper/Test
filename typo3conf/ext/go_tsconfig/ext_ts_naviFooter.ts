#
# Footer Navigation
#
naviFooter = HMENU
naviFooter {
	special = directory
	special.value = 5
	1 = TMENU
	1 {
		expAll = 1
		noBlur = 1
		wrap = <ul>|</ul>
		NO = 1
		NO.stdWrap.htmlSpecialChars = 1
		NO.stdWrap.ATagBeforeWrap = 1
		NO.wrapItemAndSub = <li>|</li>
		ACT = 0
	}
	2 < .1
}