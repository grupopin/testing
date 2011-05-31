<?php

class Zend_View_Helper_GridInstance {

  public $view;
  private $subGridFld, $langObj;
  public function setView(Zend_View_Interface $view) {
    $this->view = $view;
  }

  public function __construct() {
    $this->subGridFld = Zend_Registry::get ( 'subGridFld' );
    $this->langObj = Zend_Registry::get ( 'nsHw' )->langObj;
  }

  private function itemsCombo( $id) {
    $phName = "list_items_{$id}";
    $this->view->placeholder ( $phName )->captureStart ();
    ?>
<div align="left" style="padding-bottom: 6px;"><select
	name="item_type_<?=$id?>" id="item_type_<?=$id?>" class='item_type_select'>
	<option value='skin'><?=$this->view->l ( 'Skin' )?></option>
	<option value='mt'><?=$this->view->l ( 'Maintitle' )?></option>
	<option value='hl'><?=$this->view->l ( 'Highlight' )?></option>
	<optgroup label="Works">
	<?=$this->view->l ( 'Works' )?>
		<option value='apa'><?=$this->view->l ( 'Apa' )?></option>
		<option value='arch'><?=$this->view->l ( 'Architecture' )?></option>
		<option value='hwg'><?=$this->view->l ( 'Graphics' )?></option>
		<option value='jw'><?=$this->view->l ( 'JW' )?></option>
		<option value='paint'><?=$this->view->l ( 'Paintings' )?></option>
		<option value='tap'><?=$this->view->l ( 'Tapestry' )?></option>
	</optgroup>
	<optgroup label="Documents">
	<?=$this->view->l ( 'Documents' )?>
		<option value='audio'><?=$this->view->l ( 'Audio' )?></option>
		<option value='gallery'><?=$this->view->l ( 'Gallery' )?></option>
		<option value='video'><?=$this->view->l ( 'Video' )?></option>
		<option value='text'><?=$this->view->l ( 'Text' )?></option>
		<option value='image'><?=$this->view->l ( 'Image' )?></option>
		<option value='biography'><?=$this->view->l ( 'Biography' )?></option>
		<option value='one_exhibition'><?=$this->view->l ( 'One-man Exhibitions ' )?></option>
		<option value='group_exhibition'><?=$this->view->l ( 'Group Exhibitions' )?></option>
		<option value='monograph'><?=$this->view->l ( 'Monographs' )?></option>
		<option value='catalog_exhibition'><?=$this->view->l ( 'Exhibition Catalogues' )?></option>
		<option value='article'><?=$this->view->l ( 'Press clippings' )?></option>
		<option value='document'><?=$this->view->l ( 'Documents' )?></option>
		<option value='picture'><?=$this->view->l ( 'Pictures' )?></option>
	</optgroup>
	<optgroup label="Books">
	<?=$this->view->l('Books')?>
	 <option value='book'><?=$this->view->l('Book')?></option>

	</optgroup>
	<optgroup label="Shop"><?=$this->view->l('Shop')?>
	<?php
	$dataConf=Zend_Registry::get('dataConfig');
	$shopCats=$dataConf['shopCats'];
	foreach($shopCats as $catName=>$catProp){?>
	  <option value='<?=$catProp['term_name']?>'><?=$catProp['title']?></option>

	<?php }?>

	</optgroup>
</select> &nbsp; <select name="field_name_<?=$id?>"
	id="field_name_<?=$id?>">
	<option value='id' selected="selected"><?=$this->view->l('Id')?></option>
	<option value='title'><?=$this->view->l('Title')?></option>
	<option value='workNumber'><?=$this->view->l('Work Number')?></option>
</select> &nbsp;&nbsp; Filter: <input type="text" id="filter_<?=$id?>"
	style="width: 120px;" /> &nbsp;&nbsp;&nbsp;<input type="submit"
	name="submit_filter_<?=$id?>" id="submit_filter_<?=$id?>"
	value="<?=$this->view->l('search')?>"></div>
	<?php
	$this->view->placeholder ( $phName )->captureEnd ();
	return $this->view->placeholder ( $phName );
  }

  /**
   *
   * @param $id
   * @param $itemType
   * @param $child - id of child grid. When row is selected child grid is refreshed (Like master / detail)
   * @param $parent - id of a parent grid.
   * @param $syncWith - defines id of grid which need to be refreshed/reloaded when current grid is changed
   * @param $allItemsGrid - true/false - to show all items(width linked marked with yellow) or just linked
   * @param $subGrid
   * @return unknown_type
   */
  public function gridInit(Hw_GridConfig $gridConfig) {
    //$id, $itemType="skin", $child=null, $parent=null, $syncWith=null, $allItemsGrid=null, $selectedItemsGrid=null, $subGrid=false
    //print_r($subGridFld[$gridConfig->itemType]);
    ?>
<script type="text/javascript"><!--
    <?php
		$this->view->jQuery ()->onLoadCaptureStart ();
		?>
    var curSelectedItem_<?=$gridConfig->id?>=0;

    var actionsArr_<?=$gridConfig->id?>={
           2:{'action':'itemview','title' : '<?=$this->view->l ( 'View Item' )?>',modal:true}
      };

    jQuery("#list_<?=$gridConfig->id?>").jqGrid({

  //hide grid by default
   // hiddengrid:true,
    // the url parameter tells from where to get the data from server
    // adding ?nd='+new Date().getTime() prevent IE caching
    url:"/link/jq-list2/type/<?=$gridConfig->itemType?>?&nd="+new Date().getTime(),
    // datatype parameter defines the format of data returned from the server
    // in this case we use a XML data
    datatype: "json",
    multiselect: false,
    // colNames parameter is a array in which we describe the names
    // in the columns. This is the text that apper in the head of the grid.
    colNames:['<?=$this->view->l ( 'id' )?>','<?=$this->view->l ( 'Name/Title' )?>'],
    // colModel array describes the model of the column.
    // name is the name of the column,
    // index is the name passed to the server to sort data
    // note that we can pass here nubers too.
    // width is the width of the column
    // align is the align of the column (default is left)
    // sortable defines if this column can be sorted (default true)
    colModel:[
        {name:'id',index:'id', width:25,sortable:true},
        {name:'name',index:'name', sortable:true, align: '<?=$this->langObj->align_left?>', width:250, editable:false}
    ],
    // pager parameter define that we want to use a pager bar
    // in this case this must be a valid html element.
    // note that the pager can have a position where you want
    pager: jQuery('#pager_<?=$gridConfig->id?>'),
    // rowNum parameter describes how many records we want to
    // view in the grid. We use this in example.php to return
    // the needed data.
    rowNum:20,
    // rowList parameter construct a select box element in the pager
    //in wich we can change the number of the visible rows
    rowList:[10,20,30,40,50,100],
    // path to mage location needed for the grid
    imgpath: '/js/jqgrid/themes/sand/images',
    // sortname sets the initial sorting column. Can be a name or number.
    // this parameter is added to the url
    sortname: 'id',
    //viewrecords defines the view the total records from the query in the pager
    //bar. The related tag is: records in xml or json definitions.
    viewrecords: true,
    //sets the sorting order. Default is asc. This parameter is added to the url
    sortorder: "asc",
    caption: "<?=$this->view->l ( "List of $gridConfig->itemType" )?>",
    //height:'100%',
    height:<?=$gridConfig->gridHeight?>,
    width:<?=$gridConfig->gridWidth?>,
    shrinkToFit:true,
    <?
		if ($gridConfig->subGrid) {
			?>

		loadComplete: function(){
				jQuery('#list_<?=$gridConfig->id?> tr').each(
		        function(i){
		            var newId='id-'+$(this).attr('id').replace(/\//g,'_').replace(/\./g,'-').replace(/ /g,'spc');
		            //newId2='id-'+newId;
								$(this).attr('id',newId);
					      //alert(newId);
		            //$(this).
		        }

		     )},
    subGrid : true,
    <?
			if ($this->subGridFld [$gridConfig->itemType] ['field']) {
				?>
      subGridUrl: '/link/jq-item2/type/<?=$gridConfig->itemType?>/?fld=<?=$this->subGridFld [$gridConfig->itemType] ['field']?>&cell_type=<?=$this->subGridFld [$gridConfig->itemType] ['type']?>',
      subGridModel: <?=$this->subGridFld [$gridConfig->itemType] ['model']?>,
    		  /*[{ name  : ['Content'], width : [280] }],*/
    <?
			}
		}
		?>

    onCellSelect: function(rowid, iCol, cellcontent){
     //alert(rowid+iCol);
     // val1=jQuery('#list_hist').getGridParam['selrow'];
     var linkItem1=jQuery("#item_type_<?=$gridConfig->id?>").val();
     //alert("#item_type_<?=$gridConfig->id?>:"+linkItem1);
     if (linkItem1=='image') return false;
     if (rowid){
    	 var rowData = jQuery("#list_<?=$gridConfig->id?>").getRowData(rowid);
       jQuery("input#sel_item_<?=$gridConfig->id?>").val(rowData.id);
       curSelectedItem_<?=$gridConfig->id?>=rowData.id;
       //alert('curSelectedItem_<?=$gridConfig->id?>='+curSelectedItem_<?=$gridConfig->id?>);

      <?
		$childsArr = array ();
		if ($gridConfig->child) {
			if (is_int ( $gridConfig->child )) {
				$childsArr [0] = $gridConfig->child;
			} elseif (is_string ( $gridConfig->child )) {
				$childsArr = explode ( ",", $gridConfig->child );
			} elseif (is_array ( $gridConfig->child )) {
				$childsArr = $gridConfig->child;
			}
			foreach ( $childsArr as $c => $child ) {
				?>
          //reload connected
          jQuery('#list_<?=$child?>').trigger('reloadGrid');
        <?
			}
		}
		?>
     }
     if (actionsArr_<?=$gridConfig->id?>[iCol] && cellcontent.length>1){
       var actionsArr=actionsArr_<?=$gridConfig->id?>;
       var actVal=actionsArr_<?=$gridConfig->id?>[iCol]['action'];
       var titleVal=actionsArr_<?=$gridConfig->id?>[iCol]['title'];
       if (actVal){
         var href="/link/"+actVal+"/?width=608&height=80%&jqmRefresh=0&id="+rowid;
       if (actionsArr[iCol]['modal']){
         var linkId="#grid_link";
       }else{
         var linkId="#regular_link";

       }
       $(linkId).html("").attr('href',href);
       $(linkId).html("").attr('title',titleVal);
       //alert($(linkId).html("").attr('href'));
       $(linkId).trigger('click');
     }
     }
    },

    //onSelectRow: function(rowId){
   // 	  alert($(this).text());
    //},

//   subGridRowExpanded: function(id,rowid){
//        //id in format list1_23
//        //rowid is just int - 23
////       alert(id+'-'+rowid);
//       var rowData = jQuery("#list_<?=$gridConfig->id?>").getRowData(rowid);
////       alert(rowData.id);
//
//        var filterValue=jQuery("#filter_<?=$gridConfig->id?>").val();
//        var typeValue=jQuery("#item_type_<?=$gridConfig->id?>").val();
//        var fldName=jQuery("#field_name_<?=$gridConfig->id?>").val();
//
//            jQuery('#list_<?=$gridConfig->id?>').setGridParam({
//                 subGridUrl: '/link/jq-item2/type/'+typeValue+'/?id='+rowData.id+'&fld='+subGridProps[typeValue]['field']+'&cell_type='+subGridProps[typeValue]['type']+'&nd='+new Date().getTime()
//                , subGridModel: subGridProps[typeValue]['model']
//                //[{ name  : ['Content'], width : [280] }],
//                //, subGridUrl: '/link/jq-item/type/'+typeValue+'/?nd='+new Date().getTime()
//
//                }
//            );
//            alert(jQuery('#list_<?php print $gridConfig->id?>').getGridParam('subGridUrl'));
//            return true;
//		},

<?php
		if ($gridConfig->allItemsGrid){
			?>
    ondblClickRow: function(rowid,stats){
			  var rowData = jQuery("#list_<?=$gridConfig->id?>").getRowData(rowid);


        //alert(rowid+','+stats);

        //alert('curSelectedItem_<?=$gridConfig->parent?>:'+curSelectedItem_<?=$gridConfig->parent?>);
        var curId=curSelectedItem_<?=$gridConfig->parent?>;
        if (parseInt(curId)>0){
        var typeValue1=jQuery("#item_type_<?=$gridConfig->id?>").val();
        var linkItem1=jQuery("#item_type_<?=$gridConfig->parent?>").val();
        url="/link/jq-assert?id="+rowData.id+"&type="+linkItem1+"&link_item="+typeValue1+"&link_item_id="+curSelectedItem_<?=$gridConfig->parent?>;
        jQuery.get(url, function (data){
          //alert("asserted: "+data);
          jQuery('#list_<?=$gridConfig->selectedGridId?>').trigger('reloadGrid');
          jQuery('#list_<?=$gridConfig->id?>').trigger('reloadGrid');

          });
        }
      },
<?php
  }
		?>
    beforeRequest: function(){
        var filterValue=jQuery("#filter_<?=$gridConfig->id?>").val();
        var typeValue=jQuery("#item_type_<?=$gridConfig->id?>").val();
        var fldName=jQuery("#field_name_<?=$gridConfig->id?>").val();
        //alert(typeValue);
        //var typeValueLow=typeValue.toLowerCase();
				//fields is defined in all.phtml
        var keyField=fields[fldName][typeValue];
        //alert(keyField);
        var keyStr='';
				if (typeof(keyField)=='string' && keyField.length>0){
   				keyStr="&key="+keyField;
   				//alert(keyStr);
				}
        <?php
		if ($gridConfig->parent) {
			?>

        var typeValue2=jQuery("#item_type_<?=$gridConfig->parent?>").val();
        //alert(curSelectedItem_<?=$gridConfig->parent?>);
        //var parId=jQuery('#list_<?=$gridConfig->parent?>').getGridParam['selrow'];
        //alert(parId);

       <?php
			$selectedStr = "?";
			if ($gridConfig->allItemsGrid) {
				//show all and selected(marked)
				$selectedStr = "?show_selected=1&";
			} elseif ($gridConfig->selectedItemsGrid) {
				//show only selected
				$selectedStr = "?show_selected=0&";
			}
			?>
       var actionName="jq-list2";
	   if (typeValue=='image'){
         var actionName="jq-image-list";
	   }
	   //alert(typeValue);
	   if (subGridProps[typeValue]){
           jQuery('#list_<?=$gridConfig->id?>').setGridParam({
               url:"/link/"+actionName+"/type/"+typeValue+"/<?=$selectedStr?>link_item="+typeValue2+"&link_item_id="+curSelectedItem_<?=$gridConfig->parent?>+keyStr+"&quest_filter="+filterValue+"&nd="+new Date().getTime()
               , subGridUrl: '/link/jq-item2/type/'+typeValue+'/?fld='+subGridProps[typeValue]['field']+'&cell_type='+subGridProps[typeValue]['type']+'&nd='+new Date().getTime()
               , subGridModel: subGridProps[typeValue]['model']
               //[{ name  : ['Content'], width : [280] }],
               //, subGridUrl: '/link/jq-item/type/'+typeValue+'/?nd='+new Date().getTime()

               }
           )

       }else{
        jQuery('#list_<?=$gridConfig->id?>').setGridParam({
                url:"/link/"+actionName+"/type/"+typeValue+"/<?=$selectedStr?>link_item="+typeValue2+"&link_item_id="+curSelectedItem_<?=$gridConfig->parent?>+keyStr+"&quest_filter="+filterValue+"&nd="+new Date().getTime()
                }
            )
         }
        <?php
		} else {
			?>
		 
         if (subGridProps[typeValue]){
         jQuery('#list_<?=$gridConfig->id?>').setGridParam({
             url:"/link/jq-list2/type/"+typeValue+"/?quest_filter="+filterValue+keyStr+"&nd="+new Date().getTime()
                , subGridUrl: '/link/jq-item2/type/'+typeValue+'/?fld='+subGridProps[typeValue]['field']+'&cell_type='+subGridProps[typeValue]['type']+'&nd='+new Date().getTime()
                , subGridModel: subGridProps[typeValue]['model']//[{ name  : ['Content'], width : [280] }],
                //, subGridUrl: '/link/jq-item/type/'+typeValue+'/?nd='+new Date().getTime()
             }
         );
         }else{
        	 jQuery('#list_<?=$gridConfig->id?>').setGridParam({
                 url:"/link/jq-list2/type/"+typeValue+"/?quest_filter="+filterValue+keyStr+"&nd="+new Date().getTime()
                 }
             );
         }
        <?php
		}
		?>
        //}
        //alert(jQuery('#list_<?=$gridConfig->id?>').getGridParam('subGridUrl'));
      }

<?
if ($gridConfig->selectedItemsGrid){
?>
}).navGrid('#pager_<?=$gridConfig->id?>',{add:false,edit:true,del:false,search:false},{},{},{}).navButtonAdd(
		'#pager_<?=$gridConfig->id?>',{
			  caption:"",
			  position:'first',
			  buttonimg: "/js/jqgrid/themes/sand/images/row_delete.gif",
			  title: "<?=$this->view->l('Delete link')?>",
			  onClickButton:function(){
				  if (confirm("<?=$this->view->l('Delete the link?')?>")){
				  var url = "/link/del-link/?rnd="+Math.random();
				  var dataObj=
					{parent_id:curSelectedItem_<?=$gridConfig->parent?>,
					 parent_type:jQuery("#item_type_<?=$gridConfig->parent?>").val(),
				   type: jQuery("#item_type_<?=$gridConfig->id?>").val(),
				   id:curSelectedItem_<?=$gridConfig->id?>}
				  jQuery.get(
					        url,
					        dataObj,
					        function (data){
				   		        if (data=='Done'){
												jQuery('#list_<?=$gridConfig->id?>').trigger('reloadGrid');
												<?
												if ($gridConfig->allItemsGridId){
												?>
												jQuery('#list_<?=$gridConfig->allItemsGridId?>').trigger('reloadGrid');
												<?
                        }
												?>
												//alert('<?=$this->view->l("Link is deleted from KB")?>');
				   	   		    }else{
					   	   		    alert('<?=$this->view->l('Error deleting the link')?>');
					   	   		  }
				   	        }
				  );


			  }
			  }
			}
			);

<?
}else{
?>
}).navGrid('#pager_<?=$gridConfig->id?>',{add:false,edit:false,del:false,search:false},{},{},{url:"/link/del-item/",modal:true}).navButtonAdd('#pager_<?=$gridConfig->id?>',{
	caption:"",position:'first',
	buttonimg: "/js/jqgrid/themes/sand/images/row_add.gif",
	title: "<?=$this->view->l('Add Item')?>",
	onClickButton:function(){
		var typeValue=jQuery("#item_type_<?=$gridConfig->id?>").val();
		jQuery("a#box_<?=$gridConfig->id?>").attr('href','/kb/edit?type='+typeValue);
		var oBoxLink=jQuery("a#box_<?=$gridConfig->id?>");
		//alert(oBoxLink.href); Comentado por GC
		return showBox(oBoxLink);
	}
}).navButtonAdd(
		'#pager_<?=$gridConfig->id?>',{
			  caption:"",
			  position:'first',
			  buttonimg: "/js/jqgrid/themes/sand/images/row_delete.gif",
			  title: "<?=$this->view->l('Delete item')?>",
			  onClickButton:function(){
				  var addText='';
				  if (jQuery("#item_type_<?=$gridConfig->id?>").val()=='text'){
					  addText='<?=$this->view->l('All highlights and its links will be removed, forever!')?>';
				  }
				  if (confirm("<?=$this->view->l('Delete the item and all its links?\n')?>"+addText)){
				  var url = "/kb/del/?rnd="+Math.random();
				  var dataObj={
				   type: jQuery("#item_type_<?=$gridConfig->id?>").val(),
				   id:curSelectedItem_<?=$gridConfig->id?>
				  }

				  jQuery.get(
					        url,
					        dataObj,
					        function (data){
						        
				   		        if (data.substring(0, 4)=='Done'){
									jQuery('#list_<?=$gridConfig->id?>').trigger('reloadGrid');
									<?
									if ($gridConfig->allItemsGridId)  {
									?>
									jQuery('#list_<?=$gridConfig->allItemsGridId?>').trigger('reloadGrid');
									<?
                                            }
									?>
									alert('<?=$this->view->l("Items and all related links are deleted from KB")?>');
				   	   		    } else {
					   	   		    alert('<?=$this->view->l('Error deleting the item')?>');
					   	   		  }
				   	        }
				  );



			  }
			  }
			}
			);
<?
}
?>






jQuery("#submit_filter_<?=$gridConfig->id?>").click(
     function(){


     //alert(catIdValue);
     //if (filterValue.length>1 || catIdValue>0){
      jQuery('#list_<?=$gridConfig->id?>').trigger('reloadGrid');
      curSelectedItem_<?=$gridConfig->id?>='';
      //}else{
      // alert('<? //=$this->l('Select category or enter keywords')				?>');
      //}
       return false;
     }
    );


//jQuery("#show_editor_<?=$gridConfig->id?>").click(function(){
//	  //alert('editor');
//    jQuery.taToFck();
//   }
//);

//$.fn.extend({

//jQuery("p,span").live('click',function(){
// alert($(this).attr('id'));
//});

jQuery.taToFck=function(id){
	//alert(id);
	if (parseInt(id)>0){
		jQuery("#textdiv_"+id).replaceWith("<textarea id='textdiv_"+id+"' class='text-editors' onclick='parent.jQuery.taToFck("+id+");' style='height:210px;width:350px;overflow:auto;word-wrap:break-word;'>"+jQuery("#textdiv_"+id).html()+"</div>");
	  fckEd=jQuery("#textdiv_"+id).fck({ toolbar:'HL', height:300, width:350 });

	  jQuery(window,fckEd).ready(function(){
		    //alert('Loaded '+id);
		    jQuery('span',fckEd).click(
		    		  function(){
		            alert('click');
		    		  }
				);
		  });
	  //jQuery('#list_<?=$gridConfig->id?>').setSelection(id);
	  //jQuery("#sel_item_<?=$gridConfig->id?>").text(id);
    //curSelectedItem_<?=$gridConfig->id?>=id;

	}else{
	  jQuery(".text-editors").fck({ toolbar:'HL', height:300, width:350 });
	}
};




//$('#test',frames[0].document).click(function(){ //bind function to	event from element *inside iframe*
//	    $('<b>TESTE</b>').appendTo('body').click(function(){ // append element to the *parent frame* and assing a click handler to it
//	         alert('test');
//	     });
//
//	});

//jQuery("div#shim-layer-<?=$gridConfig->id?> p").live('click',function(){
//	alert('p!');
//});

/* current text field key name, used to extract the data from the KB */
var curTextField='';
<?
if ($gridConfig->hlEditor){
?>
jQuery("#hl_editor_<?=$gridConfig->id?>").click(
		function(){
			  var div=$("<div></div>");
			  //alert(div);

			  var curRow=jQuery('#list_<?=$gridConfig->id?>').getGridParam('selrow');
			  var rowData = jQuery("#list_<?=$gridConfig->id?>").getRowData(curRow);
			  var filterValue=jQuery("#filter_<?=$gridConfig->id?>").val();
        var typeValue=jQuery("#item_type_<?=$gridConfig->id?>").val();
        var url='/link/jq-item2/type/'+typeValue+'/?rowid='+curRow+'&id='+rowData.id+'&fld='+subGridProps[typeValue]['field']+'&cell_type='+subGridProps[typeValue]['type']+'&nd='+new Date().getTime()+'&origen=hl_editor'; //modificado por GC
        curTextField=subGridProps[typeValue]['field'];
        //alert(url);
        //var curRow1=curRow;
        <?
		if ($gridConfig->subGrid) {
			?>
        var colId=2;
        <?
		} else {
			?>
        var colId=1;
        <?
		}
		?>

        var tdSelector="table#list_<?=$gridConfig->id?>>tbody>tr:[id="+curRow+"]>td:eq("+colId+")";
        //alert(tdSelector);
        var tdEl=jQuery(tdSelector);

        var tdH=tdEl.outerHeight();
        var pos=tdEl.position();
        //alert(pos.left+'-'+pos.top);
			  $.getJSON(
					 url,
				   function(json){
				     //alert( "Data Saved: " + msg );
				       //alert(json.rows[0]['cell'][0]);
					     jQuery('#shim-table-<?=$gridConfig->id?>').hide();
					     jQuery('#textdiv_<?=$gridConfig->id?>').html('').append(json.rows[0]['cell'][0]);
					     jQuery('#shim-table-<?=$gridConfig->id?>').css({'top':pos.top+parseInt(tdH),'left':pos.left}).show('slow');
				   }
				 );



			  //$(div).html("<p>This is a paragraph to be highlighted</p><p>This is another paragraph to be highlighted</p>")


		}

);


jQuery("#hl_<?=$gridConfig->id?>").click(
 function(){
  var val=jQuery("#filter_<?=$gridConfig->id?>").val();
  //alert(val);
  if (val.length==0){
	  jQuery("#shim-table-<?=$gridConfig->id?>").removeHighlight();
	}else{
    jQuery("#shim-table-<?=$gridConfig->id?>").highlight(val);
  }
 }
);

jQuery('#hl-re-<?=$gridConfig->id?>').click(function(){
	var val=jQuery("#filter_<?=$gridConfig->id?>").val();
    $("#shim-table-<?=$gridConfig->id?>").highlightRegex();
    if(val != '') {
      $("#shim-table-<?=$gridConfig->id?>").highlightRegex(new RegExp(val, 'ig'));
    }
  });


var selectedHl_<?=$gridConfig->id?>='';
jQuery("table#shim-table-<?=$gridConfig->id?> span.hw-highlight").live('click',function(){
	var id=$(this).attr('id');

  $("span#sel-hl-<?=$gridConfig->id?>").text(id);
  selectedHl_<?=$gridConfig->id?>=id;

});



jQuery("#text_rte_<?=$gridConfig->id?>").click(function(){

	jQuery("textarea.text-editors").rte({
        base_url: 'http://hw-archive.com',
        css: ['http://hw-archive.com/css/rte.css'],
        width: 350,
        height: 400,
        controls_rte: rte_toolbar,
        controls_html: html_toolbar
        });
});
jQuery("#make_hl_link_<?=$gridConfig->id?>").click(
  function(){
	 //alert(document.getSelection());
	 //done for firefox



	 //now create link itself
	 var childType=jQuery("#item_type_<?=$gridConfig->id?>").val();
	 var parentType=jQuery("#item_type_<?=$gridConfig->parent?>").val();
	 if (childType!='text' && parentType!='mt'){
		alert('<?=$this->view->l("Highlight is created only for Text, when a maintitle is selected as a parent")?>');
		return false;
	 }

	 if (typeof(curSelectedItem_<?=$gridConfig->parent?>)!='undefined' && curSelectedItem_<?=$gridConfig->parent?>>0){

		   var selObj = window.getSelection();
		   var selObjStr=selObj.toString();

		   var selRange = selObj.getRangeAt(0);
		   //var newNode = jQuery("<span></span>").css({'background-color':'#ff9900'});
//		   if (typeof(selObj.getRangeAt(1))!='undefined'){
//				alert('<?=$this->view->l('Multiple selections are not allowed')?>');
//				return false;
//		   }

		   //range.selectNode(document.getElementsByTagName("div").item(0));


	     var curRow=curSelectedItem_<?=$gridConfig->id?>;
       //var curRowData = jQuery("#list_<?=$gridConfig->id?>").getRowData(curRow);
       var parRow=curSelectedItem_<?=$gridConfig->parent?>;
       //var parRowData= jQuery("#list_<?=$gridConfig->parent?>").getRowData(parRow);

       //var textContents=jQuery("#textdiv_"+curRow).html();
       var textContents=jQuery("#textdiv_<?=$gridConfig->id?>").html();
       //alert("textdiv_"+curRow+':'+textContents);
       if (textContents.length<1){
					return false;
       }
       //alert('Connecting '+childType+'('+curRow+') to '+parentType+'('+parRow+')');
       if (selObjStr.length<1){
				alert("Text is not selected");
				return false;
       }
       selObjStr=selObjStr.replace(/[\s]+/g,' ');
       //alert('Highlighted text is:\n'+selObjStr);
	   //alert(curRow+":"+parRow);

	        url="/link/jq-assert-hl-link/";
	        jQuery.post(
	    	        url,
	    	        { 'child'    : childType,
		    	        'child_id' : curRow,
		    	        'parent'   : parentType,
		    	        'parent_id': parRow,
		    	        'hl'       : selObjStr },
	    	        function (data){
				    	  //alert("data: "+ data);
				    	  if (parseInt(data)<1){
				    		  alert('<?=$this->view->l ( 'Error creating highlight' )?>');
				    	  }else{
					    	  hlCount=parseInt(data);
					    	  //alert('hl id :'+hlCount);
					    	  if (isNaN(hlCount)){
						    	  alert('Error creating HL'+hlCount);
						    	  return false;
					    	  }
									//mark highlighted text
				  var div=document.createElement("div");
                  $(div).attr({id:'hlDiv-'+hlCount, style:'display:inline;clear:all'});
                  var spanS=document.createElement("span");
                  $(spanS).attr({id:'hlS-'+hlCount,class:'hw-highlight', style:'cursor:pointer;'});
                  var spanE=document.createElement("span");
                  $(spanE).attr({id:'hlE-'+hlCount,class:'hw-highlight', style:'cursor:pointer;'});
                  //alert($(div).attr('id'));
                  selRange.surroundContents(div);

                  $(spanS).html(" "+hlCount+">>>").insertBefore(div);
                  $(spanE).html("<<<"+hlCount+" ").insertAfter(div);
                  var inHtml=$(div).html();
                  //alert(inHtml);
                  $(div).remove();
                  $(spanS).after(inHtml);

                  //alert('now saving text with highlights');
                  //textContents=jQuery("#textdiv_<?=$gridConfig->id?>").content();
                  //alert(textContents);
                  //return false;
                  textContents=jQuery("#textdiv_<?=$gridConfig->id?>").html();
                  //textContents=jQuery("div#hldiv_editor").html();
                  jQuery.post(
                      "/link/save-text",
                      {
                    	  'type': childType,
                    	  'text_fld_name': curTextField,
  		    	           'id' : curRow,
                    	  'text': textContents
                      },function(res){
						 alert('Highlight created: '+res);
                      }
                      );
				     //var termStr='hl-'+uid+'_'+childType+'_'+curRowData.id+'-'+parentType+'_'+parRowData.id;
				     //jQuery(newNode).attr({'id':termStr,'title':termStr});
					 //selRange.surroundContents(newNode);
			    	  /*
			    	  alert('hl created, now checking...');
			    	  //alert(selObjStr);
			    	  jQuery.post(
			    			"/link/check-predicate/",
			    			{'term_head': childType+'_hl_to_'+parentType,
			             		'body_arr[0]': parseInt(curRow),
			             		'body_arr[1]': parseInt(parRow),
			             		'body_arr[2]': selObjStr
					        },function(data){
			            	  alert("Check predicate: "+data);
			            	  //remove formatting from selObjStr, before comparison....
			            	  if (data=='true'){
				            	  alert('<?=$this->view->l ( 'Highlight created successfully' )?>');
				            	  //jQuery()
				            	  oTermStr="#"+termStr;
				            	  //alert(oTermStr);
			            		  $(oTermStr).css({'border': 'solid thin red'});//need to finish, change  /link/jq-hl/...
				            	}else{
				            		  alert('Error creating link');
					            }
			             	}
					    )
					    */
		    	  }
              });
	   }else{
		   alert("<?=$this->view->l ( 'No parent item selected, please select.' )?>");
		 }


	    //var newHTML = "<span class='newSpan'>" + rng.text + "</span>";
	    //rng.pasteHTML(newHTML);

  }
);

$('button#del_hl_<?=$gridConfig->id?>').click(function(){
	$('span#'+selectedHl_<?=$gridConfig->id?>).remove();
	var tmp=selectedHl_<?=$gridConfig->id?>.split('-');
	//str = jQuery.trim(str);
	idNum=$.trim(tmp[1]);
	$('span#hlE-'+idNum).remove();

	 var childType=jQuery("#item_type_<?=$gridConfig->id?>").val();
	 var parentType=jQuery("#item_type_<?=$gridConfig->parent?>").val();
   var curRow=curSelectedItem_<?=$gridConfig->id?>;
     //var curRowData = jQuery("#list_<?=$gridConfig->id?>").getRowData(curRow);
   var parRow=curSelectedItem_<?=$gridConfig->parent?>;
   var textContents;

   url="/link/jq-retract-hl-link/";
   jQuery.post(
	        url,
	        { 'child'    : childType,
   	        'child_id' : curRow,
   	        'hl_id'       : idNum },
	        function (data){
   		        if (data){
								alert("HL is deleted from KB");
								textContents=jQuery("#textdiv_<?=$gridConfig->id?>").html();
                jQuery.post(
                    "/link/save-text",
                    {
                  	  'type'    : childType,
                  	  'text_fld_name': curTextField,
		    	            'id' : curRow,
                  	  'text': textContents
                    },function(res){
											alert("<?=$this->view->l('Text is updated')?>");
                    }
                    );

   	   		    }
   	        }
   	        );



	//hlCount--;
});

//jQuery("#cur_hl_<?=$gridConfig->id?>").click(
//		  function(){
//
//			     eds=$.fck.editors;
//
////		       for(i in eds){
////		           //alert(i+':'+eds[i]);
////		           obj=eds[i];
////		           ed=obj.e;
////		           for(el in ed){
////			           if (ed[el]){
////		        	    alert(el+'='+ed[el]);
////			           }else{
////			        	   alert(el+'->'+ed.el);
////				         }
////		           }
////
////		       }
//
//			}
//
//);

<?php
  }
?>

jQuery("#shim-close-<?=$gridConfig->id?>").click(
	function(){
		jQuery("#shim-table-<?=$gridConfig->id?>").hide();

	}
);

//jQuery("#show_hl_<?=$gridConfig->id?>").click(
//     function(){
////       eds=$.fck.editors;
////       for(i in eds){
////           alert(i+':'+eds[i].instanceName.EditorDocument.body.innerHTML);
////       }
//
////       var e = FCKeditorAPI.GetInstance('textarea_13') ;
////       if( !e.EditorDocument ) {
////           alert('This function can only be used in WYSIWYG mode');
////           return false;
////       } else {
////           alert(e.EditorDocument.body.innerHTML);
////           return true;
////       }
//       var curRow=jQuery('#list_<?=$gridConfig->id?>').getGridParam('selrow')
//       var htmlStr='';
//       htmlStr = $.fck.content("textdiv_"+curRow);
//       if (htmlStr){
//           //find all highlighted items
//         //$("span[style*=background-color]",htmlStr).each(function(){alert($(this).html())});
//    	   $("span",htmlStr).each(function(){
//        	     if($(this).css("background-color")!='transparent'){
//        	       alert($(this).text());
//        	     }
//        	   });
//       }
//       //instanceName.EditorDocument.body.innerHTML;
//     }
//    );
    <?
		if ($gridConfig->subGrid) {
			?>
  jQuery.fixIds<?=$gridConfig->id?>=function(){
     jQuery('#list_<?=$gridConfig->id?> tr').each(
        function(i){
            var newId=$(this).attr('id').replace(/\//g,'_');
						newId=newId.replace(/\./g,'-');
            newId='id-'+newId;
						$(this).attr('id',newId);
			      //alert(newId);
            //$(this).
        }

     );
};

    <?php

  }
		$this->view->jQuery ()->onLoadCaptureEnd ();
		?>
    --></script>

		<?php
		/*
		 <button style="background-color: lighblue;"
		 id='show_editor_<?=$gridConfig->id?>'
		 title="<?=$this->view->l ( 'Click to open an HTML editor for any open text on the screen' )?>"><?=$this->view->l ( 'HTML/Text Editor' )?></button>

		 ?>
<button id="show_id_<?=$gridConfig->id?>">id</button>
		 <button id="cur_hl_<?=$gridConfig->id?>">Link HL</button>
		 <? */
		if ($gridConfig->hlEditor){
		  ?>
<button style="background-color: navy;"
	id="hl_editor_<?=$gridConfig->id?>"
	title="<?=$this->view->l ( 'Hightlight editor' )?>"><?=$this->view->l ( 'HL Editor' )?></button>
<div style="height: 6px;">&nbsp;</div>
		  <?php
		}

  }

  /**
   * Generates Grid of current type items linked to some other grid
   * @param $id
   * @param $itemType - default item Type
   * @param $direction - means that left grid is child of the current grid which is to the right
   * @return string
   */
  public function gridInstance(Hw_GridConfig $gridConfig) {
    //$connected to 0,1,2,3.... - id number of grid
    //$typeOfConnection - child_of, parent_of, both
    //allItemsGrid - is a grid with all available items of the current type.
    $this->view->placeholder ( 'grid_' . $gridConfig->id )->captureStart ();
    ?>
<a href="" id='box_<?php print $gridConfig->id?>' style='visibility:hidden;'></a>
<table id="shim-table-<?=$gridConfig->id?>"
	style="width: 185; height: 200; background-color: #ffffff; border: solid thick #F1C9E9; position: absolute; top: 200; display: none;">
	<tr>
		<td width="95%">
		<button id="make_hl_link_<?=$gridConfig->id?>"
			title="<?=$this->view->l('Create highlight')?>">Create HL</button>
		<!--<button id="text_rte_<?=$gridConfig->id?>">Editor</button>//-->
		<button id="del_hl_<?=$gridConfig->id?>"
			title="<?=$this->view->l('Delete highlight')?>">Delete HL</button>
		<span id="sel-hl-<?=$gridConfig->id?>" style="color: purple"></span></td>
		<td width="5%" id="shim-close-<?=$gridConfig->id?>"
			style="font-weight: bold; font-size: 18px; cursor: pointer;">x</td>
	</tr>
	<tr>
		<td colspan="2" id="shim-content-<?=$gridConfig->id?>">
		<div id='textdiv_<?=$gridConfig->id?>' class='text-editors'
			style='height: 210px; width: 350px; overflow: auto; word-wrap: break-word;'></div>
		</td>
	</tr>

</table>
<table id="grid-table-<?=$gridConfig->id?>" cellpadding="0"
	cellspacing="0" style="border: solid thin #ffffff">
	<tr>
		<td>
		<div align="left" style="color: #000000; padding-bottom: 10px;"><?=$gridConfig->gridCaption?></div>
		<?/* print $this->view->l ( 'Selected' )*/?><input
			id="sel_item_<?=$gridConfig->id?>" type="hidden" value=0> <?=$this->itemsCombo ( $gridConfig->id )?>
			<?=$this->gridInit ( $gridConfig )?>
		<table id="list_<?=$gridConfig->id?>" class="scroll"
			dir="<?=$this->langObj->dir?>" cellpadding="0" cellspacing="0"></table>
		<div id="pager_<?=$gridConfig->id?>" class="scroll"
			style="text-align: center;" dir="<?=$this->langObj->dir?>"></div>
		</td>
	</tr>
</table>

			<?php
			$this->view->placeholder ( 'grid_' . $gridConfig->id )->captureEnd ();
			return $this->view->placeholder ( 'grid_' . $gridConfig->id );
	}

}

?>