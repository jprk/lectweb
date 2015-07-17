<p>
Vyberte dílčí úlohy, jimž chcete změnit termín odevzdání pro tohoto studenta.
</p>
<form id="extForm" action="?act=edit,extension,{$student.id}" method="post">
<input type="hidden" name="mode" value="{$mode}">
<table class="pointtable" border="0" cellpadding="2" cellspacing="1">
<tr class="newobject">
<th>&nbsp;</th>
<th style="text-align: left; padding: 0ex 0.5ex;">Zkratka</th>
<th style="text-align: left; padding: 0ex 0.5ex;">Název</th>
<th>Individuální termín</th>
</tr>
{section name=sId loop=$subtaskList}
{if $smarty.section.sId.iteration is even}
<tr class="rowA">
{else}
<tr class="rowB">
{/if}
<td width="5%" align="center"><input type="checkbox" id="subtask{$subtaskList[sId].id}" name="objids[{$subtaskList[sId].id}]"></td>
<td width="5%" style="padding: 0ex 0.5ex;">{$subtaskList[sId].ttitle}</td>
<td style="padding: 0ex 0.5ex;">{$subtaskList[sId].title}</td>
<td width="20%" style="padding: 0ex 1ex;">{$subtaskList[sId].dateto}</td>
</tr>
{/section}
<tr class="newobject">
<td>&nbsp;</td>
<td colspan="3">
<input type="submit" value="Zadat datum">
<input type="reset" value="Vymazat">
</td>
</tr>
</table>
</form>
