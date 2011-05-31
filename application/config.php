<?php
$L=new Hw_L();

$searchIgnore=array(
 'for','and','nor','but','or','yet','so'
);



$skinIcons=array(
1=>'images/skins/skins_ep_eng',
2=>'images/skins/skins_cl_eng',
3=>'images/skins/skins_ho_eng',
4=>'images/skins/skins_id_eng',
5=>'images/skins/skins_ea_eng',

);

$skinNames=array(
1=>'epidermis',
2=>'clothes',
3=>'houses',
4=>'identity',
5=>'earth',

);

//to do add sql , which takes all cats from hwarch_shop !!!!!!!!!!!
$shopCats=array(
      'accessories'=>array('title'=>'Accessories'),
      'bestseller'=>array('title'=>'Bestseller'),
      'postage stamps'=>array('title'=>'Postage stamps'),
      'fontain'=>array('title'=>'Fontain'),
      'apparel'=>array('title'=>'Apparel'),
      'books'=>array('title'=>'Books'),
      'office supplies'=>array('title'=>'Office supplies'),
      'flags'=>array('title'=>'Flags'),
      'movies'=>array('title'=>'Movies'),
      'art_supplies'=>array('title'=>'Art Supplies'),
      'ceramic_tile'=>array('title'=>'Ceramic tile'),
      'furoshiki'=>array('title'=>'Furoshiki'),
      'gifts'=>array('title'=>'Gifts'),
      'notecards'=>array('title'=>'Calendars and Diaries / Notebooks'),
      'miniature'=>array('title'=>'Miniature'),
      'coins'=>array('title'=>'Coins'),
      'objects'=>array('title'=>'Objects'),
      'stationery'=>array('title'=>'Stationery'),
      'relicas'=>array('title'=>'Porcelain / Ceramics / Glassware'),
      'prints'=>array('title'=>'Posters / Art Prints / Postcards'),
      'products_of_month'=>array('title'=>'Products of the Month'),
      'jewellery'=>array('title'=>'Jewellery'),
      'silk_scarf / Scarf'=>array('title'=>'Silk Scarf / Scarf'),
      'games'=>array('title'=>'Games'),
      'textiles'=>array('title'=>'Textiles (Drapery) / Wool'),
      'watches'=>array('title'=>'Watches')
);
foreach($shopCats as $catName=>$catProps){
  $shopCats[$catName]['term_name']=strtolower(preg_replace('/[\\W]+/','_',$catProps['title']));//the same should be in importShop.php!!!!
}


$itemTitles = array (
     'skin' => 'title_en',
     'mt' => 'maintitle_name_en',
     'hl' => 'title',
     'apa' => 'title',
     'arch' => 'title',
     'hwg' => 'title',
     'jw'=>'title',
     'paint' => 'title',
     'tap' => 'title',
     'audio' => 'title_en',
     'gallery' => 'gallery_title_en',
     'video' => 'title_en',
     'text' => 'text_title_en',
     'book' => 'title_en',
     'biography' => 'title_en',
	 'one_exhibition' => 'title_en',
	 'group_exhibition' => 'title_en',
	 'monograph' => 'title_en',
	 'catalog_exhibition' => 'title_en',
     'article' => 'title_en',
 	 'document' => 'title_en',
     'picture' => 'picture_title_en'
);

//addShopItemTitles(&$itemTitles,$shopCats,'descr');
addShopItemTitles($itemTitles,$shopCats,'descr');


$relatedItems=array(
     'skin' => 'skin',
     'mt' => 'mt',
     'hl' => 'id',
     'apa' => 'apa',
     'arch' => 'arch',
     'hwg' => 'hwg',
     'jw'=>'jw',
     'paint' => 'workNumber',
     'tap' => 'tap',
     'audio' => 'audio',
     'gallery' => 'gallery',
     'video' => 'video',
     'text' => 'txt',
	 'article' => 'article_id',
     'document' => 'doc_id',
     'picture' => 'picture_id'
);

$itemIds=array(
     'skin' => 'skin',
     'mt' => 'mt',
     'hl' => 'id',
     'apa' => 'apa',
     'arch' => 'arch',
     'hwg' => 'hwg',
     'jw'=>'jw',
     'paint' => 'workNumber',
     'tap' => 'tap',
     'audio' => 'audio',
     'gallery' => 'gallery',
     'video' => 'video',
     'text' => 'txt',
     'book' => 'book',
     'biography' => 'bio',
	 'one_exhibition' => 'one_ex',
	 'group_exhibition' => 'group_ex',
     'monograph' => 'mono',
     'catalog_exhibition' => 'cat_ex',
	 'article' => 'article_id',
     'document' => 'doc_id',
	 'picture' => 'picture_id'
     );
     
     //addShopItemTitles(&$itemIds,$shopCats,'id');
     addShopItemTitles($itemIds,$shopCats,'id');

     $itemWorkNumber = array (
     'skin' => '',
     'mt' => '',
     'hl' => '',
     'apa' => 'workNumber',
     'arch' => 'workNumber',
     'hwg' => 'workNumber',
     'jw'=>'workNumber',
     'paint' => 'workNumber',
     'tap' => 'workNumber',
     'audio' => '',
     'gallery' => '',
     'video' => '',
     'text' => 'txt',
     'book' => '',
     'picture' => ''
     );
 
 $itemYear= array('gallery'=>'gallery_year');
 $itemKeyword=array('gallery'=>'gallery_keywords');

 $fields=array(
 'workNumber'=>$itemWorkNumber,
 'id'=>$itemIds,
 'title'=>$itemTitles,
 'year'=>$itemYear,
 'keyword'=>$itemKeyword

 );


$objectCategory['work']=array(
 'apa','arch','hwg','jw','paint','tap'
);

$objectCategory['docs']=array(
 'text','video','audio','gallery','biography','one_exhibition','group_exhibition','article','document','picture'
);

$objectCategory['skin']=array(
 'skin','mt','hl'
);


 $subGridFld = array (
    'mt' =>
 array (
    'field' => 'maintitle_name_en,maintitle_name_ge,maintitle_sort_id',
    'type' => 'str,str,str',
    'edit-field'=>'maintitle_name_en,maintitle_name_ge,maintitle_summary_en,maintitle_summary_ge,maintitle_sort_id',
    'edit-type'=>'ta,ta,ha,ha,str',
    'insert-field'=>'mt',
    'insert-type'=>'str',
    'model' => "[{ name  : ['Details'], width : [350] }]" ),
  
    'biography' =>
 array (
    'field' => 'title_en,title_ge,title_sort_id',
    'type' => 'str,str,str',
    'edit-field' => 'title_en,title_ge,contents_en,contents_ge,title_sort_id',
    'edit-type' => 'ta,ta,ha,ha,str',
    'insert-field' => 'bio',
    'insert-type' => 'str',
    'model' => "[{ name  : ['Details'], width : [350] }]" ),
 
     'one_exhibition' =>
 array (
    'field' => 'title_en,title_ge,title_sort_id',
    'type' => 'str,str,str',
    'edit-field' => 'title_en,title_ge,contents_en,contents_ge,title_sort_id',
    'edit-type' => 'ta,ta,ha,ha,str',
    'insert-field' => 'one_ex',
    'insert-type' => 'str',
    'model' => "[{ name  : ['Details'], width : [350] }]" ),

     'group_exhibition' =>
 array (
    'field' => 'title_en,title_ge,title_sort_id',
    'type' => 'str,str,str',
    'edit-field' => 'title_en,title_ge,contents_en,contents_ge,title_sort_id',
    'edit-type' => 'ta,ta,ha,ha,str',
    'insert-field' => 'group_ex',
    'insert-type' => 'str',
    'model' => "[{ name  : ['Details'], width : [350] }]" ),

     'monograph' =>
 array (
    'field' => 'title_en,title_ge,title_sort_id',
    'type' => 'str,str,str',
    'edit-field' => 'title_en,title_ge,contents_en,contents_ge,title_sort_id',
    'edit-type' => 'ta,ta,ha,ha,str',
    'insert-field' => 'mono',
    'insert-type' => 'str',
    'model' => "[{ name  : ['Details'], width : [350] }]" ),
 
     'catalog_exhibition' =>
 array (
    'field' => 'title_en,title_ge,title_sort_id',
    'type' => 'str,str,str',
    'edit-field' => 'title_en,title_ge,contents_en,contents_ge,title_sort_id',
    'edit-type' => 'ta,ta,ha,ha,str',
    'insert-field' => 'cat_ex',
    'insert-type' => 'str',
    'model' => "[{ name  : ['Details'], width : [350] }]" ),
 
    'text' =>
 array (
    	'field' => 'text_contents_en',
      'insert-field'=>'txt',//additional field for insert mode
      'insert-type'=>'str',
      //do not add to edit-field list 'txt'. As it is changed in two places: Id of predicate and in List of properties. 
      'edit-field' => 'text_type,text_category,text_title_en,text_title_ge,text_contents_en,text_contents_ge,text_keywords,text_date,text_summary_en,text_summary_ge,text_occasion_en,text_occasion_ge,text_published_en,text_published_ge,text_author,text_orig_lang,text_translator,text_source_en,text_source_ge,text_excerpt,text_origin',
    	'type' => 'str,str,str,str',
      'edit-type'=>'str,str,ta,ta,ha,ha,ta,str,ha,ha,ha,ha,ha,ha,str,str,str,ha,ha,ha,str',
    	'model' => "[{ name  : ['Details'], width : [350] }]"
    	),

    'skin' =>
    	array (
    	'field' => 'title_en,title_ge',
    	'type' => 'str,str',
    	'edit-field'=>'title_en,title_ge,title_long_en,title_long_ge,descr_en,descr_ge',
    	'edit-type'=>'ta,ta,ta,ta,ha,ha',
    	'insert-field'=>'skin',
    	'insert-field'=>'str',
    	'model' => "[{ name  : ['Details'], width : [350] }]"
    	),

    'hl' =>
    	array ('field' => 'text', 'type' => 'str', 'model' => "[{ name  : ['Details'], width : [350] }]" ),
    'paint' =>
    	array ('field' => 'workNumber,title,titleGerman,titleEnglish,titleFrench,finishedPlace,year,heightMm,widthMm,techniqueKeyword,technique,date,hundertwasser_comment_ge,hundertwasser_comment_en,oneManExhibitions,groupExhibitions,literatureMonographs,literatureExhibitionCatalogues,literatureVarious,literatureMagazinesPeriodicals,reproductionsArtPrints', 'type' => 'str,str,str,str,str,str,str,str,str,str,str,str,str,str,str,str,str,str,str,str,str', 'model' => "[{ name  : ['Details'], width : [350] }]" ),
    'jw' =>
    	array ('field' => 'workNumber,title,titleGerman,titleEnglish,titleFrench,finishedPlace,year,heightMm,widthMm,techniqueKeyword,technique,date,hundertwasser_comment_ge,hundertwasser_comment_en,oneManExhibitions,groupExhibitions,literatureMonographs,literatureExhibitionCatalogues,literatureVarious,literatureMagazinesPeriodicals,reproductionsArtPrints', 'type' => 'str,str,str,str,str,str,str,str,str,str,str,str,str,str,str,str,str,str,str,str,str', 'model' => "[{ name  : ['Details'], width : [350] }]" ),

    	/*
    	 'graph'=>
    	 array(
    	 'field'=>'description_portfolio,edition',
    	 'type'=>'str,str',
    	 'model'=>"[{ name  : ['Descr','ed'], width : [140,140] }]"),
    	 */
    'hwg' => array ('field' => 'workNumber,title,publishedPlace,publishedYear,descriptionPortfolio,techniqueKeyword,technique,sheetHeightMm,sheetLengthMm,imageHeightMm,imageLengthMm,printedBy,printedPlace,printedDate,edition,publishedBy,hundertwasser_comment_ge,hundertwasser_comment_en,information_ge,information_en,oneManExhibitions,literatureMonographs,literatureExhibitionCatalogues,literatureVarious,reproductionsArtPrints', 'type' => 'str,str,str,str,str,str,str,str,str,str,str,str,str,str,str,str,str,str,str,str,str,str,str,str', 'model' => "[{ name  : ['Details'], width : [350] }]" ),

		'apa' => array ('field' => ' workNumber,title,titleGerman,titleEnglish,finishedPlace,year,literatureMonographs,literatureExhibitionCatalogues', 'type' => 'str,str,str,str,str,str', 'model' => "[{ name  : ['Object props.'], width : [350] }]" ),

		'tap' => array ('field' => 'workNumber,title,titleGerman,titleEnglish,titleFrench,descriptionCategoryKeyword,finishedPlace,year,date,heightMm,widthMm,material,wovenBy,hundertwasser_comment_ge,hundertwasser_comment_en,information_ge,information_en,oneManExhibitions,groupExhibitions,literatureMonographs,literatureExhibitionsCatalogues,reproductionsArtPrints', 'type' => 'str,str,str,str,str,str,str,str,str,str,str,str,str,str,str,str,str,str,str,str,str,str', 'model' => "[{ name  : ['Details'], width : [350], height: [100] }]" ),

		'arch' => array ('field' => 'titleGerman,titleEnglish,descriptionCategoryKeyword,description,information_ge,information_en,finishedPlace,year,technique,literatureMonographs,literatureExhibitionCatalogues',
						 'type' => 'str,str,str,str,str,str,str,str,str,str,str', 
						 'model' => "[{ name  : ['Details'], width : [350] }]" ),


    'gallery'=>array('field'=>'image_path,gallery_title_en,gallery_title_ge,gallery_description_ge,gallery_description_en,gallery_year,gallery_year_circa,gallery_photographer,gallery_keywords,galery_place_ge,galery_place_en,gallery_copyright,galery_image',
                     'type'=>'img,str,str,str,str,str,str,str,str,str,str,str,str',
    								 'edit-type'=>'img,ta,ta,ha,ha,str,str,str,ta,str,str,str,str',
          					 'insert-field'=>'gallery',//additional field for insert mode
                     'insert-type'=>'str',
    								 'folder-name'=>'galery',//with one l !!
    								 'folder'=>array('image_path'=>'galery'),
                     'model' => "[{ name  : ['Details'], width : [350] }]"),

    	/*
    	 * [
    	 book-1,
    	 typeOfObject-book,
    	 image_path-'',
    	 title_en-'Living_Art: Friedensreich Hundertwasser',
    	 public_place-'Munich: Prestel Verlag',
    	 public_year-'2010',
    	 public_time-'spring',
    	 url-'http://www.randomhouse.de/prestel/'
    	 ]
    	 */
    'book'=>array(
    	 'field'=>'image_path,title_en,public_place,public_year,public_time,url,author,description_en,description_ge',
    	 'type'=>'img,str,str,str,str,link,str,str,str',
    	 'edit-type'=>'img,ta,str,str,str,str,str,ha,ha',
    	 'insert-field'=>'book',
    	 'insert-type'=>'str',
    	 'model' => "[{ name  : ['Details'], width : [350] }]",
    	 'folder-name'=>'book',
    	 'folder'=>array('image_path'=>'book'),
    	),
    	
    'image'=>array(
    	'fields'=>array(
    	 'edit'=>array(
    	 	'frontend_visible'=>array('type'=>'checkbox'),
    		'image_name'=>array('type'=>'str','readonly'=>1),
    	 	'object_type'=>array('type'=>'str','readonly'=>1)
    	 ),
    	 'insert'=>array(
    	  'image_name'=>array('type'=>'imagefile')
    	 ),
    	),
    	'model' => "[{ name  : ['Details'], width : [350] }]"
    	),

     'picture'=>array('field'=>'image_path,picture_title_en,picture_title_ge,picture_description_ge,picture_description_en,picture_year,picture_year_circa,picture_photographer,picture_keywords,picture_place_ge,picture_place_en,picture_copyright',
                     'type'=>'img,str,str,str,str,str,str,str,str,str,str,str,str',
    						 'edit-type'=>'img,ta,ta,ha,ha,str,str,str,ta,str,str,str,str',
          			 'insert-field'=>'picture_id',//additional field for insert mode
                     'insert-type'=>'str',
    						 'folder-name'=>'picture',
    						 'folder'=>array('image_path'=>'picture'),
                     'model' => "[{ name  : ['Details'], width : [350] }]"),
    	

    'audio'=>array(
    	 //refactoring on 29/11/09
    	 'fields'=>array(
    	  'edit'=>array(
    	    'title_en'=>array('type'=>'ta'),
			'title_ge'=>array('type'=>'ta'),
			'subtitle_en'=>array('type'=>'ta'),
			'subtitle_ge'=>array('type'=>'ta'),
			'date_of_shot'=>array('type'=>'str'),
			'place_en'=>array('type'=>'str'),
			'place_ge'=>array('type'=>'str'),
			'duration'=>array('type'=>'str'),
			'language'=>array('type'=>'str'),
			'filepath_en'=>array('type'=>'audio'),
    	    'filepath_ge'=>array('type'=>'audio'),
			'content_en'=>array('type'=>'ha'),
			'content_ge'=>array('type'=>'ha'),
			'description_keywords_en'=>array('type'=>'str'),
			'description_keywords_ge'=>array('type'=>'str'),
			'note_occasion_en'=>array('type'=>'ha'),
			'note_occasion_ge'=>array('type'=>'ha'),
			'broadcasting_title_en'=>array('type'=>'str'),
			'broadcasting_title_ge'=>array('type'=>'str'),
			'broadcasting_date'=>array('type'=>'str'),
			'broadcasting_company_en'=>array('type'=>'str'),
			'broadcasting_company_ge'=>array('type'=>'str'),
			'broadcasting_place_en'=>array('type'=>'str'),
			'broadcasting_place_ge'=>array('type'=>'str'),
    	    'source_en'=>array('type'=>'ta'),
      		'source_ge'=>array('type'=>'ta')
    	  ),
    	  'insert'=>array(
    	   'audio'=>array('type'=>'str'),
    	  ),
    	 ),
         //'field'=>'title_en,title_ge,subtitle_en,subtitle_ge,date_of_shot,place_en,place_ge,duration,language,filepath_en,filepath_ge,content_en,content_ge,description_keywords_en,description_keywords_ge,note_occasion_en,note_occasion_ge,broadcasting_title_en,broadcasting_title_ge,broadcasting_date,broadcasting_company_en,broadcasting_company_ge,broadcasting_place_en,broadcasting_place_ge',
    	 'type'=>'ta,ta,ta,ta,str,str,str,audio,str,str,str,str,str,str,str',
    	 'edit-type'=>'ta,ta,ta,ta,str,str,str,audio,ha,ha,ha,str,str,str,str',
    	 'insert-field'=>'audio',
    	 'insert-type'=>'str',
    	 'folder-name'=>'audio',
    	 'folder'=>array('filepath_en'=>'audio'),
    	 'model' => "[{ name  : ['Details'], width : [350] }]"
      ),
      /*
       *


       *
       *
       */
      'video'=>array(
               'fields'=>array(
      			 'edit'=>array(
					'title_en'=>array('type'=>'ta'),
      				'title_ge'=>array('type'=>'ta'),
					'subtitle_en'=>array('type'=>'ta'),
      				'subtitle_ge'=>array('type'=>'ta'),
					'filepath_en'=>array('type'=>'video'),
      				'filepath_ge'=>array('type'=>'video'),
					'author_en'=>array('type'=>'ta'),
      				'author_ge'=>array('type'=>'ta'),
					'director_en'=>array('type'=>'ta'),
      				'director_ge'=>array('type'=>'ta'),
					'producer_en'=>array('type'=>'ta'),
      				'producer_ge'=>array('type'=>'ta'),
					'date_of_shot'=>array('type'=>'str'),
					'year_of_shot'=>array('type'=>'str'),
					'place_of_shot_en'=>array('type'=>'str'),
					'duration'=>array('type'=>'str'),
					'bc_title_en'=>array('type'=>'ta'),
      				'bc_title_ge'=>array('type'=>'ta'),
					'bc_date'=>array('type'=>'str'),
					'bc_year'=>array('type'=>'str'),
					'bc_time'=>array('type'=>'str'),
					'bc_place_en'=>array('type'=>'ta'),
      				'bc_place_ge'=>array('type'=>'ta'),
					'bc_company_en'=>array('type'=>'ta'),
      				'bc_company_ge'=>array('type'=>'ta'),
					'language'=>array('type'=>'str'),
					'content_en'=>array('type'=>'ha'),
      				'content_ge'=>array('type'=>'ha'),
					'descr_keywords_en'=>array('type'=>'str'),
      				'descr_keywords_ge'=>array('type'=>'str'),
					'note_occasion_en'=>array('type'=>'ha'),
      				'note_occasion_ge'=>array('type'=>'ha'),
      
      			 ),
      			 'insert'=>array(
      			 	'video'=>array('type'=>'str'),
      			 ),	
      			),
               //'field'=>'video, title, subtitle, filepath, author, director, producer, date_of_shot, year_of_shot, place_of_shot, duration, bc_title, bc_date, bc_year, bc_time, bc_place, bc_company, language, content, descr_keywords, note_occasion',
               'type'=>'str,str,str,video,str,str,str,str,str,str,str,str,str,str,str,str,str,str,str,str,str',
               'edit-type'=>'str,str,ta,video,str,str,str,str,str,str,str,str,str,str,str,str,str,str,ta,ta,ta',
               'insert-field'=>'video',
               'insert-type'=>'str',
               'folder-name'=>'video',
               'folder'=>array('filepath_en'=>'video'),
      		   'model' => "[{ name  : ['Details'], width : [350] }]"
      		   ),

      'article'=>array(
               'fields'=>array(
      			 'edit'=>array(
					'title_en'=>array('type'=>'ta'),
      				'title_ge'=>array('type'=>'ta'),
					'filepath'=>array('type'=>'article'),
					'date'=>array('type'=>'str'),
      		   		'author'=>array('type'=>'ta'),
      		   		'medium'=>array('type'=>'ta'),
					'article_summary_en'=>array('type'=>'ha'),
      				'article_summary_ge'=>array('type'=>'ha'),
					'article_keywords_en'=>array('type'=>'ta'),
      				'article_keywords_ge'=>array('type'=>'ta'),
      		        'language'=>array('type'=>'str'),     		   		
      			 ),
      			 'insert'=>array(
      			 	'article_id'=>array('type'=>'str'),
      			 ),	
      			),
               'type'=>     'ta,ta,article,str,ta,ta,ha,ha,ta,ta,str',
               'edit-type'=>'ta,ta,article,str,ta,ta,ha,ha,ta,ta,str',
               'insert-field'=>'article_id',
               'insert-type'=>'str',
               'folder-name'=>'article',
               'folder'=>array('filepath'=>'article'),
      		   'model' => "[{ name  : ['Details'], width : [350] }]"
      		   ),

      'document'=>array(
               'fields'=>array(
      			 'edit'=>array(
					'title_en'=>array('type'=>'ta'),
      				'title_ge'=>array('type'=>'ta'),
					'filepath'=>array('type'=>'document'),
					'date'=>array('type'=>'str'),
					'doc_description_en'=>array('type'=>'ha'),
      				'doc_description_ge'=>array('type'=>'ha'),
      		   		'doc_source_en'=>array('type'=>'ta'),
      				'doc_source_ge'=>array('type'=>'ta'),
					'doc_keywords_en'=>array('type'=>'ta'),
      				'doc_keywords_ge'=>array('type'=>'ta'),
      			 ),
      			 'insert'=>array(
      			 	'doc_id'=>array('type'=>'str'),
      			 ),	
      			),
               'type'=>     'ta,ta,document,str,ha,ha,ta,ta,ta,ta',
               'edit-type'=>'ta,ta,document,str,ha,ha,ta,ta,ta,ta',
               'insert-field'=>'doc_id',
               'insert-type'=>'str',
               'folder-name'=>'document',
               'folder'=>array('filepath'=>'document'),
      		   'model' => "[{ name  : ['Details'], width : [350] }]"
      		   ),
      		   
      		 );
      				 
      			foreach($subGridFld as $fldName=>$fldProps){
      			  if (is_array($subGridFld[$fldName]['fields']['edit'])){
      				$subGridFld[$fldName]['field']=implode(',',array_keys($subGridFld[$fldName]['fields']['edit']));
      				foreach($subGridFld[$fldName]['fields']['edit'] as $fldN=>$fldP){
      					$fldTypes[]=$fldP['type'];
      				}
      				$subGridFld[$fldName]['type']=implode(',',$fldTypes);
      			  }
      			}	 
      			//$subGridFld['video']['field']=implode(',',array_keys($subGridFld['video']['fields']['edit']));
      			//$subGridFld['audio']['field']=implode(',',array_keys($subGridFld['audio']['fields']['edit']));	 	 

      				 foreach($shopCats as $cat=>$catProp){
      				   $subGridFld[$catProp['term_name']]=array(
				        'field'=>'image_path,descr',
				        'type'=>'img,str',
				        'http_path'=>'hw-archive.com/shop',
				        'model' => "[{ name  : ['Details'], width : [350] }]"
				        );
       				 }


 $itemView=array(
  'paint'=>array(
    'list'=>array(
      'thumb'=>array(
      		'path_small'=>'/hwdb/data/Images_oeuvre/small/',
      		'path_medium'=>'/hwdb/data/Images_oeuvre/medium/',
      		'path_original'=>'/hwdb/data/Images_oeuvre/originals/',
    			'prefix_small'=>'sm_',
    			'prefix_medium'=>'med_',
    			'prefix_original'=>'orig_',
      				 ),
      'work_number'=>'workNumber',
      'title'=>'title'.$langArr['lang_word'],
      'title_default'=>'title',
      'year'=>'year',
      'selected'=>"id,workNumber,title,title{$langArr['lang_word']},year",
      'search'=>"workNumber",
      'orderby'=>"workNumber",
      'order'=>'asc',
      'id'=>'workNumber',
      'image_id'=>'workNumber',

      ),
    'view'=>array(
      'thumb'=>array(
      		'path_small'=>'/hwdb/data/Images_oeuvre/small/',
      		'path_medium'=>'/hwdb/data/Images_oeuvre/medium/',
      		'path_original'=>'/hwdb/data/Images_oeuvre/originals/',
    			'prefix_small'=>'sm_',
    			'prefix_medium'=>'med_',
    			'prefix_original'=>'orig_',
      ),
      'item'=>array(
      	'header'=>array(
      	 'workNumber'=>'',
      	 'title'.$langArr['lang_word']=>'',
      	 'title'=>'',
      	 'finishedPlace'=>'',
      	 'year'=>'',
      	 'heightMm'=>'',
      	 'widthMm'=>'',
      	 'techniqueKeyword'=>'',
      	 'technique'=>'',
      	 'date'=>''

      	 ),
      	'main'=>array(
      	 'oneManExhibitions'=>$L->__('One man exhibitions'),
      	 'hundertwasser_comment_'.$langArr['lang_alt_short']=>$L->__('Comments'),
      	 'literatureMonographs'=>$L->__('Literature: Monographs'),
      	 'literatureExhibitionCatalogues'=>$L->__('Literature:exhibition catalogues'),
      	 'literatureVarious'=>$L->__('Literature: various'),
      	 'literatureMagazinesPeriodicals'=>$L->__('Literature: magazines, periodicals'),
      	 'reproductionsArtPrints'=>$L->__('Reproductions: art prints'),
      	 ),
      	 ),
      	 ),
      	 ),
 'jw'=>array(
    'list'=>array(
      'thumb'=>array(
      		'path_small'=>'/hwdb/data/Images_jw/small/',
      		'path_medium'=>'/hwdb/data/Images_jw/medium/',
      		'path_original'=>'/hwdb/data/Images_jw/originals/',
    			'prefix_small'=>'sm_JW_',
    			'prefix_medium'=>'med_JW_',
    			'prefix_original'=>'orig_JW_',
      	 ),
      'work_number'=>'workNumber',
      'title'=>'title'.$langArr['lang_word'],
      'title_default'=>'title',
      'year'=>'year',
      'selected'=>"id,jw,workNumber,title,title{$langArr['lang_word']},year",
      'search'=>"jw",
      'orderby'=>"jw",
      'order'=>'asc',
      'id'=>'jw',
      'image_id'=>'jw',
      	 ),

      	 ),

   'hwg'=>array(
    'list'=>array(
      'thumb'=>array(
      		'path_small'=>'/hwdb/data/Images_hwg/small/',
      		'path_medium'=>'/hwdb/data/Images_hwg/medium/',
      		'path_original'=>'/hwdb/data/Images_hwg/originals/',
    			'prefix_small'=>'',
    			'prefix_medium'=>'',
    			'prefix_original'=>'',
      	 ),
      'work_number'=>'workNumber',
      'title'=>'title'.$langArr['lang_word'],
      'title_default'=>'title',
      'year'=>'year',
      'selected'=>"id,hwg,workNumber,title,title{$langArr['lang_word']},year,descriptionPortfolio",
      'search'=>"hwg",
      'orderby'=>"hwg",
      'order'=>'asc',
      'id'=>'hwg',
      'image_id'=>'workNumber',
      	 ),
),
  'tap'=>array(
    'list'=>array(
      'thumb'=>array(
      		'path_small'=>'/hwdb/data/Images_tap/small/',
      		'path_medium'=>'/hwdb/data/Images_tap/medium/',
      		'path_original'=>'/hwdb/data/Images_tap/originals/',
    			'prefix_small'=>'sm_',
    			'prefix_medium'=>'med_',
    			'prefix_original'=>'orig_',
      	 ),
      'work_number'=>'workNumber',
      'title'=>'title'.$langArr['lang_word'],
      'title_default'=>'title',
      'year'=>'year',
      'selected'=>"id,tap,workNumber,title,title{$langArr['lang_word']},year",
      'search'=>"tap",
      'orderby'=>"tap",
      'order'=>'asc',
      'id'=>'tap',
      'image_id'=>'workNumber',
      	 ),
      	 ),

   'apa'=>array(
    'list'=>array(
      'thumb'=>array(
      		'path_small'=>'/hwdb/data/Images_apa/small/',
      		'path_medium'=>'/hwdb/data/Images_apa/medium/',
      		'path_original'=>'/hwdb/data/Images_apa/originals/',
    			'prefix_small'=>'sm_APA_',
    			'prefix_medium'=>'med_APA_',
    			'prefix_original'=>'orig_APA_',
      	 ),
      'work_number'=>'workNumber',
      'title'=>'title'.$langArr['lang_word'],
      'title_default'=>'title',
      'year'=>'year',
      'selected'=>"id,apa,workNumber,title,title{$langArr['lang_word']},year,descriptionCategoryKeyword",
      'search'=>"apa",
      'orderby'=>"apa",
      'order'=>'asc',
      'id'=>'apa',
      'image_id'=>'apa',
      	 ),
      	 ),

   'arch'=>array(
    'list'=>array(
      'thumb'=>array(
      		'path_small'=>'/hwdb/data/Images_arch/small/',
      		'path_medium'=>'/hwdb/data/Images_arch/medium/',
      		'path_original'=>'/hwdb/data/Images_arch/originals/',
    			'prefix_small'=>'sm_ARCH_',
    			'prefix_medium'=>'med_ARCH_',
    			'prefix_original'=>'orig_ARCH_',
      	 ),
      'work_number'=>'workNumber',
      'title'=>'title'.$langArr['lang_word'],
      'title_default'=>'title',
      'year'=>'year',
      'selected'=>"id,arch,workNumber,title,title{$langArr['lang_word']},year",
      'search'=>"arch",
      'orderby'=>"arch",
      'order'=>'asc',
      'id'=>'arch',
      'image_id'=>'arch',
      	 ),
      	 ),

  'text'=>array(
    'list'=>array(
      'work_number'=>'',
      'title'=>'text_title_'.$langArr['lang_alt_short'],
      'title_default'=>'text_title_en',
      'year'=>'year',
      'selected'=>"id,txt,text_title_en,text_title_{$langArr['lang_alt_short']},year,text_date",
      'search'=>"txt",
      'orderby'=>"text_date",
      'order'=>'asc',
      'id'=>'txt',
      	 ),

      	 ),

  'audio'=>array(
    'list'=>array(
      'work_number'=>'',
      'title'=>'title_'.$langArr['lang_alt_short'],
      'title_default'=>'title_en',
      'year'=>'date_of_shot',
      'selected'=>"id,audio,filepath,title_en,title_{$langArr['lang_alt_short']},date_of_shot",
      'search'=>"audio",
      'orderby'=>"id",
      'order'=>'asc',
      'id'=>'audio',
      'filepath'=>'filepath_en'

      	 ),

      	 ),

   'video'=>array(
    'list'=>array(
      'work_number'=>'',
      'title'=>'title_en',
      'title_default'=>'title_en',
      'year'=>'year_of_shot',
      'selected'=>"id,video,filepath_en,title_en,year_of_shot",
      'search'=>"video",
      'orderby'=>"id",
      'order'=>'asc',
      'id'=>'video',
      'filepath'=>'filepath_en'
      	 ),

      	 ),


   'gallery'=>array(
    'list'=>array(
      'thumb'=>array(
      		'path_small'=>'/hwdb/thumbs/galery/',
      		'path_medium'=>'/hwdb/thumbs/galery/',
      		'path_original'=>'/hwdb/thumbs/galery/',
    			'prefix_small'=>'thumb.',
    			'prefix_medium'=>'',
    			'prefix_original'=>'',
      	 ),
      'work_number'=>'',
      'title'=>'gallery_title_'.$langArr['lang_alt_short'],
      'title_default'=>'gallery_title_en',
      'year'=>'gallery_year',
      'selected'=>"id,image_path,gallery,gallery_title_en,gallery_title_{$langArr['lang_alt_short']},gallery_year",
      'search'=>"gallery",
      'orderby'=>"id",
      'order'=>'asc',
      'id'=>'gallery',
      'filepath'=>'image_path',
      	 ),
      	 ),
      	 
   'picture'=>array(
    'list'=>array(
      'thumb'=>array(
      		'path_small'=>'/hwdb/thumbs/picture/',
      		'path_medium'=>'/hwdb/thumbs/picture/',
      		'path_original'=>'/hwdb/thumbs/picture/',
    		'prefix_small'=>'thumb.',
    		'prefix_medium'=>'',
    		'prefix_original'=>'',
      	 ),
      'work_number'=>'',
      'title'=>'picture_title_'.$langArr['lang_alt_short'],
      'title_default'=>'picture_title_en',
      'year'=>'picture_year',
      'selected'=>"id,image_path,picture_id,picture_title_en,picture_title_{$langArr['lang_alt_short']},picture_year",
      'search'=>"picture_id",
      'orderby'=>"id",
      'order'=>'asc',
      'id'=>'picture_id',
      'filepath'=>'image_path',
      	 ),
      	 ),
      	 
    'article'=>array(
      'list'=>array(
        'work_number'=>'',
        'title'=>'title_en',
        'title_default'=>'title_en',
        'date'=>'date',
        'selected'=>"id,article_id,filepath,title_en,date",
        'search'=>"article_id",
        'orderby'=>"id",
        'order'=>'asc',
        'id'=>'article_id',
        'filepath'=>'filepath'
      	 ),
     ),

    'document'=>array(
      'list'=>array(
        'work_number'=>'',
        'title'=>'title_en',
        'title_default'=>'title_en',
        'date'=>'date',
        'selected'=>"id,doc_id,filepath,title_en,date,doc_source_en",
        'search'=>"doc_id",
        'orderby'=>"id",
        'order'=>'asc',
        'id'=>'doc_id',
        'filepath'=>'filepath'
      	 ),
     ),
    );

      	 require_once('searchConfig.php');

      	 $itemType='text';
      	 $itemProp[$itemType]['name']='text';
      	 $itemProp[$itemType]['link']='related_text';
      	 $itemProp[$itemType]['type']='text';
      	 $itemProp[$itemType]['title']='Text';


      	 //include jw
      	 $itemType='paint';
      	 $itemProp[$itemType]['name']='paintings';
      	 $itemProp[$itemType]['link']='related_paint';
      	 $itemProp[$itemType]['type']='paint';
      	 $itemProp[$itemType]['title']='Paintings';


      	 $itemType='jw';
      	 $itemProp[$itemType]['name']='early_works';
      	 $itemProp[$itemType]['link']='related_jw';
      	 $itemProp[$itemType]['type']='jw';
      	 $itemProp[$itemType]['title']='Early works';



      	 $itemType='hwg';
      	 $itemProp[$itemType]['name']='graphic';
      	 $itemProp[$itemType]['link']='related_hwg';
      	 $itemProp[$itemType]['type']='hwg';
      	 $itemProp[$itemType]['title']='Original Graphic';
      	 

      	 $itemType='tap';
      	 $itemProp[$itemType]['name']='tapestries';
      	 $itemProp[$itemType]['link']='related_tap';
      	 $itemProp[$itemType]['type']='tap';
      	 $itemProp[$itemType]['title']='Tapestries';


      	 $itemType='apa';
      	 $itemProp[$itemType]['name']='apa';
      	 $itemProp[$itemType]['link']='related_apa';
      	 $itemProp[$itemType]['type']='apa';
      	 $itemProp[$itemType]['title']='Applied Art';


      	 $itemType='arch';
      	 $itemProp[$itemType]['name']='architecture';
      	 $itemProp[$itemType]['link']='related_arch';
      	 $itemProp[$itemType]['type']='arch';
      	 $itemProp[$itemType]['title']='Architecture';


      	 $itemType='video';
      	 $itemProp[$itemType]['name']='video';
      	 $itemProp[$itemType]['link']='related_video';
      	 $itemProp[$itemType]['type']='video';
      	 $itemProp[$itemType]['title']='Video';


      	 $itemType='audio';
      	 $itemProp[$itemType]['name']='audio';
      	 $itemProp[$itemType]['link']='related_audio';
      	 $itemProp[$itemType]['type']='audio';
      	 $itemProp[$itemType]['title']='Audio';


      	 $itemType='gallery';
      	 $itemProp[$itemType]['name']='galleries';
      	 $itemProp[$itemType]['link']='related_gallery';
      	 $itemProp[$itemType]['type']='gallery';
      	 $itemProp[$itemType]['title']='Galleries';

      	 $itemType='picture';
      	 $itemProp[$itemType]['name']='pictures';
      	 $itemProp[$itemType]['link']='related_picture';
      	 $itemProp[$itemType]['type']='picture';
      	 $itemProp[$itemType]['title']='Pictures';
      	 
      	 $itemType='biography';
      	 $itemProp[$itemType]['name']='biography';
      	 $itemProp[$itemType]['link']='related_biography';
      	 $itemProp[$itemType]['type']='biography';
      	 $itemProp[$itemType]['title']='Biography';

      	 $itemType='one_exhibition';
      	 $itemProp[$itemType]['name']='one_exhibition';
      	 $itemProp[$itemType]['link']='related_one_exhibition';
      	 $itemProp[$itemType]['type']='one_exhibition';
      	 $itemProp[$itemType]['title']='One-man exhibitions';

      	 $itemType='group_exhibition';
      	 $itemProp[$itemType]['name']='group_exhibition';
      	 $itemProp[$itemType]['link']='related_group_exhibition';
      	 $itemProp[$itemType]['type']='group_exhibition';
      	 $itemProp[$itemType]['title']='Group exhibitions';
      	       	 
      	 $itemType='monograph';
      	 $itemProp[$itemType]['name']='monograph';
      	 $itemProp[$itemType]['link']='related_monograph';
      	 $itemProp[$itemType]['type']='monograph';
      	 $itemProp[$itemType]['title']='Monographs';   

      	 $itemType='catalog_exhibition';
      	 $itemProp[$itemType]['name']='catalog_exhibition';
      	 $itemProp[$itemType]['link']='related_catalog_exhibition';
      	 $itemProp[$itemType]['type']='catalog_exhibition';
      	 $itemProp[$itemType]['title']='Exhibition Catalogues'; 

      	 $itemType='article';
      	 $itemProp[$itemType]['name']='article';
      	 $itemProp[$itemType]['link']='related_article';
      	 $itemProp[$itemType]['type']='article';
      	 $itemProp[$itemType]['title']='Press clippings';      	 
      	 
      	 $itemType='document';
      	 $itemProp[$itemType]['name']='document';
      	 $itemProp[$itemType]['link']='related_document';
      	 $itemProp[$itemType]['type']='document';
      	 $itemProp[$itemType]['title']='Document';      	 
      	 
      	 
      	 Zend_Registry::set ( 'fields', $fields);
      	 Zend_Registry::set ( 'itemTitles', $itemTitles );
      	 Zend_Registry::set ( 'itemWorkNumber', $itemWorkNumber );
      	 Zend_Registry::set ( 'itemIds', $itemIds );

      	 $dataConfig['skinIcons']=$skinIcons;
      	 $dataConfig['skinNames']=$skinNames;
      	 $dataConfig['shopCats']=$shopCats;
      	 $dataConfig['relatedItems']=$relatedItems;
      	 $dataConfig['itemView']=$itemView;
      	 $dataConfig['searchFields']=$searchFields;
      	 $dataConfig['itemProp']=$itemProp;
      	 $dataConfig['itemWorkNumber']=$itemWorkNumber;

      	 $dataConfig['objectCategory']=$objectCategory;
      	 $dataConfig['itemTitles']=$itemTitles;
      	 $dataConfig['itemIds']=$itemIds;
      	 $dataConfig['searchIgnore']=$searchIgnore;

      	 Zend_registry::set ('dataConfig',$dataConfig);



      	 Zend_Registry::set ( 'subGridFld', $subGridFld );

