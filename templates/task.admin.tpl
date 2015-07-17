<table class="admintable" border="0" cellpadding="2" cellspacing="1">
<tr class="newobject">
<td colspan="5">Přidat další úlohu</td>
<td width="48" class="smaller" align="right" valign="middle" colspan="2"
  ><a href="?act=edit,task,0"><img src="images/famfamfam/report_add.png" title="přidat novou úlohu" alt="[nová úloha]" width="16" height="16"></a></td>
</tr>
<tr>
  <th>Název</th>
  <th>Typ</th>
  <th>Od</th>
  <th>Do</th>
  <th>Min</th>
  <th colspan="2">&nbsp;</th>
</tr>
{section name=aId loop=$taskList}
{if $smarty.section.aId.iteration is even}
<tr class="rowA">
{else}
<tr class="rowB">
{/if}
<td>{$taskList[aId].title}</td>
<td>{$taskList[aId].typestr}</td>
<td class="center">
{if $taskList[aId].datefrom == '-'}
-
{elseif $taskList[aId].datefrom}
{$taskList[aId].datefrom|date_format:"%d.%m.%Y"}
{else}
nutno zadat
{/if}
</td>
<td class="center">
{if $taskList[aId].dateto == '-'}
-
{elseif $taskList[aId].dateto}
{$taskList[aId].dateto|date_format:"%d.%m.%Y"}
{else}
nutno zadat
{/if}
<td class="center">{$taskList[aId].minpts}</td>
</td>
{if $taskList[aId].datefrom == '-'}
<td width="16" class="smaller" valign="middle">&nbsp;</td>
{else}
<td width="16" class="smaller" valign="middle"
  ><a href="?act=edit,taskdates,{$taskList[aId].id}"><img src="images/famfamfam/calendar.png" alt="[změna data]" title="změna data" width="16" height="16"></a></td>
{/if}
<td width="32" class="smaller" valign="middle"
  ><a href="?act=edit,task,{$taskList[aId].id}"  ><img src="images/famfamfam/report_edit.png"   alt="[změnit]"     title="změnit"     width="16" height="16"></a
  ><a href="?act=delete,task,{$taskList[aId].id}"><img src="images/famfamfam/report_delete.png" alt="[smazat]"     title="smazat"     width="16" height="16"></a></td>
</tr>
{/section}
<tr class="newobject">
<td colspan="5">Vazba na dílčí úkoly</td>
<td width="48" class="smaller" align="right" valign="middle" colspan="2"
  ><a href="?act=edit,tsksub,{$lecture.id}"><img src="images/article.gif" alt="[vazba na dílčí úlohy]" width="16" height="16"></a></td>
</tr>
</table>
