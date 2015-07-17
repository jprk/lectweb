<h1>Editace osoby</h1>
<form action="ctrl.php?act=save,people,{$person.id}" method="post">
<input type="hidden" name="id" value="{$person.id}">
<table border="0" cellpadding="0" cellspacing="0" width="100%">
<tr class="rowA">
<td class="itemtitle">Jméno</td>
<td><input type="text" name="name" maxlength="255" size="30" value="{$person.name}"></td>
</tr>
<tr class="rowB">
<td class="itemtitle">Pøíjmení</td>
<td><input type="text" name="surname" maxlength="255" size="50" value="{$person.surname}"></td>
</tr>
<tr class="rowA">
<td class="itemtitle">Titul pøed</td>
<td><input type="text" name="prefix" maxlength="255" size="16" value="{$person.prefix}"></td>
</tr>
<tr class="rowB">
<td class="itemtitle">Titul za</td>
<td><input type="text" name="suffix" maxlength="255" size="8" value="{$person.suffix}"></td>
</tr>
<tr class="rowA">
<td class="itemtitle">E-mail</td>
<td><input type="text" name="email" maxlength="255" size="24" value="{$person.email}"></td>
</tr>
<tr class="rowB">
<td class="itemtitle">Telefon</td>
<td><input type="text" name="phone" maxlength="16" size="16" value="{$person.phone}"></td>
</tr>
<tr class="rowA">
<td class="itemtitle">Fax</td>
<td><input type="text" name="fax" maxlength="16" size="16" value="{$person.fax}"></td>
</tr>
<tr class="rowB">
<td class="itemtitle">Historie</td>
<td>
<textarea name="history" style="width: 100%; height=100px;">
{$person.history}
</textarea>
</td>
</tr>
<tr class="rowA">
<td class="itemtitle">Výzkum</td>
<td>
<textarea name="research" style="width: 100%; height=100px;">
{$person.research}
</textarea>
</td>
</tr>
<tr class="rowB">
<td class="itemtitle">Zájmy</td>
<td>
<textarea name="interests" style="width: 100%; height=100px;">
{$person.interests}
</textarea>
</td>
</tr>
<tr class="rowA">
<td class="itemtitle">Èlenství</td>
<td>
<textarea name="membership" style="width: 100%; height=100px;">
{$person.membership}
</textarea>
</td>
</tr>
<tr class="rowB">
<td class="itemtitle">Kategorie</td>
<td>
<select name="category" style="width: 100%;">
{html_options options=$categories selected=$person.category}
</select>
</td>
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
