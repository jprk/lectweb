<table class="admintable" border="0" cellpadding="2" cellspacing="1">
<tr class="newobject">
<td colspan="2">Přidat další vyhodnocení</td>
<td width="50" class="smaller" valign="middle"
  ><a href="?act=edit,evaluation,0"><img src="images/add.gif" alt="[nové vyhodnocení]" width="16" height="16"></a></td>
</tr>
{section name=aId loop=$evaluationList}
{if $smarty.section.aId.iteration is even}
<tr class="rowA">
{else}
<tr class="rowB">
{/if}
<td>{$evaluationList[aId].title}</td>
<td>{$evaluationList[aId].schoolyear}</td>
<td width="50" class="smaller" valign="middle"
  ><a href="?act=edit,evltsk,{$evaluationList[aId].id}"><img src="images/article.gif" alt="[vazby]" width="16" height="16"></a
  ><a href="?act=edit,evaluation,{$evaluationList[aId].id}"><img src="images/edit.gif" alt="[edit]" width="16" height="16"></a
  ><a href="?act=delete,evaluation,{$evaluationList[aId].id}"><img src="images/delete.gif" alt="[smazat]" width="16" height="16"></a></td>
</tr>
{/section}
</table>
