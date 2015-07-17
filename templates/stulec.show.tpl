{literal}
<script type="text/javascript">
function roll ( elem )
{
    if ( elem.className == 'rowA' )
    {
        elem.className = 'rowAh';
    }
    else
    {
        elem.className = 'rowBh';
    }
}

function rollback ( elem )
{
    if ( elem.className == 'rowAh' )
    {
        elem.className = 'rowA';
    }
    else
    {
        elem.className = 'rowB';
    }
}
</script>
{/literal}
{if $studentList}
<table class="admintable" style="border: 1px solid black;" border="0" cellpadding="4" cellspacing="1">
<thead class="rowA">
<th style="width: 6em; text-align: left; border-bottom: 1px solid black;">Příjmení</th>
<th style="width: 5em; text-align: left; border-bottom: 1px solid black;">Jméno</th>
<th style="width: 2em; border-bottom: 1px solid black;">{throt text="Ročník / skupina"}</th>
{section name=subtaskPos loop=$subtaskList}
<th class="smaller" style="width: 1em; border-bottom: 1px solid black;">{throt text=$subtaskList[subtaskPos].title}</th>
{/section}
{section name=taskPos loop=$taskList}
{if $smarty.section.taskPos.first}
{assign var="tskBorder" value=" border-left: 1px solid black;"}
{else}
{assign var="tskBorder" value=""}
{/if}
<th class="smaller" style="width: 3em; border-bottom: 1px solid black;{$tskBorder}">{throt text=$taskList[taskPos].title}</th>
{/section}
<th class="smaller" style="width: 4em; border-bottom: 1px solid black; border-left: 1px solid black;">{throt text="Celkem"}</th>
<th class="smaller" style="width: 2em; border-bottom: 1px solid black; border-left: 1px solid black;">{throt text="Body ke zkoušce"}</th>
{if $evaluation.do_grades}
{assign var="evalHdr" value="Známka"}
{else}
{assign var="evalHdr" value="Zápočet"}
{/if}
<th class="smaller" style="width: 5ex; border-bottom: 1px solid black; border-left: 1px solid black;">{throt text=$evalHdr}</th>
<th class="smaller" style="width: 6em; border-bottom: 1px solid black;">Datum zápočtu</th>
</thead>
{* ---------- end header ---------- *}
{section name=studentPos loop=$studentList}
{if $smarty.section.studentPos.iteration is even}
<tr class="rowA" onmouseover="roll(this);" onmouseout="rollback(this);">
{else}
<tr class="rowB" onmouseover="roll(this);" onmouseout="rollback(this);">
{/if}
<td title="dbid {$studentList[studentPos].dbid}"><strong>{$studentList[studentPos].surname}</strong></td>
<td title="id {$studentList[studentPos].id}"><strong>{$studentList[studentPos].firstname}</strong></td>
<td class="center">{$studentList[studentPos].yearno}/{$studentList[studentPos].groupno}</td>
{section name=subtaskPos loop=$subtaskList}
{if $smarty.section.studentPos.iteration is even}<td class="subtskA">{else}<td class="subtskB">{/if}<span title="{$studentList[studentPos].subpoints[subtaskPos].comment}">{$studentList[studentPos].subpoints[subtaskPos].points}</span></td>
{/section}
{section name=taskPos loop=$taskList}
{if $smarty.section.taskPos.first}
{assign var="tskBorder" value="style=\"border-left: 1px solid black;\""}
{else}
{assign var="tskBorder" value=""}
{/if}
{if $smarty.section.studentPos.iteration is even}<td class="tskA{$studentList[studentPos].taskclass[taskPos]}"{$tskBorder}>{else}<td class="tskB{$studentList[studentPos].taskclass[taskPos]}"{$tskBorder}>{/if}{$studentList[studentPos].taskpoints[taskPos]}</td>
{/section}
{* ---------- end tasks ---------- *}
{if $smarty.section.studentPos.iteration is even}<td style="border-left: 1px solid black;" class="sumA{$studentList[studentPos].sumclass}">{else}<td style="border-left: 1px solid black;" class="sumB{$studentList[studentPos].sumclass}">{/if}{$studentList[studentPos].sumpoints}</td>
{if $smarty.section.studentPos.iteration is even}<td style="border-left: 1px solid black;" class="sumA{$studentList[studentPos].sumclass}">{else}<td style="border-left: 1px solid black;" class="sumB{$studentList[studentPos].sumclass}">{/if}<strong>{$studentList[studentPos].exmpoints}</strong></td>
{if $smarty.section.studentPos.iteration is even}<td style="border-left: 1px solid black;" class="sumA{$studentList[studentPos].sumclass}">{else}<td style="border-left: 1px solid black;" class="sumB{$studentList[studentPos].sumclass}">{/if}{$studentList[studentPos].gotcredit}</td>
<td>&nbsp;</td>
</tr>
{/section}
<tr class="rowA">
<td colspan="3" style="border-top: 1px solid black;">Průměr</td>
{section name=subtaskPos loop=$subtaskList}
<td class="sumA" style="border-top: 1px solid black;">{$statData.avgSubtask[subtaskPos]|string_format:"%.2f"}</td>
{/section}
{section name=taskPos loop=$taskList}
{if $smarty.section.taskPos.first}
{assign var="tskBorder" value=" border-left: 1px solid black;"}
{else}
{assign var="tskBorder" value=""}
{/if}
<td class="sumA" style="border-top: 1px solid black;{$tskBorder}">{$statData.avgTask[taskPos]|string_format:"%.2f"}</td>
{/section}
<td class="sumA" style="border-top: 1px solid black; border-left: 1px solid black;">{$statData.average|string_format:"%.2f"}</td>
<td class="sumA" style="border-top: 1px solid black; border-left: 1px solid black;">{$statData.exmAvg|string_format:"%.2f"}</td>
<td class="sumA" style="border-top: 1px solid black; border-left: 1px solid black;">-</td>
<td class="sumA" style="border-top: 1px solid black;">---</td>
</tr>
<tr class="rowB">
<td colspan="3">Nesplněno</td>
{section name=subtaskPos loop=$subtaskList}
<td class="sumB">-</td>
{/section}
{section name=taskPos loop=$taskList}
{if $smarty.section.taskPos.first}
{assign var="tskBorder" value="style=\"border-left: 1px solid black;\""}
{else}
{assign var="tskBorder" value=""}
{/if}
<td class="sumB"{$tskBorder}>{$statData.negTaskCount[taskPos]}</td>
{/section}
<td class="sumB" style="border-left: 1px solid black;">{$statData.negative}</td>
<td class="sumB" style="border-left: 1px solid black;">-</td>
<td class="sumB" style="border-left: 1px solid black;">-</td>
<td class="sumB">---</td>
</tr>
<tr class="rowB">
<td colspan="3">Účast</td>
{section name=subtaskPos loop=$subtaskList}
<td class="sumB">{$statData.parSubCount[subtaskPos]}</td>
{/section}
{section name=taskPos loop=$taskList}
{if $smarty.section.taskPos.first}
{assign var="tskBorder" value="style=\"border-left: 1px solid black;\""}
{else}
{assign var="tskBorder" value=""}
{/if}
<td class="sumA"{$tskBorder}>{$statData.parTaskCount[taskPos]}</td>
{/section}
<td class="sumA" style="border-left: 1px solid black;">-</td>
<td class="sumA" style="border-left: 1px solid black;">-</td>
<td class="sumA" style="border-left: 1px solid black;">-</td>
<td class="sumA">---</td>
</tr>
</table>
{else}
<p>Seznam studentů je prázdný.</p>
{/if}
<h2>Minimální počty bodů</h2>
<p>
{section name=taskPos loop=$taskList}
Minimální počet bodů za <strong>{$taskList[taskPos].title}</strong>
{if $taskList[taskPos].minpts > 0 }
je <strong>{$taskList[taskPos].minpts}</strong>.
{else}
nebyl stanoven.
{/if}
{/section}
</p>
<p>
Minimální počet bodů pro získání zápočtu je <strong>{$evaluation.pts_E}</strong>.
</p>
