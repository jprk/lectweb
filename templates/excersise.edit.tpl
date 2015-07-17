<form name="exerciseForm" action="?act=save,exercise,{$exercise.id}" method="post">
<input type="hidden" name="id" value="{$exercise.id}">
<table class="admintable" border="0" cellpadding="2" cellspacing="1">
<tr class="rowA">
<td class="itemtitle">Předmět</td>
<td>
<select name="lecture_id" style="width: 100%; font-size: 8pt;">
{html_options options=$lectureSelect selected=$exercise.lecture_id}
</select>
</td>
</tr>
<tr class="rowB">
<td class="itemtitle">Školní rok</td>
<td>
<select name="schoolyear" style="width: 100%; font-size: 8pt;">
{html_options options=$yearSelect selected=$exercise.schoolyear}
</select>
</td>
</tr>
<tr class="rowA">
<td class="itemtitle">Den</td>
<td>
<select name="day" style="width: 100%; font-size: 8pt;">
{html_options options=$daySelect selected=$exercise.day.num}
</select>
</td>
</tr>
<tr class="rowB">
<td class="itemtitle">Od</td>
<td><input type="text" name="from" maxlength="5" size="5" value="{$exercise.from|date_format:"%H:%M"}">&nbsp;&nbsp;<img src="images/calendar.gif" alt="[kalendář]" onClick="getCalendarFor(document.exerciseForm.from);"></td>
</tr>
<tr class="rowA">
<td class="itemtitle">Do</td>
<td><input type="text" name="to" maxlength="5" size="5" value="{$exercise.to|date_format:"%H:%M"}">&nbsp;&nbsp;<img src="images/calendar.gif" alt="[kalendář]" onClick="getCalendarFor(document..exerciseForm.to);"></td>
</tr>
<tr class="rowB">
<td class="itemtitle">Místnost</td>
<td><input type="text" name="room" maxlength="32" size="8" value="{$exercise.room}"></td>
</tr>
<tr class="rowA">
<td class="itemtitle">Cvičící</td>
<td>
<select name="lecturer_id" style="width: 100%; font-size: 8pt;">
{html_options options=$lecturerSelect selected=$exercise.lecturer_id}
</select>
</td>
</tr>
<tr class="rowB">
<td>&nbsp;</td>
<td>
<input type="submit" value="Uložit">
<input type="reset" value="Vymazat">
</td>
</tr>
</table>
</form>
