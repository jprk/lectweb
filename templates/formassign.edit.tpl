{if $formassignment.copysub}
<p>
Vyberte dílčí úlohu, jejíž přiřazení úkolů chcete zkopírovat k této dílčí
úloze.
</p>
<form name="subtaskeditform" action="" method="get">
<input type="hidden" name="act" value="show,formassign,{$subtask.id}">
<table class="admintable" border="0" cellpadding="2" cellspacing="1">
<tr class="rowA">
<td class="itemtitle">Název dílčí úlohy</td>
<td>
<select name="copysub" style="width: 100%;">
{section name=aId loop=$studentSubtaskList}
<option label="{$studentSubtaskList[aId].ttitle}" value="{$studentSubtaskList[aId].id}">{$studentSubtaskList[aId].title} ({$studentSubtaskList[aId].ttitle})</option>
{/section}
</select>
</td>
</tr>
<tr class="rowB">
<td>&nbsp;</td>
<td>
<input type="submit" value="Pokračovat">
<input type="reset" value="Vymazat">
</td>
</tr>
</table>
</form>
{else}
<form name="fileeditform" action="?act=save,formassign,{$subtask.id}" method="post" enctype="multipart/form-data">
<input type="hidden" name="subtask_id" value="{$subtask.id}">
<input type="hidden" name="MAX_FILE_SIZE" value="8000000">
<table class="admintable" border="0" cellpadding="2" cellspacing="1">
<tr class="rowA">
<td class="itemtitle">Soubor s řešeními</td>
<td>
<!--input type="file" name="userfile" style="width: 100%;"><br-->
<input type="file" name="assignfile" size="100%"><br>
</td>
</tr>
<tr class="rowB">
<td>&nbsp;</td>
<td>
<input type="submit" value="Odeslat">
<input type="reset" value="Vymazat">
</td>
</tr>
</table>
</form>
{/if}