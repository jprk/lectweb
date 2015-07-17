<form action="?act=save,urls,{$url.id}" method="post">
<input type="hidden" name="id" value="{$url.id}">
<table class="admintable" border="0" cellpadding="2" cellspacing="1">
<tr class="rowA">
<td class="itemtitle">URL</td>
<td><input class="admintable" type="text" name="url" maxlength="255" style="width: 100%;" value="{$url.url}"></td>
</tr>
<tr class="rowB">
<td class="itemtitle">Titulek</td>
<td><input type="text" name="title" maxlength="80" style="width: 100%;" value="{$url.title|escape:"html"}"></td>
</tr>
<tr class="rowA">
<td class="itemtitle">Popis odkazu</td>
<td>
<textarea class="textfont" name="description" style="width: 100%; height: 100px;">{$url.description}</textarea>
</td>
</tr>
<tr class="rowB">
<td class="itemtitle">Pozice</td>
<td><input type="text" name="position" maxlength="3" size="3" value="{$url.position}"></td>
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
