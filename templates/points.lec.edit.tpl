{include file="points.lock.tpl"}
<h2>Seznam studentů</h2>
{if $studentList}
<form action="?act=save,points,{$lecture.id}" method="post">
<input type="hidden" name="type" value="lec">
<table class="pointtable" border="0" cellpadding="2" cellspacing="1">
<tr>
<th style="width: 8em; text-align: left;"
  ><a href="?act=edit,points,{$lecture.id}&type=lec&order=2">Jméno</a
  >/<a href="?act=edit,points,{$lecture.id}&type=lec&order=3">login</a></th>
<th style="width: 5em;">Skupina</th>
{section name=subtaskPos loop=$subtaskList}
<th style="width: 5ex;"><img src="throt.php?text={$subtaskList[subtaskPos].title}" title="{$subtaskList[subtaskPos].title}" alt="{$subtaskList[subtaskPos].title}"></th>
{/section}
</tr>
{section name=studentPos loop=$studentList}
{if $smarty.section.studentPos.iteration is even}
<tr class="rowA">
{else}
<tr class="rowB">
{/if}
{if $order == 3}
<td>{$studentList[studentPos].login}</td>
{else}
<td>{$studentList[studentPos].surname}&nbsp;{$studentList[studentPos].firstname}</td>
{/if}
<td class="center">{$studentList[studentPos].yearno}/{$studentList[studentPos].groupno}</td>
{section name=subtaskPos loop=$subtaskList}
{if $smarty.section.studentPos.iteration is even}<td class="subtskA">{else}<td class="subtskB">{/if}<input type="text"  size="5" maxlength="5" style="width: 3em; text-align: center;" name="points[{$studentList[studentPos].dbid}][{$subtaskList[subtaskPos].id}]" value="{$studentList[studentPos].subpoints[subtaskPos].points}"><input type="hidden" name="comments[{$studentList[studentPos].dbid}][{$subtaskList[subtaskPos].id}]" value="{$studentList[studentPos].subpoints[subtaskPos].comment}"></td>
{/section}
</tr>
{/section}
<tr class="newobject"><td colspan="{$smarty.section.subtaskPos.index+2}" class="center"><input type="submit" value="Uložit"></td></tr>
</table>
</form>
{else}
<p>
Tento předmět ještě nemá přiřazené žádné studenty.
</p>
{/if}
