<p>
Vložte soubory s úlohami, které Vám student odevzdal:
</p>

<form name="solutionform" action="?act=save,formsolution,{$student.id}" method="post" enctype="multipart/form-data">
<input type="hidden" name="MAX_FILE_SIZE" value="16000000">
<input type="hidden" name="mode" value="2">
<table class="admintable" border="0" cellpadding="2" cellspacing="1">
<thead>
<tr class="newobject">
  <th class="left">Název úlohy</th>
  <th>Soubor</th>
</tr>
</thead>
<tbody>
{section name=aId loop=$solutionList}
{if $smarty.section.aId.iteration is even}
<tr class="rowA">
{else}
<tr class="rowB">
{/if}
  <td>{$solutionList[aId].title}</td>
  <td><input type="file" name="solutions[{$solutionList[aId].id}][0]" size="0%"></td>
</tr>
{/section}
</tbody>
</table>
<p>
<input type="submit" value="Odeslat řešení">
<input type="reset" value="Vymazat">
</p>
</form>
