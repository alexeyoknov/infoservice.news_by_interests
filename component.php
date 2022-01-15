<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
use Bitrix\Main\Loader;


if (!$USER->IsAuthorized())
{
	ShowError (GetMessage("NO_AUTORIZATION"));
	return;
}

$arParams["NEWS_IBLOCK_ID"] = intval(trim($arParams["NEWS_IBLOCK_ID"]));
if(!($arParams["NEWS_IBLOCK_ID"] > 0))
{
	ShowError (GetMessage("NO_IBLOCK"));
	return;
}

$arParams["PROP_CODE_AUTHOR"] = trim($arParams["PROP_CODE_AUTHOR"]);
if( strlen($arParams["PROP_CODE_AUTHOR"]) < 1)
{
	ShowError (GetMessage("NO_PROP_CODE_AUTHOR"));
	return;
}

$arParams["PROP_CODE_AUTHOR_TYPE"] = trim($arParams["PROP_CODE_AUTHOR_TYPE"]);
if( strlen($arParams["PROP_CODE_AUTHOR_TYPE"]) < 1)
{
	ShowError (GetMessage("NO_PROP_CODE_AUTHOR_TYPE"));
	return;
}

if ($this->startResultCache($USER->GetLogin() . $USER->GetID(), false)) 
{
	if (!Loader::includeModule("iblock")) 
	{
		$this->abortResultCache();
		ShowError(GetMessage("IBLOCK_MODULE_NOT_INSTALLED"));
		return;
	}

	// user
	$arOrderUser = array("id");
	$sortOrder = "asc";
	$arFilterUser = array(
		"ACTIVE" => "Y"
	);

    $arUserParams = [
        'SELECT' => ['ID','LOGIN',$arParams["PROP_CODE_AUTHOR_TYPE"]]
    ];
	
	$arResult["USERS"] = array();

	$allUsers = CUser::GetList($arOrderUser, $sortOrder, $arFilterUser, $arUserParams);
	while($arUser = $allUsers->GetNext())
	{
		$arResult['USERS'][$arUser["ID"]] = [
            'LOGIN' => $arUser["LOGIN"],
            $arParams["PROP_CODE_AUTHOR_TYPE"] => $arUser[$arParams["PROP_CODE_AUTHOR_TYPE"]]
        ];
	}	

	$arSelectElems = array (
		"ID",
		"IBLOCK_ID",
		"NAME",
		"DETAIL_PAGE_URL",
		"PROPERTY_".$arParams["PROP_CODE_AUTHOR"]
	);

	$arFilterElems = array (
		"IBLOCK_ID" => $arParams["IBLOCK_ID"],
		"ACTIVE" => "Y",
	);

	$arSortElems = array (
		//"NAME" => "ASC"
	);

	$arResult["NEWS"] = array();

	$rsElementElement = CIBlockElement::GetList($arSortElems, $arFilterElems, false, false, $arSelectElems);
    $arNewsAuthor = "";
	while($arElement = $rsElementElement->GetNext())
	{	
        $arNewsAuthor = $arElement["PROPERTY_".$arParams["PROP_CODE_AUTHOR"]."_VALUE"];
		if (!empty($arNewsAuthor))
		{
            
			if ($arElement["PROPERTY_".$arParams["PROP_CODE_AUTHOR"]."_VALUE"] == $USER->GetID()) {
				$arResult["NEWS_ID_CURRENT"][] = $arElement["ID"];
			}else{
				$arResult["NEWS_ID"][$arElement["ID"]][$arNewsAuthor] = $arResult['USERS'][$arNewsAuthor];


                if ($arResult['USERS'][$arNewsAuthor][$arParams["PROP_CODE_AUTHOR_TYPE"]] == $arResult['USERS'][$USER->GetID()][$arParams["PROP_CODE_AUTHOR_TYPE"]]) {
                    $arResult["NEWS_BY_AUTHOR"][$arNewsAuthor][] = $arElement["ID"];
					$arResult["NEWS_ID_OTHER"][] = $arElement["ID"];
				};
				
            }
            $arResult["NEWS"][$arElement["ID"]] = $arElement;
		}
	}

	$arResult["NEWS_ID_CURRENT"] = array_unique($arResult["NEWS_ID_CURRENT"]);
	$arResult["NEWS_ID_OTHER"] = array_unique($arResult["NEWS_ID_OTHER"]); 

	foreach ($arResult["NEWS_ID_CURRENT"] as $key => $value) {
		if (in_array($value, $arResult["NEWS_ID_OTHER"])) {

			$unsetId = array_keys ($arResult["NEWS_ID"][$value])[0];
			foreach ($arResult["NEWS_BY_AUTHOR"][$unsetId] as $k => $v) {
				if ($v == $value){
					unset($arResult["NEWS_BY_AUTHOR"][$unsetId][$k]);
				};
			};

		}
	}

	foreach($arResult["NEWS_ID_OTHER"] as $k=>$v)
		if (in_array($v, $arResult["NEWS_ID_CURRENT"]))
			unset($arResult["NEWS_ID_OTHER"][$k]);

	$arResult["UNIQ_NEWS_COUNT"] = count($arResult["NEWS_ID_OTHER"]);

	//echo "<pre>"; print_r( $arResult ); echo "</pre>";

	$this->SetResultCacheKeys(array(
		"UNIQ_NEWS_COUNT",
	));
	
	$this->includeComponentTemplate();
} 
else 
{
	$this->abortResultCache();
}
$APPLICATION->SetTitle("Новостей [".$arResult["UNIQ_NEWS_COUNT"]."]");
?>