<h1>Parametry aplikace</h1>
<form action="ctrl.php?act=save,settings,42" method="post">
<table border="0" cellpadding="0" cellspacing="0" width="100%">
<tr class="rowA">
<td class="itemtitle">DPH</td>
<td><input type="text" name="dph" maxlength="2" size="2" value="{$settings.dph}">&nbsp;%</td>
</tr>
<tr class="rowB">
<td class="itemtitle">GBP</td>
<td><input type="text" name="gbpczk" maxlength="6" size="6" value="{$settings.gbpczk}">&nbsp;Kè</td>
</tr>
<tr class="rowA">
<td class="itemtitle">USD</td>
<td><input type="text" name="usdczk" maxlength="6" size="6" value="{$settings.usdczk}">&nbsp;Kè</td>
</tr>
<tr class="rowB">
<td class="itemtitle">Poštovné</td>
<td><input type="text" name="mailgbp" maxlength="6" size="6" value="{$settings.mailgbp}">&nbsp;GBP</td>
</tr>
<tr class="rowA">
<td class="itemtitle">Adresát objednávek</td>
<td><input type="text" name="orderemail" maxlength="80" class="wide" value="{$settings.orderemail}"></td>
</tr>
<tr class="rowB">
<td class="itemtitle">Adresát komentáøù</td>
<td><input type="text" name="commentemail" maxlength="80" class="wide" value="{$settings.commentemail}"></td>
</tr>
<tr class="rowA">
<td class="itemtitle">Adresát registrace</td>
<td><input type="text" name="regemail" maxlength="80" class="wide" value="{$settings.regemail}"></td>
</tr>
<tr class="rowB">
<td class="itemtitle">Errors-To:</td>
<td><input type="text" name="erroremail" maxlength="80" class="wide" value="{$settings.regemail}"></td>
</tr>
<tr class="rowA">
<td>&nbsp;</td>
<td>
<input type="submit" value="Odeslat">
<input type="reset" value="Vymazat">
</td>
</tr>
</table>
</form>

