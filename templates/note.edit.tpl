<form action="?act=save,note,{$note.id}" method="post">
<input type="hidden" name="id"        value="{$note.id}">
<input type="hidden" name="object_id" value="{$note.object_id}">
<table class="admintable" border="0" cellpadding="2" cellspacing="1">
<tr class="rowA">
<td class="itemtitle">Text</td>
<td>
<textarea name="text" style="width: 100%; height: 300px;">
{$note.text|escape:"html"}
</textarea>
</td>
</tr>
<tr class="rowB">
<td class="itemtitle">Typ poznámky</td>
<td>
<select name="type" style="width: 100%;">
{html_options options=$noteTypeSelect selected=$note.type}
</select>
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
