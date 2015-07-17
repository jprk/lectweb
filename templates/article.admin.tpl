<h1>Seznam uložených èlánkù</h1>
<table class="admintable" border="0" cellpadding="2" cellspacing="1">
<tr class="newobject">
<td colspan="2">&nbsp;Pøidat èlánek</td>
<td width="40" class="smaller" 
  ><a href="ctrl.php?act=edit,article,0"><img src="images/add.gif" alt="[add]" width="16" height="16"></a></td>
</tr>
{section name=articlePos loop=$articleList}
<tr class="titlerow">
<td colspan="2">{$articleList[articlePos].sname}</td>
<td>&nbsp;</td>
</tr>
{section name=aId loop=$articleList[articlePos].articles}
{if $smarty.section.aId.iteration is even}
<tr class="rowA">
{else}
<tr class="rowB">
{/if}
<td width="16">&nbsp;</td>
<td>{$articleList[articlePos].articles[aId].title}</td>
<td width="40" class="smaller" valign="middle"
  ><a href="?act=edit,article,{$articleList[articlePos].articles[aId].id}"><img src="images/edit.gif" alt="[edit]" width="16" height="16"></a
  ><a href="?act=delete,article,{$articleList[articlePos].articles[aId].id}"><img src="images/delete.gif" alt="[delete]" width="16" height="16"></a></td>
</tr>
{/section}
{/section}
</table>
