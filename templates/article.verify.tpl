<h1>Registrace</h1>
{if $regerror}
<p>
{$regerror}
</p>
{else}
<p>
Tento �l�nek je p��stupn� pouze po p�edchoz� registraci. Registrace bude fungovat
pouze tehdy, podporuje-li v� prohl�e� cookies. Je-li tomu tak, bude registrace
nutn� pouze jednou.
</p> 
{/if}
<form action="ctrl.php?act=verify,article,{$article.Id}" method="post">
<input type="hidden" name="Id" value="{$article.Id}">
<table border="0" cellpadding="0" cellspacing="0" width="100%">
<tr class="rowB">
<td class="itemtitle">Jm�no a p��jmen�</td>
<td><input type="text" name="name" style="width: 100%;"></td>
</tr>
<tr class="rowA">
<td class="itemtitle">E-mail</td>
<td><input type="text" name="email" style="width: 100%;"></td>
</tr>
<tr class="rowB">
<td class="itemtitle">Firma</td>
<td><input type="text" name="company" style="width: 100%;"></td>
</tr>
<tr class="rowA">
<td>&nbsp;</td>
<td>
<input type="submit" value="Registrovat">
<input type="reset" value="Vymazat">
</td>
</tr>
</table>
</form>

