<form name="lecturerForm" action="?act=save,lecturer,{$lecturer.id}" method="post">
<input type="hidden" name="id" value="{$lecturer.id}">
<table class="admintable" border="0" cellpadding="2" cellspacing="1">
<tr class="rowA">
<td class="itemtitle">Jméno</td>
<td><input type="text" name="firstname" maxlength="64" size="32" value="{$lecturer.firstname}"></td>
</tr>
<tr class="rowB">
<td class="itemtitle">Přijmení</td>
<td><input type="text" name="surname" maxlength="96" size="32" value="{$lecturer.surname}"></td>
</tr>
<tr class="rowA">
<td class="itemtitle">Místnost</td>
<td><input type="text" name="room" maxlength="16" size="16" value="{$lecturer.room}"></td>
</tr>
<tr class="rowB">
<td class="itemtitle">E-mail</td>
<td><input type="text" name="email" maxlength="96" size="32" value="{$lecturer.email}"></td>
</tr>
<tr class="rowA">
<td>&nbsp;</td>
<td>
<input type="submit" value="Uložit">
<input type="reset" value="Vymazat">
</td>
</tr>
</table>
</form>
