<p>
<table class="admintable" border="0" cellpadding="2" cellspacing="1">
<tr class="newobject">
<td colspan="5">&nbsp;Přidat cvičení</td>
<td width="40" class="smaller" valign="middle"
  ><a href="?act=edit,exercise,0&lecture_id={$lecture.id}"><img src="images/add.gif" title="přidat cvičení" alt="[nové cvičení]" width="16" height="16"></a></td>
</tr>
{if $exerciseList}
<tr>
<th>den</th>
<th>od</th>
<th>do</th>
<th>kde</th>
<th>cvičící</th>
<th>&nbsp;</th>
</tr>
{section name=aId loop=$exerciseList}
{if $smarty.section.aId.iteration is even}
<tr class="rowA">
{else}
<tr class="rowB">
{/if}
<td class="center">{$exerciseList[aId].day.name}</td>
<td class="center">{$exerciseList[aId].from|date_format:"%H:%M"}</td>
<td class="center">{$exerciseList[aId].to|date_format:"%H:%M"}</td>
<td class="center">{$exerciseList[aId].room}</td>
<td class="center">{$exerciseList[aId].lecturer.firstname} {$exerciseList[aId].lecturer.surname}</td>
<td width="40" class="smaller" valign="middle"
  ><a href="?act=edit,exercise,{$exerciseList[aId].id}"><img src="images/edit.gif" alt="[edit]" width="16" height="16"></a
  ><a href="?act=delete,exercise,{$exerciseList[aId].id}"><img src="images/delete.gif" alt="[smazat]" width="16" height="16"></a></td>
</tr>
{/section}
{/if}
</table>
