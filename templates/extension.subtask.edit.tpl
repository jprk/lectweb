<p>
Následujícím studentům bude prodlouženo odevzdávání úlohy <em>{$subtask.title}</em>
do data zadaného níže:
</p>
<ul>
{section name=sId loop=$studentList}
<li>{$studentList[sId].surname} {$studentList[sId].firstname} ({$studentList[sId].yearno}/{$studentList[sId].groupno})</li>
{/section}
</ul>
<p>
Vložte prosím nové datum.
</p>
<form name="subtaskForm" action="?act=save,extension,{$subtask.id}" method="post">
<input type="hidden" name="mode" value="{$mode}">
{section name=sId loop=$studentList}
<input type="hidden" name="objids[{$studentList[sId].id}]" value="checked">
{/section}
<table class="admintable" border="0" cellpadding="2" cellspacing="1">
<tr class="rowA">
<td class="itemtitle" width="20%">Odevzdání do</td>
<td><input type="text" name="dateto" maxlength="10" size="10" value="{$subtask.dateto|date_format:"%d.%m.%Y"}">&nbsp;&nbsp;<img src="images/calendar.gif" alt="[kalendář]" title="otevři kalendář" onClick="openCalendar('subtaskForm','dateto');"></td>
</tr>
<tr class="newobject">
<td>&nbsp;</td>
<td>
<input type="submit" value="Uložit">
<input type="reset" value="Vymazat">
</td>
</tr>
</table>
</form>
