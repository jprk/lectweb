<table class="admintable" border="0" cellpadding="2" cellspacing="1">
<tr class="newobject">
<td colspan="3">&nbsp;Přidat uživatele</td>
<td width="32" class="smaller" valign="middle"
  ><a href="?act=edit,user,0"><img src="images/add.gif" title="přidat" alt="[nový uživatel]" width="16" height="16"></a></td>
</tr>
{if $userList}
<tr class="adminlisthead">
<th style="width: 72%; text-align: left;">&nbsp;Jméno</th>
<th style="width: 8ex;">Login</th>
<th style="width: 8ex;">Role</th>
<th style="width: 32px;">&nbsp;</th>
</tr>
{section name=aId loop=$userList}
{if $smarty.section.aId.iteration is even}
<tr class="rowA">
{else}
<tr class="rowB">
{/if}
<td>&nbsp;{$userList[aId].surname}{if $userList[aId].firstname}, {$userList[aId].firstname}{/if}</td>
<td align="center">{$userList[aId].login}</td>
<td align="center">{$userList[aId].roleName}</td>
<td width="32" class="smaller" valign="middle"
  ><a href="ctrl.php?act=edit,user,{$userList[aId].id}"><img src="images/edit.gif"     title="změnit" alt="[edit]"   width="16" height="16"></a
  ><a href="ctrl.php?act=delete,user,{$userList[aId].id}"><img src="images/delete.gif" title="smazat" alt="[delete]" width="16" height="16"></a></td>
</tr>
{/section}
{/if}
</table>
