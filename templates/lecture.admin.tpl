<table class="admintable" border="0" cellpadding="2" cellspacing="1">
<tr class="newobject">
<td colspan="2">&nbsp;Přidat předmět</td>
<td width="48" class="smaller" valign="middle"
  ><a href="?act=edit,lecture,0"><img src="images/famfamfam/add.png" title="nový předmět" alt="[nový předmět]" width="16" height="16"></a
  ></td>
</tr>
{if $lectureList}
<tr class="adminlisthead">
<th style="width: 6ex;">Kód</th>
<th style="width: 80%; text-align: left;">&nbsp;Název</th>
<th style="width: 32px;">&nbsp;</th>
</tr>
{section name=aId loop=$lectureList}
{if $smarty.section.aId.iteration is even}
<tr class="rowA">
{else}
<tr class="rowB">
{/if}
<td align="center">&nbsp;{$lectureList[aId].code}&nbsp;</td>
<td>&nbsp;{$lectureList[aId].title}</td>
<td width="48" class="smaller" valign="middle"
  ><a href="?act=edit,lecture,{$lectureList[aId].id}"><img src="images/famfamfam/application_edit.png" title="editovat" alt="[editovat]" width="16" height="16"></a
  ><a href="?act=delete,lecture,{$lectureList[aId].id}"><img src="images/famfamfam/delete.png" title="smazat" alt="[smazat]" width="16" height="16"></a
  ><a href="?act=admin,lecture,{$lectureList[aId].id}"><img src="images/famfamfam/application_go.png" title="přepnout" alt="[přepnout]" width="16" height="16"></a
  ></td>
</tr>
{/section}
{/if}
</table>
