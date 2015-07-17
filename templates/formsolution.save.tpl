{if $confirmed}
<p>
Řešení úlohy bylo uloženo do databáze. Pokud jste odpověděli správně,
měly by vám naskočit i nějaké body.
<p>
{else}
<p>
Potvrďte prosím, že následující hodnoty jsou správně.
</p>
<form name="solutionform" action="?act=save,formsolution,{$subtask.id}" method="post" >
<input type="hidden" name="confirmed" value="1">
{section name=pId loop=$parts}
<input type="hidden" name="a[{$parts[pId].part}]" value="{$aa[pId]}">
<input type="hidden" name="b[{$parts[pId].part}]" value="{$bb[pId]}">
<input type="hidden" name="c[{$parts[pId].part}]" value="{$cc[pId]}">
<input type="hidden" name="d[{$parts[pId].part}]" value="{$dd[pId]}">
<input type="hidden" name="e[{$parts[pId].part}]" value="{$ee[pId]}">
<input type="hidden" name="f[{$parts[pId].part}]" value="{$ff[pId]}">
<h3>Úloha {$subtask.ttitle}-{$assignment.assignmnt_id|string_format:"%05d"}{$parts[pId].part}</h2>
<table cellspacing="1" cellpadding="0">
<tr><td>A =</td><td><input type="text" name="xa[{$parts[pId].part}]" value="{$aa[pId]}" disabled="disabled"></td></tr>
<tr><td>B =</td><td><input type="text" name="xb[{$parts[pId].part}]" value="{$bb[pId]}" disabled="disabled"></td></tr>
<tr><td>C =</td><td><input type="text" name="xc[{$parts[pId].part}]" value="{$cc[pId]}" disabled="disabled"></td></tr>
<tr><td>D =</td><td><input type="text" name="xd[{$parts[pId].part}]" value="{$dd[pId]}" disabled="disabled"></td></tr>
<tr><td>E =</td><td><input type="text" name="xe[{$parts[pId].part}]" value="{$ee[pId]}" disabled="disabled"></td></tr>
<tr><td>F =</td><td><input type="text" name="xf[{$parts[pId].part}]" value="{$ff[pId]}" disabled="disabled"></td></tr>
</table>
{/section}
<p>
<input type="submit" value="Odeslat řešení">
<input type="reset" value="Vymazat">
</p>
</form>
{/if}