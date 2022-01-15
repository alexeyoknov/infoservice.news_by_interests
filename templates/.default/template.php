<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<p><b><?=GetMessage("NEWS_ELS")?></b></p>
<?
foreach($arResult["NEWS_BY_AUTHOR"] as $a => $b){?>
	<ul>
		<li> <ul>
			<?=$arResult["USERS"][$a]["LOGIN"]?>
			<?
				foreach($b as $news){?>
					<li>"<?=$arResult["NEWS"][$news]["NAME"]?>"</li>
				<?};
			?>
			
		</ul></li>
	</ul>
<?}?>


