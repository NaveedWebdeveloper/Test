lib.right = HMENU
lib.right{
	special = directory
	special.value = 5
	
	1 = TMENU
	1{
		wrap = <ul>|</ul>
		NO.wrapItemAndSub = <li>|</li>
		
		ACT = 1
		ACT.wrapItemAndSub = <li style="color:red;">|</li>
	}
	2 = TMENU
	2{
		wrap = <ul class="level2">|</ul>
		NO.wrapItemAndSub = <li class="level2">|</li>
		
		ACT = 1
		ACT.wrapItemAndSub = <li class="level2" style="color:red;">|</li>
	}
}