<h1>{if $adminMode}<span class="editimg"
  ><a href="ctrl.php?act=edit,section,{$section.Id}"
  ><img src="images/edit.gif" alt="[edit]" width="16" height="16"></a></span
  >{/if}{$section.title}</h1>
{$section.text}
<table border="0">
{section name=articlePos loop=$articleList}
<tr>
{if $articleList[articlePos].imgId}
<td valign="top"><img src="ctrl.php?act=show,files,{$articleList[articlePos].imgId}" alt="{$articleList[articlePos].imgAlt}"></td>
{else}
<td valign="top"><img src="images/e75.gif" alt="[placeholder]"></td>
{/if}
<td valign="top">
<p class="otitle">{if $adminMode}<span class="editimg"
  ><a href="ctrl.php?act=edit,article,{$articleList[articlePos].Id}"
  ><img src="images/edit.gif" alt="[edit]" width="16" height="16"></a></span
  >{/if}{$articleList[articlePos].title}
{if $articleList[articlePos].author}<p class="oauthor">{$articleList[articlePos].author}{/if}
{if $articleList[articlePos].text}<div class="otext">{$articleList[articlePos].text}</div>{/if}
{if $articleList[articlePos].files}
<!-- Files attached to this item -->
<table>
{section name=articleFile loop=$articleList[articlePos].files}
<tr>
  <td valign="top"><img src="images/{$articleList[articlePos].files[articleFile].icon}.gif" width="16" height="16" alt="[{$articleList[articlePos].files[articleFile].icon} file]"></td>
  <td>
  <p class="atitle"
    ><a href="ctrl.php?act=show,file,{$fileList[filePos].Id}"
	>{$articleList[articlePos].files[articleFile].origname}</a>
  <p class="aabstract">{$articleList[articlePos].files[articleFile].description}
  </td>
</tr>
{/section}
</table>
{/if}
<p class="oprice">Cena:
{if $articleList[articlePos].price <= 0}
na dotaz
{else}
{$articleList[articlePos].price}&nbsp;{$articleList[articlePos].currency}
<form action="ctrl.php?act=show,order,42" method="post">
<input type="submit" value="Objednávka">
</form>
{/if}
</td>
</tr>
{/section}
</table>
