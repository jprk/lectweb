<h1>Editace obrázku referencí</h1>
<form action="ctrl.php?act=save,references,{$file.Id}" method="post" enctype="multipart/form-data">
<input type="hidden" name="Id" value="{$file.Id}">
<input type="hidden" name="MAX_FILE_SIZE" value="100000">
<input type="hidden" name="parent" value="{$referenceSection}">
<input type="hidden" name="type" value="{$referenceFileType}">
<table border="0" cellpadding="0" cellspacing="0" width="492">
<tr class="rowA">
<td class="itemtitle">Soubor s obrázkem</td>
<td>
<input type="file" name="userfile" style="width: 100%;"><br>
<small>Nechcete-li nahrát nová data, nechte toto pole nevyplnìné.</small>
</td>
</tr>
<tr class="rowB">
<td class="itemtitle">Popis reference</td>
<td><input type="text" name="description" maxlength="255" style="width: 100%;" value="{$file.description|escape:"html"}"></td>
</tr>
<tr class="rowA">
<td class="itemtitle">Pozice</td>
<td><input type="text" name="position" maxlength="3" size="3" value="{$file.position}"></td>
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
