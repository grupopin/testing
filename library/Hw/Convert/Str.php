<?php
class Hw_Convert_Str{

  public function getcsv( $input, $delimiter=",", $enclosure='"', $escape="\\"){

    if(!function_exists('str_getcsv')){
      $fp=fopen("php://temp/maxmemory:$fiveMBs", 'r+');
      fputs($fp, $input);
      rewind($fp);

      $data=fgetcsv($fp, mb_strlen($input), $delimiter, $enclosure); //  $escape only got added in 5.3.0


      fclose($fp);
      return $data;
    }else{
      return str_getcsv($input, $delimiter=",", $enclosure='"', $escape="\\");
    }

  }

public function htmlDecode($str){
  $table=get_html_translation_table(HTML_ENTITIES, ENT_QUOTES);
  $str2=str_replace(array_values($table),array_keys($table),$str);
  return $str2;

}

public function htmlEncode($str){
  $table=get_html_translation_table(HTML_ENTITIES, ENT_QUOTES);
  $str2=str_replace(array_keys($table),array_values($table),$str);
  return $str2;

}

  public function stripWrappingQuotes( $str, $symb="'"){
    if(mb_substr($str, 0, 1) == $symb){
      $str1=substr_replace($str, '', 0, 1);
    }else{
      return $str;
    }

    if(mb_substr($str1, -1, 1) == $symb){
      $str1=substr_replace($str1, '', -1, 1);
    }else{
      return $str;
    }

    return $str1;

  }

}
?>