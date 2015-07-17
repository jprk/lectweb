<form name="taskForm" action="?act=save,task,{$task.id}" method="post">
<input type="hidden" name="id" value="{$task.id}">
<input type="hidden" name="lecture_id" value="{$lecture.id}">
<table class="admintable" border="0" cellpadding="2" cellspacing="1">
<tr class="rowA">
<td class="itemtitle">Název</td>
<td><input type="text" name="title" style="width: 100%;"maxlength="255" size="32" value="{$task.title}"></td>
</tr>
<tr class="rowB">
<td class="itemtitle">Předmět</td>
<td>
<input type="text" readonly="readonly" style="background-color: #fbfbf0; width: 100%;" value="{$lecture.code} - {$lecture.title}">
</td>
</tr>
<tr class="rowA">
<td class="itemtitle">Typ úlohy</td>
<td>
<select name="type" style="width: 100%; font-size: 8pt;">
{html_options options=$taskTypeSelect selected=$task.type}
</select>
</td>
</tr>
<tr class="rowB">
<td class="itemtitle">Minimum bodů</td>
<td><input type="text" name="minpts" maxlength="3" size="3" value="{$task.minpts}"></td>
</tr>
<tr class="rowA">
<td class="itemtitle">Pozice</td>
<td><input type="text" name="position" maxlength="2" size="2" value="{$task.position}"></td>
</tr>
<tr class="submitrow">
<td>&nbsp;</td>
<td>
<input type="submit" value="Uložit">
<input type="reset" value="Vymazat">
</td>
</tr>
</table>
</form>
