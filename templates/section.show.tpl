{include file="admin.sec.hea.tpl"}
{if $sectionImg}
<img class="secimg" width="180" src="?act=show,files,{$sectionImg.id}" alt="{$sectionImg.description}">
{/if}
{$section.text}
{if $articleList}
<h2>Články</h2>
<div class="article">
<table>
{section name=articlePos loop=$articleList}
<tr>
  <td valign="top"><img src="images/article.gif" width="16" height="16" alt="#"></td>
  <td>
  <p class="atitle"
    ><a href="?act=show,article,{$articleList[articlePos].id}"
	>{$articleList[articlePos].title}</a
	>{if $articleList[articlePos].protect}&nbsp;<img
	 src="images/key.gif" width="10" height="12" alt="přístup po registraci"
	>{/if}{include file="admin.sec.art.tpl"}
  <p class="aabstract">{$articleList[articlePos].abstract}
  </td>
</tr>
{/section}
</table>
</div>
{/if}
{if $sectionFileList}
<h2>Soubory</h2>
<div class="file">
<table>
{section name=filePos loop=$sectionFileList}
<tr>
  <td valign="top"><i class="fa {$sectionFileList[filePos].fa_icon}"></i>&nbsp;</td>
  <td>
  <p class="atitle"
    ><a href="?act=show,file,{$sectionFileList[filePos].id}"
	>{$sectionFileList[filePos].origfname}</a>{include file="admin.sec.fil.tpl"}</p>
  <p class="aabstract">{$sectionFileList[filePos].description}
  </td>
</tr>
{/section}
</table>
</div>
{/if}
