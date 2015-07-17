<p>
Studentovi bude prodlouženo odevzdání následujících úloh do data, zadaného
níže:
</p>
<ul>
{section name=sId loop=$subtaskList}
<li>{$subtaskList[sId].title} ({$subtaskList[sId].ttitle})</li>
{/section}
</ul>
<p>
Vložte prosím nové datum.
</p>
<form name="subtaskForm" action="?act=save,extension,{$student.id}" method="post">
<input type="hidden" name="mode" value="{$mode}">
{section name=sId loop=$subtaskList}
<input type="hidden" name="objids[{$subtaskList[sId].id}]" value="checked">
{/section}
<table class="admintable" border="0" cellpadding="2" cellspacing="1">
<tr class="rowA">
<td class="itemtitle" width="20%">Odevzdání do</td>
<td><input type="text" name="dateto" maxlength="10" size="10" value="{$student.dateto|date_format:"%d.%m.%Y"}">&nbsp;&nbsp;<img src="images/calendar.gif" alt="[kalendář]" title="otevři kalendář" onClick="openCalendar('subtaskForm','dateto');"></td>
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
