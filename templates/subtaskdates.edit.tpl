<p>
V níže uvedeném formuláři můžete změnit datumy omezující dobu odevzdání
dílčí úlohy <em>{$subtask.title}</em>.
</p>
<form name="subtaskForm" action="?act=save,subtaskdates,{$subtask.id}" method="post">
<input type="hidden" name="subtask_id" value="{$subtaskdates.subtask_id}">
<input type="hidden" name="year" value="{$subtaskdates.year}">
<table class="admintable" border="0" cellpadding="2" cellspacing="1">
<tr class="rowA">
<td class="itemtitle" style="width: 16ex;">Odevzdání od</td>
<td><input type="text" name="datefrom" maxlength="10" size="10" value="{$subtaskdates.datefrom|date_format:"%d.%m.%Y"}">&nbsp;&nbsp;<img src="images/calendar.gif" alt="[kalendář]" onClick="openCalendar('subtaskForm','datefrom');"></td>
</tr>
<tr class="rowB">
<td class="itemtitle" style="width: 16ex;">Odevzdání do</td>
<td><input type="text" name="dateto" maxlength="10" size="10" value="{$subtaskdates.dateto|date_format:"%d.%m.%Y"}">&nbsp;&nbsp;<img src="images/calendar.gif" alt="[kalendář]" title="otevři kalendář" onClick="openCalendar('subtaskForm','dateto');"></td>
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
