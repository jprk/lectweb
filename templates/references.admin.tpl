<h1>Seznam uložených obrázkù referencí</h1>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
<tr>
<td>Pøidat referenci</td>
<td width="40" class="smaller" 
  ><a href="ctrl.php?act=edit,references,0"><img src="images/add.gif" alt="[add]" width="16" height="16"></a></td>
</tr>
{section name=refPos loop=$refList}
{if $smarty.section.refPos.iteration is even}
<tr class="rowA">
{else}
<tr class="rowB">
{/if}
<td>{$refList[refPos].description}</td>
<td width="40" class="smaller" valign="middle"
  ><a href="ctrl.php?act=edit,references,{$refList[refPos].Id}"><img src="images/edit.gif" alt="[edit]" width="16" height="16"></a
  ><a href="ctrl.php?act=delete,references,{$refList[refPos].Id}"><img src="images/delete.gif" alt="[delete]" width="16" height="16"></a></td>
</tr>
{/section}
</table>
