{include file="admin.sec.hea.tpl"}
{$section.text}
<h2>Task list</h2>
<table class="admintable" border="0" cellpadding="2" cellspacing="1">
<tr class="newobject">
<th>Task title</th>
<th colspan="2" style="width: 32ex;">Hand in from-to</th>
<th>Is active?</th>
</tr>
{section name=aId loop=$studentSubtaskList}
{if $smarty.section.aId.iteration is even}
<tr class="rowA">
{else}
<tr class="rowB">
{/if}
<td>&nbsp;{$studentSubtaskList[aId].title}</td>
<td class="date">{$studentSubtaskList[aId].datefrom|date_format:"%d.%m.%Y %H:%M"}</td>
<td class="date">{$studentSubtaskList[aId].dateto|date_format:"%d.%m.%Y %H:%M"}</td>
<td class="center">{if $studentSubtaskList[aId].active}active{else}inactive{/if}</td>
</tr>
{/section}
</table>
{if $sectionFileList}
<h2>Files and premium tasks</h2>
<div class="file">
<table>
{section name=filePos loop=$sectionFileList}
<tr>
  <td valign="top"><img src="images/{$sectionFileList[filePos].icon}.gif" width="16" height="16" alt="[{$sectionFileList[filePos].icon} file]"></td>
  <td>
  <p class="atitle"
    ><a href="?act=show,file,{$sectionFileList[filePos].id}"
	>{$sectionFileList[filePos].origfname}</a>{include file="admin.sec.fil.tpl"}</p>
  <p class="aabstract">{$sectionFileList[filePos].description}
  </td>
</tr>
{/section}
</table>
</div>
{/if}
