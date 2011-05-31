/**
 * add class to all descendant tree of the node
 */
;$.fn.markNodes = function(cl) {
	  var ret = [];
	  this.contents().each( function() {
	    var fn = arguments.callee;
	      if (this.nodeType == 1){ 
	        $(this).addClass(cl);
	      }else{
		      $(this).contents().each(fn);
	      }
	  });
	  
	};
/**
 * find left and right siblings
 * 
 */
$.fn.extend({
	rightSiblings: function(el){
	  var curEl=this;
	  var found=false;
	  var rightSibls=[];
	  
	  this.parent().contents().each(function(){
		  //var fn = arguments.callee;
		  //alert($(curEl).attr('id')+','+$(this).attr('id'));
		  if (found){
		      rightSibls.push(this);
		  }
			if (el==this){
       //alert('same object');
       found=true;
			}else if($(curEl).attr('id')!='undefined' && ($(curEl).attr('id')==$(this).attr('id'))){
			  //alert('found');
			  found=true;
		  }
	 })
 
  return $(rightSibls);
  },
	leftSiblings: function(el){
	  var curEl=this;
	  var found=false;
	  var leftSibls=[];
	  
	  this.parent().contents().each(function(){
		  //var fn = arguments.callee;
		  //alert($(curEl).attr('id')+','+$(this).attr('id'));
		  if (!found){
				if (el==this){
			       //alert('same object');
			       found=true;
				}else if($(curEl).attr('id')!='undefined' && ($(curEl).attr('id')==$(this).attr('id'))){
						  //alert('found');
						  found=true;
				}
				if (!found){
				 leftSibls.push(this);
				}
		  }else{
				return false;
		  }
	 })
 
  return $(leftSibls);
  }  
  

});