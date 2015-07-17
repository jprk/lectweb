<h1>Seznam osob</h1>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
<tr>
<td>Pøidat osobu</td>
<td width="40" class="smaller" 
  ><a href="ctrl.php?act=edit,people,0"><img src="images/add.gif" alt="[add]" width="16" height="16"></a></td>
</tr>
{section name=aId loop=$people}
{if $smarty.section.aId.iteration is even}
<tr class="rowA">
{else}
<tr class="rowB">
{/if}
<td>&nbsp;{$people[aId].surname}, {$people[aId].name}</td>
<td width="40" class="smaller" valign="middle"
  ><a href="ctrl.php?act=edit,people,{$people[aId].id}"><img src="images/edit.gif" alt="[edit]" width="16" height="16"></a
  ><a href="ctrl.php?act=delete,people,{$people[aId].id}"><img src="images/delete.gif" alt="[delete]" width="16" height="16"></a></td>
</tr>
{/section}
</table>
