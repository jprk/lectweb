<h1>Seznam studentských projektù</h1>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
<tr>
<td>Nový projekt ...</td>
<td width="40" class="smaller" 
  ><a href="ctrl.php?act=edit,project,0"><img src="images/add.gif" alt="[add]" width="16" height="16"></a></td>
</tr>
{section name=aId loop=$prjlist}
{if $smarty.section.aId.iteration is even}
<tr class="rowA">
{else}
<tr class="rowB">
{/if}
<td>{$prjlist[aId].mtitle} ({$prjlist[aId].title})</td>
<td width="40" class="smaller" valign="middle"
  ><a href="ctrl.php?act=edit,project,{$prjlist[aId].id}"><img src="images/edit.gif" alt="[edit]" width="16" height="16"></a
  ><a href="ctrl.php?act=delete,project,{$prjlist[aId].id}"><img src="images/delete.gif" alt="[delete]" width="16" height="16"></a></td>
</tr>
{/section}
</table>
