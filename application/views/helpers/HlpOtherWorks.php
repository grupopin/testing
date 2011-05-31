<?php
class Zend_View_Helper_HlpOtherWorks{
	public $view;
	public function setView(Zend_View_Interface $view)
	{
		$this->view = $view;
	}

	function hlpOtherWorks($id, $work, $workArr, $lang, &$titleArr, $titleFld=null)
	{
		if ($titleArr[$work] && is_null($titleFld)){
			$titleFld=$titleArr[$work];
		}
		//print $titleFld."<br>";
		?>
<table align=center align=left border=0 width=280>
<?
$idFldName=$work.'_id';
$sz=sizeof($workArr);
$otherWorksIds=array();
for($i=0;$i<$sz;$i++){
	if ($workArr[$i][$idFldName]!=$id && !in_array($workArr[$i][$idFldName], $otherWorksIds)){
		$otherWorksIds[]=$workArr[$i][$idFldName];
		//print_r($otherWorksIds);
		$otherWorks[]=$workArr[$i];
	}
}
for($i=0;$i<=5;$i+=2){
	$val=$this->view->textFldValue($titleFld,$lang,$otherWorks[$i]);
	$val=$this->view->hlpFormatText($val,50);
	$val2=$this->view->textFldValue($titleFld,$lang,$otherWorks[$i+1]);
	$val2=$this->view->hlpFormatText($val2,50);
	?>
	<tr>
		<td valign=top align=left class="related_items"
			style="padding-left: 6px;"><?if ($val){?> <?if ($otherWorks[$i]['img']){ ?>
		<img width=60% src=<?=$otherWorks[$i]['img']?>> <?}else{
			?> <img border=0 src=http://www.hw-archive.com/images/pic1.jpg> <?
}
?>
		<div><br style="line-height: 10px;">
		<a href="/<?=$work?>/item/id/<?=$otherWorks[$i][$idFldName]?>"
			class="related_items"><?=$val?></a></div>
			<?}?></td>
		<td width=10></td>
		<td class="related_items" align=center><?if ($val2){?> <?if ($otherWorks[$i+1]['img']){ ?>
		<img width=60% src=<?=$otherWorks[$i+1]['img']?>> <?}else{
			?> <img border=0 src=http://www.hw-archive.com/images/pic1.jpg> <?
}
?>
		<div><br style="line-height: 10px;">
		<a href="/<?=$work?>/item/id/<?=$otherWorks[$i+1][$idFldName]?>"
			class="related_items"><?=$val2?></a></div>
			<?}?></td>

	</tr>
	<?
}

?>
</table>
<?
}

}
?>