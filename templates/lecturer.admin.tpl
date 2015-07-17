<p>
<table class="admintable" border="0" cellpadding="2" cellspacing="1">
<tr class="newobject">
<td colspan="3">Přidat dalšího učitele</td>
<td width="40" class="smaller" valign="middle"
  ><a href="?act=edit,lecturer,0"><img src="images/add.gif" title="nový učitel" title="nový učitel" alt="[nový učitel]" width="16" height="16"></a></td>
</tr>
<tr>
<th style="text-align: left;">Jméno</th>
<th style="text-align: left;">Místnost</th>
<th style="text-align: left;">E-mail</th>
<th>&nbsp;</th>
</tr>
{section name=aId loop=$lecturerList}
{if $smarty.section.aId.iteration is even}
<tr class="rowA">
{else}
<tr class="rowB">
{/if}
<td>{$lecturerList[aId].firstname} {$lecturerList[aId].surname}</td>
<td>{$lecturerList[aId].room}</td>
<td>{$lecturerList[aId].email}</td>
<td width="40" class="smaller" valign="middle"
  ><a href="?act=edit,lecturer,{$lecturerList[aId].id}"><img src="images/edit.gif" title="změnit" alt="[edit]" width="16" height="16"></a
  ><a href="?act=delete,lecturer,{$lecturerList[aId].id}"><img src="images/delete.gif" title="smazat" alt="[smazat]" width="16" height="16"></a></td>
</tr>
{/section}
</table>
</p>
