/*
*	@author 		Mansoor Ahmad
*	@date 			13.11.2009
*	@description	Function for Dropping and requesting Database
*/

	var newPostion = 'NO';
	var ceIdForPos = 'NO';
	var dropareaPosClass = 'go_backend_layout_droppables_position';

	/*
	*	var ID			Need the ID of the element, which is dragged
	*	description 	Copy & Paste the Element from x Pos to y Pos
	*/
	function startDroppable(ID){							
		$('go_backend_layout_droppable_'+ID).cleared = false;
		//alert(newPostion);
		Droppables.add('go_backend_layout_droppable_'+ID, 
		{ 
			accept: 'go_backend_layout_draggable',
			hoverclass: 'hover',
			greedy: true,
			
			onDrop: function(draggable, droparea, event)
					{ 			
						
						oldParent = draggable.parentNode;
						ceId = draggable.id;
						ceIdForPos = '';
												
						oldParentsChilds = document.getElementById(oldParent.id).childNodes;
						countNodes = oldParentsChilds.length;
						for(var i=0;i < countNodes; i++)
						{
							//alert(i + \'--\' + draggable.id + \'--\' + oldParentsChilds[i].id);
										
							if(oldParentsChilds[i].id == draggable.id)
							{
								//alert(draggable.id);
								//alert( i + '--' + (i-6) + '--');
								//alert(oldParentsChilds[(i-6)].getAttribute("onmouseup"));
								oldParentsChildPosHref = oldParentsChilds[(i-6)].firstChild.getAttribute("href");
								//alert(oldParentsChildPosHref);
								oldParentsChildPos = parseInt(oldParentsChildPosHref.substr(oldParentsChildPosHref.lastIndexOf("vDEF%3A")+7,oldParentsChildPosHref.length)) + 1;
								//alert( i + '--' + (i-6) + '--' + oldParentsChildPos);
							}
						}
						
						
						newParentsChilds 	=	document.getElementById(droparea.id).childNodes;
						insertPosition 		= 	4*(newPostion+1);
						dropareaRow = droparea.id.substr(28,droparea.id.length).split("-");
						draggableRow = draggable.id.split("-");

						if((newPostion != 'NO') && !(((newPostion+1) == oldParentsChildPos) && (droparea.id == oldParent.id)) && !((newPostion == oldParentsChildPos) && (droparea.id == oldParent.id)) && (draggableRow[1] != dropareaRow[2])) {	

							ceLabel 	= 	document.getElementById(draggable.id+'_label').innerHTML;
							fieldLabel 	= 	document.getElementById(droparea.id+'_label').innerHTML;
							//check = confirm("Das Element: " + ceLabel + "\naus der Position: "+oldParentsChildPos+"\n\nwird verschoben\n\nnach: "+fieldLabel+"\nin die Position: "+(newPostion+1)+".\n\nWollen Sie diesen Vorgang fortsetzen?");
							check = confirm("Möchten Sie dieses Element verschieben?");
							
							if(check !== false)
							{
								//* $('go_backend_layout_droppable_'+ID).highlight();
								if (!droparea.cleared)
								{
									// droparea.innerHTML = \'\';
									droparea.cleared = true;
								}
								
								//# Removing insertfield from old parent field						
								insertField = draggable.nextSibling;
								for(var j=0;j<1;j++)
								{
									insertField = insertField.nextSibling;
									//alert(insertField.nodeName);
								}
								draggable.parentNode.removeChild(insertField);
								
								
								//# Removing this element from old parent field
								draggable.parentNode.removeChild(draggable);
								
							
								//# Inserting this element plus insertField in new parent field
								/*
								if((newPostion > oldParentsChildPos) && (droparea.id == oldParent.id))
								{
									droparea.insertBefore(insertField,newParentsChilds[insertPosition+8]);
									droparea.insertBefore(draggable,newParentsChilds[insertPosition+8]);
								}
								else if(newPostion > 0)
								{
									droparea.insertBefore(draggable,newParentsChilds[insertPosition]);
									droparea.insertBefore(insertField,newParentsChilds[insertPosition]);
								}
								else
								{
									droparea.insertBefore(insertField,newParentsChilds[insertPosition]);
									droparea.insertBefore(draggable,newParentsChilds[insertPosition]);
								}
								*/
								//* droparea.appendChild(draggable);
								
								oldParentRow = oldParent.id.substr(28,oldParent.id.length).split("-");
						
								if(oldParentRow[1] == oldParentRow[2])
								{
									var oldTableTyp = 'pages';
								}
								else
								{
									var oldTableTyp = 'tt_content';
								}
											
								if(dropareaRow[1] == dropareaRow[2])
								{
									var newTableTyp = 'pages';
								}
								else
								{
									var newTableTyp = 'tt_content';
								}
							
							
								var oldTableID = oldParentRow[2];
								var newTableID = dropareaRow[2];

								var url = 'index.php?id=' + dropareaRow[1]  + '&CB[removeAll]=normal&pasteRecord=cut&source='+oldTableTyp+':'+oldTableID+':sDEF:lDEF:'+oldParentRow[0]+':vDEF:'+oldParentsChildPos+'&destination='+newTableTyp+':'+newTableID+':sDEF:lDEF:'+dropareaRow[0]+':vDEF:'+newPostion;
									
								//alert(url);
								window.location.href = url;
							}
						}
					}
		});
	}
	
	
	
	
	
	/*
	*	@ID					Need the ID auf the element
	*	@mode				Control it by the Javascript Events, to change the highlighting
	*	@decription			Set and control the highlighting of the position "elements"
	*/
	function setDropareaPosClass(ID, mode)
	{
		if(ceIdForPos.length > 2)
		{
			nextSib = document.getElementById(ceIdForPos).nextSibling;
			for(var j=0;j<1;j++)
			{
				nextSib = nextSib.nextSibling;
			}
			//alert(nextSib.getAttribute('ID'));

			previousSib = document.getElementById(ceIdForPos).previousSibling;
			for(var k=0;k<5;k++)
			{
				previousSib = previousSib.previousSibling;
			}
			//alert(previousSib.getAttribute('ID'));
			
			if((nextSib.getAttribute('ID') == ID) || (previousSib.getAttribute('ID') == ID))
			{
				dropareaPosClass = 'go_backend_layout_droppables_position';
			}
			else
			{
				dropareaPosClass = 'go_backend_layout_droppables_position_act';
			}
		}		
	}