<p>
V níže uvedeném formuláři můžete změnit datumy omezující dobu odevzdání
úlohy <em>{$task.title}</em>.
</p>
<form name="taskForm" action="?act=save,taskdates,{$task.id}" method="post">
<input type="hidden" name="task_id" value="{$taskdates.task_id}">
<input type="hidden" name="year" value="{$taskdates.year}">
<table class="admintable" border="0" cellpadding="2" cellspacing="1">
<tr class="rowA">
<td class="itemtitle" style="width: 16ex;">Odevzdání od</td>
<td><input type="text" name="datefrom" maxlength="10" size="10" value="{$taskdates.datefrom|date_format:"%d.%m.%Y"}">&nbsp;&nbsp;<img src="images/calendar.gif" alt="[kalendář]" title="otevři kalendář" onClick="openCalendar('taskForm','datefrom');"></td>
</tr>
<tr class="rowB">
<td class="itemtitle" style="width: 16ex;">Odevzdání do</td>
<td><input type="text" name="dateto" maxlength="10" size="10" value="{$taskdates.dateto|date_format:"%d.%m.%Y"}">&nbsp;&nbsp;<img src="images/calendar.gif" alt="[kalendář]" title="otevři kalendář" onClick="openCalendar('taskForm','dateto');"></td>
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
