<h1>{$section.title}</h1>
{$section.text}
<p>
<form action="ctrl.php?act=save,comments,42" method="post">
<table border="0" cellpadding="2" cellspacing="0" width="100%">
<tr class="rowA"><td>Jméno:</td  ><td><input type="text" name="name"    class="wide"></td></tr>
<tr class="rowB"><td>E-mail:</td ><td><input type="text" name="email"   class="wide"></td></tr>
<tr class="rowA"><td>Firma:</td  ><td><input type="text" name="company" class="wide"></td></tr>
<tr class="rowB"><td>Mìsto:</td  ><td><input type="text" name="city"    size="20"></td></tr>
<tr class="rowA"><td>Telefon:</td><td><input type="text" name="phone"   size="15"></td></tr>
<tr class="rowB">
<td colspan="2">
<textarea name="text" style="width: 100%; height=300px;">
{$section.text}
</textarea>
</td>
</tr>
<tr class="rowA">
<td>&nbsp;</td>
<td>
<input type="submit" value="Odeslat komentáø">
<input type="reset" value="Vymazat">
</td>
</tr>
</table>
</form>
