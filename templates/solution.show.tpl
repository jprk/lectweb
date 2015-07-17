<p>
V databázi jsou uložena následující řešení úlohy <em>{$subtask.title} ({$subtask.ttitle})</em>:
</p>
<table class="admintable" border="0" cellpadding="2" cellspacing="1">
<tr class="newobject">
<th style="text-align: left;"><a href="?act=show,solution,{$subtask.id}&order=1">Login</a></th>
<th style="text-align: left;"><a href="?act=show,solution,{$subtask.id}&order=2">Student</a></th>
<th>Skupina</th>
<th style="width: 10em;"><a href="?act=show,solution,{$subtask.id}&order=3">Odevzdáno</a></th>
<th>Kontroluje</th>
<th>Kontroloval</th>
<th style="width: 2em;">Body/Max</th>
<th>&nbsp;</th>
</tr>
{section name=aId loop=$solutionFileList}
{if $smarty.section.aId.iteration is even}
<tr class="rowA">
{else}
<tr class="rowB">
{/if}
<td><a name="{$solutionFileList[aId].login}" style="color: black; text-decoration: none;">{$solutionFileList[aId].login}</a></td>
<td>{$solutionFileList[aId].surname} {$solutionFileList[aId].firstname}</td>
<td class="center">{$solutionFileList[aId].yearno}/{$solutionFileList[aId].groupno}</td>
<td class="date">{$solutionFileList[aId].timestamp|date_format:"%d.%m.%Y %H:%M:%S"}</td>
<td class="center">{$solutionFileList[aId].efirst} {$solutionFileList[aId].elast}</td>
<td class="center">{$solutionFileList[aId].ufirst} {$solutionFileList[aId].ulast}</td>
<td class="center" title="{$solutionFileList[aId].comment}" alt="{$solutionFileList[aId].comment}">{$solutionFileList[aId].pts}&nbsp;/&nbsp;{$subtask.maxpts}</td>
<td width="40" class="smaller" valign="middle"
  ><a href="?act=edit,solution,{$solutionFileList[aId].id}&order={$order}"><img src="images/famfamfam/award_star_add.png" alt="[udělit body]" title="[obodovat]" width="16" height="16"></a></td>
</tr>
</tr>
{/section}
</table>
<p>
Všechna zadání lze stáhnout jako jeden ZIP soubor <a href="?act=show,file,{$subtask.id}&zip=1">zde</a>.
</p>
<p>
Všechna zadání i řešení v jednom PDF lze stáhnout <a href="?act=show,file,{$subtask.id}&all=1">zde</a>.
</p>
