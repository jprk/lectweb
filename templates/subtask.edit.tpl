<form name="subtaskForm" action="?act=save,subtask,{$subtask.id}" method="post">
<input type="hidden" name="id" value="{$subtask.id}">
<input type="hidden" name="lecture_id" value="{$lecture.id}">
<table class="admintable" border="0" cellpadding="2" cellspacing="1">
<tr class="rowA">
<td class="itemtitle">Název</td>
<td><input type="text" name="title" maxlength="255" size="32" value="{$subtask.title}"></td>
</tr>
<tr class="rowB">
<td class="itemtitle">Krátký název</td>
<td><input type="text" name="ttitle" maxlength="4" size="4" value="{$subtask.ttitle}"></td>
</tr>
<tr class="rowA">
<td class="itemtitle">Předmět</td>
<td>
<input type="text" readonly="readonly" style="width: 100%;" value="{$lecture.code} - {$lecture.title}">
</td>
</tr>
<tr class="rowB">
<td class="itemtitle">Typ úlohy</td>
<td>
<select name="type" style="width: 100%; font-size: 8pt;">
{html_options options=$taskTypeSelect selected=$subtask.type}
</select>
</td>
</tr>
<tr class="rowA">
<td colspan="2">
<textarea id="edcTextArea" name="assignment" style="width: 100%; height: 300px;">{$subtask.assignment|escape:"html"}</textarea>
</td>
</tr>
{* ... dates are now edited by subtaskdates.*.tpl ...
<tr class="rowB">
<td class="itemtitle">Odevzdání od</td>
<td><input type="text" name="datefrom" maxlength="10" size="10" value="{$subtask.datefrom|date_format:"%d.%m.%Y"}">&nbsp;&nbsp;<img src="images/calendar.gif" alt="[kalendář]" onClick="openCalendar('subtaskForm','datefrom');"></td>
</tr>
<tr class="rowA">
<td class="itemtitle">Odevzdání do</td>
<td><input type="text" name="dateto" maxlength="10" size="10" value="{$subtask.dateto|date_format:"%d.%m.%Y"}">&nbsp;&nbsp;<img src="images/calendar.gif" alt="[kalendář]" title="otevři kalendář" onClick="openCalendar('subtaskForm','dateto');"></td>
</tr>
*}
<tr class="rowB">
<td class="itemtitle">Maximum bodů</td>
<td><input type="text" name="maxpts" maxlength="3" size="3" value="{$subtask.maxpts}"></td>
</tr>
<tr class="rowA">
<td class="itemtitle">Pozice</td>
<td><input type="text" name="position" maxlength="2" size="2" value="{$subtask.position}"></td>
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
