{if $allDataFilesList}
<div class="file">
<table>
{section name=filePos loop=$allDataFilesList}
<tr>
  <td valign="top"><img src="images/{$allDataFilesList[filePos].icon}.gif" width="16" height="16" alt="[{$allDataFilesList[filePos].icon} file]"></td>
  <td>
  <p class="atitle"
    ><a href="?act=show,file,{$allDataFilesList[filePos].id}"
	>{$allDataFilesList[filePos].origfname}</a></p>
  <p class="aabstract">{$allDataFilesList[filePos].description}
  </td>
</tr>
{/section}
</table>
</div>
{else}
<p>
Listujeme, ale seznam souborù ke stažení je v tento okamžik prázdný.
</p>
{/if}
