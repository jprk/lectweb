<table class="admintable" border="0" cellpadding="2" cellspacing="1">
<tr>
  <th style="text-align: left;">Název</th>
  <th>Kód</th>
  <th>K dispozici</th>
  <th>Vygenerováno</th>
  <th>&nbsp;</th>
</tr>
{section name=aId loop=$subtaskList}
{if $smarty.section.aId.iteration is even}
<tr class="rowA">
{else}
<tr class="rowB">
{/if}
<td>{$subtaskList[aId].title}</td>
<td class="center">{$subtaskList[aId].ttitle}</td>
<td class="center">{$subtaskList[aId].count}</td>
<td class="center">{$subtaskList[aId].generated}</td>
<td width="96" class="smaller" valign="middle"
  ><a href="?act=edit,formassign,{$subtaskList[aId].id}"             ><img src="images/famfamfam/database.png"         alt="[import]"              title="import zadání"                    width="16" height="16"></a
  ><a href="?act=show,formassign,{$subtaskList[aId].id}"             ><img src="images/famfamfam/database_refresh.png" alt="[generate]"            title="generuj zadání"                   width="16" height="16"></a
  ><a href="?act=show,formassign,{$subtaskList[aId].id}&regenerate=1"><img src="images/famfamfam/database_error.png"   alt="[regenerate]"          title="regeneruj zadání"                 width="16" height="16"></a
  ><a href="?act=edit,formassign,{$subtaskList[aId].id}&copysub=1"   ><img src="images/famfamfam/database_connect.png" alt="[copy subtask]"        title="kopíruj z jiné úlohy"             width="16" height="16"></a
  ><a href="?act=show,formassign,{$subtaskList[aId].id}&onlynew=1"   ><img src="images/famfamfam/database_add.png"     alt="[generate additional]" title="generuj zadání pro nové studenty" width="16" height="16"></a
  ><a href="?act=show,formassign,{$subtaskList[aId].id}&catalogue=1" ><img src="images/famfamfam/report_edit.png"      alt="[catalogue]"           title="generuj seznam řešení"            width="16" height="16"></a></td>
</tr>
{/section}
</table>
