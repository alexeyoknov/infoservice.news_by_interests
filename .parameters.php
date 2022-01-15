<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

 $arComponentParameters = array(
    "GROUPS" => array(),
    "PARAMETERS" => array(
		"NEWS_IBLOCK_ID" => array(
			"NAME" => GetMessage("NEWS_IBLOCK_ID"),
			"TYPE" => "STRING",
		),
		
		"PROP_CODE_AUTHOR" => array(
			"NAME" => GetMessage("PROP_CODE_AUTHOR"),
			"TYPE" => "STRING",
		),

		"PROP_CODE_AUTHOR_TYPE" => array(
			"NAME" => GetMessage("PROP_CODE_AUTHOR_TYPE"),
			"TYPE" => "STRING",
		),

		"CACHE_TIME"  =>  Array("DEFAULT"=>300),
    ),
);
?>