<table class="admintable" border="0" cellpadding="2" cellspacing="1">
<tr class="newobject">
<td colspan="2">Přidat poznámku</td>
<td width="32" class="smaller" 
  ><a href="?act=edit,note,0&object_id={$lecture.id}"><img src="images/add.gif" alt="[přidat]" title="přidat poznámku" width="16" height="16"></a></td>
</tr>
{if $noteList}
<tr>
<th align="left">Text</th>
<th>Datum</th>
<th>&nbsp;</th>
</tr>
{section name=nId loop=$noteList}
{if $smarty.section.nId.iteration is even}
<tr class="rowA">
{else}
<tr class="rowB">
{/if}
<td>{$noteList[nId].text}</td>
<td class="smaller">{$noteList[nId].date|date_format:"%d.%m.%Y"}</td>
<td width="32" class="smaller" valign="middle"
  ><a href="?act=edit,note,{$noteList[nId].id}"><img src="images/edit.gif" alt="[edit]" title="změnit" width="16" height="16"></a
  ><a href="?act=delete,note,{$noteList[nId].id}"><img src="images/delete.gif" alt="[delete]" title="smazat" width="16" height="16"></a></td>
</tr>
{/section}
{/if}
</table>
