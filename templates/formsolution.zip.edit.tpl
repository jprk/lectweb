<p>
Vložte soubory s vypracovaným řešením zadání ze Simulinku.
Odpovídat můžete pouze jednou.
</p>
<form name="solutionform" action="?act=save,formsolution,{$subtask.id}" method="post" enctype="multipart/form-data">
<input type="hidden" name="MAX_FILE_SIZE" value="16000000">
{section name=pId loop=$parts}
<h3>Úloha {$subtask.ttitle}-{$assignment.assignmnt_id|string_format:"%05d"}{$parts[pId].part}</h2>
<table cellspacing="1" cellpadding="0">
<tr><td>Model v Simulinku (.mdl)&nbsp;</td><td><input type="file" name="mdl[{$parts[pId].part}]" size="50"></td></tr>
<!-- tr><td>Popis řešení (.pdf)      </td><td><input type="file" name="pdf[{$parts[pId].part}]"></td></tr -->
</table>
{/section}
<p>
<input type="submit" value="Odeslat řešení">
<input type="reset" value="Vymazat">
</p>
</form>
