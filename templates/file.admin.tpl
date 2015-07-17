<table class="admintable" border="0" cellpadding="2" cellspacing="1">
<tr class="newobject">
<td colspan="3">Přidat soubor</td>
<td width="32" class="smaller" 
  ><a href="?act=edit,file,0"><img src="images/add.gif" alt="[add]" width="16" height="16"></a></td>
</tr>
{section name=filePos loop=$fileList}
{if $fileList[filePos].sfiles}
<tr class="titlerow">
<td colspan="3">{$fileList[filePos].sname}</td>
<td>&nbsp;</td>
</tr>
{section name=sfId loop=$fileList[filePos].sfiles}
{if $smarty.section.sfId.iteration is even}
<tr class="rowA">
{else}
<tr class="rowB">
{/if}
<td width="16">&nbsp;</td>
<td>{$fileList[filePos].sfiles[sfId].origfname}</td>
<td><div style="overflow:hidden; white-space:nowrap; text-overflow:ellipsis; width:300px;">{$fileList[filePos].sfiles[sfId].description}</div></td>
<td width="32" class="smaller" valign="middle"
  ><a href="?act=edit,file,{$fileList[filePos].sfiles[sfId].id}"><img src="images/edit.gif" alt="[edit]" width="16" height="16"></a
  ><a href="?act=delete,file,{$fileList[filePos].sfiles[sfId].id}"><img src="images/delete.gif" alt="[delete]" width="16" height="16"></a></td>
</tr>
{/section}
{/if}
{section name=aId loop=$fileList[filePos].afiles}
<tr class="titlerow">
<td colspan="3">{$fileList[filePos].sname}&nbsp;/&nbsp;<em title="článek">{$fileList[filePos].afiles[aId].article}</em></td>
<td>&nbsp;</td>
</tr>
{section name=afId loop=$fileList[filePos].afiles[aId].files}
{if $smarty.section.afId.iteration is even}
<tr class="rowA">
{else}
<tr class="rowB">
{/if}
<td width="16">&nbsp;</td>
<td>{$fileList[filePos].afiles[aId].files[afId].origfname}</td>
<td>{$fileList[filePos].afiles[aId].files[afId].description}</td>
<td width="32" class="smaller" valign="middle"
  ><a href="?act=edit,file,{$fileList[filePos].afiles[aId].files[afId].id}"><img src="images/edit.gif" alt="[edit]" width="16" height="16"></a
  ><a href="?act=delete,file,{$fileList[filePos].afiles[aId].files[afId].id}"><img src="images/delete.gif" alt="[delete]" width="16" height="16"></a></td>
</tr>
{/section}
{/section}
{/section}
</table>
