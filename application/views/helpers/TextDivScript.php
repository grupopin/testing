<?
class Zend_View_Helper_TextDivScript
{
  function textDivScript(){
  	?>
  
<script>
function switchDiv(divName){
  var divs=document.getElementsByTagName('DIV');
  curDiv='txtdiv_'+divName;
  document.getElementById(curDiv).style.display='';
  
  for(i in divs){
   idVal=divs[i].id;
   //alert(idVal);
   if (idVal && idVal.indexOf('txtdiv_')==0 && idVal!=curDiv){
    divs[i].style.display='none';
   }
  }
  
  
}
</script>
<?
}
}
?>