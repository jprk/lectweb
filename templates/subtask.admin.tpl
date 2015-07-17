<p>
<table class="admintable" border="0" cellpadding="2" cellspacing="1">
<tr class="newobject">
<td colspan="6">Přidat dílčí úlohu</td>
<td colspan="2" width="64" class="smaller" align="right" valign="middle"
  ><a href="?act=edit,subtask,0"><img src="images/famfamfam/report_add.png" title="přidat novou dílčí úlohu" alt="[nová dílčí úloha]" width="16" height="16"></a></td>
</tr>
<tr>
  <th>Název</th>
  <th>Kód</th>
  <th>Typ</th>
  <th>Od</th>
  <th>Do</th>
  <th>Max</th>
  <th colspan="2">&nbsp;</th>
</tr>
{section name=aId loop=$subtaskList}
{if $smarty.section.aId.iteration is even}
<tr class="rowA">
{else}
<tr class="rowB">
{/if}
<td title="pozice {$subtaskList[aId].position}">{$subtaskList[aId].title}</td>
<td class="center">{$subtaskList[aId].ttitle}</td>
<td>
  <div title="{$subtaskList[aId].typestr}" 
       style="overflow: hidden; white-space: nowrap; text-overflow: ellipsis; width: 200px;"
       >{$subtaskList[aId].typestr}</div></td>
<td class="center">
{if $subtaskList[aId].datefrom == '-'}
-
{elseif $subtaskList[aId].datefrom}
{$subtaskList[aId].datefrom|date_format:"%d.%m.%Y"}
{else}
nezadáno
{/if}
</td>
<td class="center">
{if $subtaskList[aId].dateto == '-'}
-
{elseif $subtaskList[aId].dateto}
{$subtaskList[aId].dateto|date_format:"%d.%m.%Y"}
{else}
nezadáno
{/if}
</td>
<td class="center">{$subtaskList[aId].maxpts}</td>
{if $subtaskList[aId].datefrom == '-'}
<td width="16" class="smaller" valign="middle">&nbsp;</td>
{else}
<td width="16" class="smaller" valign="middle"
  ><a href="?act=edit,subtaskdates,{$subtaskList[aId].id}"><img src="images/famfamfam/calendar.png" alt="[změna data]" title="změna data" width="16" height="16"></a></td>
{/if}
<td width="48" class="smaller" valign="middle"
  ><a href="?act=admin,extension,{$subtaskList[aId].id}&mode=1"><img src="images/famfamfam/bell_add.png"      alt="[prodloužit]" title="prodloužit" width="16" height="16"></a
  ><a href="?act=edit,subtask,{$subtaskList[aId].id}"          ><img src="images/famfamfam/report_edit.png"   alt="[změnit]"     title="změnit"     width="16" height="16"></a
  ><a href="?act=delete,subtask,{$subtaskList[aId].id}"        ><img src="images/famfamfam/report_delete.png" alt="[smazat]"     title="smazat"     width="16" height="16"></a></td>
</tr>
{/section}
</table>
