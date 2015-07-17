<form action="?act=save,evltsk,{$lecture.id}" method="post">
<table class="admintable" border="0" cellpadding="2" cellspacing="1">
<tr>
<th align="left">Název</th>
{section name=tId loop=$taskList}
<th style="width: 5em;">{$taskList[tId].title}<br><small>({$taskList[tId].minpts}b. min)</small></th>
{/section}
</tr>
{section name=eId loop=$evaluationList}
{if $smarty.section.eId.iteration is even}
<tr class="rowA">
{else}
<tr class="rowB">
{/if}
<td>{$evaluationList[eId].title}</td>
{section name=tId loop=$taskList}
<td class="center"><input type="checkbox" name="te_rel[{$evaluationList[eId].id}][]" value="{$taskList[tId].id}"{$evaluationList[eId].checked[tId]}}></td>
{/section}
</tr>
{/section}
<tr class="submitrow">
  <td colspan="{$smarty.section.tId.max+1}" class="center"><input type="submit" value="Uložit"></td>
</tr>
</table>
</form>
