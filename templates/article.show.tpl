<h1>{if $adminMode}<span class="editimg"
  ><a href="ctrl.php?act=edit,article,{$article.Id}"
  ><img src="images/edit.gif" alt="[edit]" width="16" height="16"></a></span
  >{/if}{$article.title}</h1>
{$article.text}
{if $fileList}
<h2>Soubory</h2>
<div class="file">
<table>
{section name=filePos loop=$fileList}
<tr>
  <td valign="top"><img src="images/{$fileList[filePos].icon}.gif" width="16" height="16" alt="[{$fileList[filePos].icon} file]"></td>
  <td>
  <p class="atitle"
    ><a href="ctrl.php?act=show,files,{$fileList[filePos].Id}"
	>{$fileList[filePos].origname|escape:"html"}</a>
  <p class="aabstract">{$fileList[filePos].description}
  </td>
</tr>
{/section}
</table>
</div>
{/if}
