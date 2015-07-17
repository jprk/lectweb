<p>
Vyberte úlohu k ručnímu obodování.
</p>

<table class="admintable" border="0" cellpadding="2" cellspacing="1">
<tr class="newobject">
<th class="left">Název úlohy</th>
<th>Odevzdáno</th>
<th>Zkontrolováno</th>
<th>Zbývá</th>
<th>Akce</th>
</tr>
{section name=aId loop=$solutionList}
{if $smarty.section.aId.iteration is even}
<tr class="rowA">
{else}
<tr class="rowB">
{/if}
<td>{$solutionList[aId].title}</td>
<td class="center" style="width: 16ex;">{$solutionList[aId].submitted}</td>
<td class="center" style="width: 16ex;">{$solutionList[aId].corrected}</td>
<td class="center" style="width: 16ex;">{$solutionList[aId].submitted-$solutionList[aId].corrected}</td>
<td width="40" class="smaller" valign="middle"
  ><a href="?act=show,solution,{$solutionList[aId].id}"><img src="images/famfamfam/application_go.png" alt="[přejít na úlohu]" title="[zvolit]" width="16" height="16"></a></td>
</tr>
</tr>
{/section}
</table>

{*
<table class="admintable" border="0" cellpadding="2" cellspacing="1">
<tr class="newobject">
<th class="left">Název úlohy</th>
<th>Student</th>
<th>Odevzdáno</th>
<th>Předstih</th>
<th>Kontroloval</th>
<th>Body/Max</th>
<th>Akce</th>
</tr>
{section name=aId loop=$solutionList}
{if $smarty.section.aId.iteration is even}
<tr class="rowA">
{else}
<tr class="rowB">
{/if}
<td>{$solutionList[aId].title}</td>
<td class="center">{$solutionList[aId].student_name}</td>
<td class="date">{$solutionList[aId].lastmodified|date_format:"%d.%m.%Y %H:%M:%S"}</td>
<td class="center">{$solutionList[aId].lead}</td>
<td class="center">
{if $solutionList[aId].lecturer_name}
{$solutionList[aId].lecturer_name}
{else}
-
{/if}
</td>
<td class="center">{$solutionList[aId].pts}&nbsp;/&nbsp;{$solutionList[aId].maxpts}</td>
<td width="40" class="smaller" valign="middle"
  ><a href="?act=show,file,{$solutionList[aId].file_id}"><img src="images/download.gif" alt="[stáhnout]" title="[stáhnout]" width="16" height="16"></a
  ><a href="?act=mark,solution,{$solutionList[aId].id}"><img src="images/check-true.gif" alt="[označit jako zpracované]" title="[označit]" width="16" height="16"></a></td>
</tr>
</tr>
{/section}
</table>
*}