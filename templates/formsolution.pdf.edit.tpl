<p>
Vložte PDF soubor (respektive soubory, pokud má úloha více částí)
s vypracovaným řešením zadání. Odpovídat můžete pouze jednou.
</p>
<form name="solutionform" action="?act=save,formsolution,{$subtask.id}" method="post" enctype="multipart/form-data">
<input type="hidden" name="MAX_FILE_SIZE" value="16000000">
{section name=pId loop=$parts}
<h3>Úloha {$subtask.ttitle}-{$assignment.assignmnt_id|string_format:"%05d"}{$parts[pId].part}</h2>
<table class="admintable" border="0" cellpadding="2" cellspacing="1">
<tr class="rowA">
  <td class="itemtitle" width="100%">Soubor s popisem řešení úlohy (.pdf)</td>
  <td>
    <input type="file" name="pdf[{$parts[pId].part}]" size="70%"><br>
  </td>
</tr>
</table>
{/section}
<p>
<input type="submit" value="Odeslat řešení">
<input type="reset" value="Vymazat">
</p>
</form>
