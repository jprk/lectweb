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
<p>
<table bgcolor="black" border="0" cellpadding="4" cellspacing="1">
<tr bgcolor="white">
<td><strong>Den:</strong></td>
<td>{$exercise.day.name}</td>
</tr>
<tr bgcolor="white">
<td><strong>Hodina:</strong></td>
<td>{$exercise.from|date_format:"%H:%M"}&nbsp;-&nbsp;{$exercise.to|date_format:"%H:%M"}</td>
</tr>
<tr bgcolor="white">
<td><strong>Místnost:</strong></td>
<td>{$exercise.room}</td>
</tr>
</table>
</p>
<h2>Cvičící</h2>
<p>
{if $lecturer.surname}
<table bgcolor="black"  border="0" cellpadding="4" cellspacing="1">
<tr bgcolor="white">
<td><strong>Jméno:</strong></td>
<td>{$lecturer.firstname} {$lecturer.surname}</td>
</tr>
<tr bgcolor="white">
<td><strong>E-mail:</strong></td>
<td><a href="mailto:{$lecturer.email}">{$lecturer.email}</a></td>
</tr>
<tr bgcolor="white">
<td><strong>Místnost:</strong></td>
<td>{$lecturer.room}</td>
</tr>
</table>
{else}
Cvičení nemá přiřazeno určitého cvičícího. S případnými dotazy se prosím
obracejte na přednášejícího.
{/if}
</p>
<h2>Seznam studentů</h2>
{if $studentList}
<table bgcolor="black" style="border: 1px solid black;" border="0" cellpadding="4" cellspacing="1">
<tr class="rowA">
{if $exercise.displaynames}
{* Display full student names as it was explicitely requested. *}
<th style="width: 6em; text-align: left; border-bottom: 1px solid black;">Příjmení</th>
<th style="width: 5em; text-align: left; border-bottom: 1px solid black;">Jméno</th>
<th style="width: 2em; border-bottom: 1px solid black; border-right: 1px solid black;">Ročník / skupina</th>
{else}
{* Do not show names here, it could be considered as a violation of law. *}
<th style="border-bottom: 1px solid black; border-right: 1px solid black;">ID studenta</th>
{/if}
{section name=subtaskPos loop=$subtaskList}
<th class="smaller" style="width: 4em; border-bottom: 1px solid black;" title="{$subtaskList[subtaskPos].title}">{$subtaskList[subtaskPos].ttitle}</th>
{/section}
{section name=taskPos loop=$taskList}
{if $smarty.section.taskPos.first}
{assign var="tskBorder" value=" border-left: 1px solid black;"}
{else}
{assign var="tskBorder" value=""}
{/if}
<th class="smaller" style="width: 4em; border-bottom: 1px solid black;{$tskBorder}">{throt text=$taskList[taskPos].title}</th>
{/section}
<th style="width: 5em; border-bottom: 1px solid black; border-left: 1px solid black;">{throt text="Body ke zkoušce"}</th>
<th style="width: 5em; border-bottom: 1px solid black; border-left: 1px solid black;">Celkem</th>
{if $evaluation.do_grades}
{assign var="evalHdr" value="Známka"}
{else}
{assign var="evalHdr" value="Zápočet"}
{/if}
<th style="width: 5em; border-bottom: 1px solid black; border-left: 1px solid black;">{$evalHdr}</th>
</tr>
{* ---------- end header ---------- *}
{section name=studentPos loop=$studentList}
{if $smarty.section.studentPos.iteration is even}
<tr class="rowA" onmouseover="roll(this);" onmouseout="rollback(this);">
{else}
<tr class="rowB" onmouseover="roll(this);" onmouseout="rollback(this);">
{/if}
{if $exercise.displaynames}
<td>{$studentList[studentPos].surname}</td>
<td>{$studentList[studentPos].firstname}</td>
<td class="center" style="border-right: 1px solid black;">{$studentList[studentPos].yearno}/{$studentList[studentPos].groupno}</td>
{else}
<td class="center" style="border-right: 1px solid black;">{$studentList[studentPos].id|string_format:"%010u"}</td>
{/if}
{section name=subtaskPos loop=$subtaskList}
{if $smarty.section.studentPos.iteration is even}<td class="subtskA">{else}<td class="subtskB">{/if}<span title="{$studentList[studentPos].subpoints[subtaskPos].comment}">{$studentList[studentPos].subpoints[subtaskPos].points}</span></td>
{/section}
{section name=taskPos loop=$taskList}
{if $smarty.section.taskPos.first}
{assign var="tskBorder" value="style=\"border-left: 1px solid black;\""}
{else}
{assign var="tskBorder" value=""}
{/if}
{if $smarty.section.studentPos.iteration is even}<td class="tskA{$studentList[studentPos].taskclass[taskPos]}"{$tskBorder}>{else}<td class="tskB{$studentList[studentPos].taskclass[taskPos]}" {if $smarty.section.taskPos.first} style="border-left: 1px solid black;"{/if}>{/if}{$studentList[studentPos].taskpoints[taskPos]}</td>
{/section}
{if $smarty.section.studentPos.iteration is even}<td style="border-left: 1px solid black;" class="sumA{$studentList[studentPos].sumclass}">{else}<td style="border-left: 1px solid black;" class="sumB{$studentList[studentPos].sumclass}">{/if}{$studentList[studentPos].exmpoints}</td>
{if $smarty.section.studentPos.iteration is even}<td style="border-left: 1px solid black;" class="sumA{$studentList[studentPos].sumclass}">{else}<td style="border-left: 1px solid black;" class="sumB{$studentList[studentPos].sumclass}">{/if}{$studentList[studentPos].sumpoints}</td>
{if $smarty.section.studentPos.iteration is even}<td style="border-left: 1px solid black;" class="sumA{$studentList[studentPos].sumclass}">{else}<td style="border-left: 1px solid black;" class="sumB{$studentList[studentPos].sumclass}">{/if}{$studentList[studentPos].gotcredit}</td>
</tr>
{/section}
<tr class="rowA">
<td {if $exercise.displaynames}colspan="3"{else}colspan="1"{/if} style="border-top: 1px solid black; border-right: 1px solid black;">Průměr</td>
{section name=subtaskPos loop=$subtaskList}
<td class="center" style="border-top: 1px solid black;">{$statData.avgSubtask[subtaskPos]|string_format:"%.2f"}</td>
{/section}
{section name=taskPos loop=$taskList}
{if $smarty.section.taskPos.first}
{assign var="tskBorder" value=" border-left: 1px solid black;"}
{else}
{assign var="tskBorder" value=""}
{/if}
<td class="center" style="border-top: 1px solid black;{$tskBorder}">{$statData.avgTask[taskPos]|string_format:"%.2f"}</td>
{/section}
<td class="sumA" style="border-top: 1px solid black; border-left: 1px solid black;">{$statData.exmAvg|string_format:"%.2f"}</td>
<td class="sumA" style="border-top: 1px solid black; border-left: 1px solid black;">{$statData.average|string_format:"%.2f"}</td>
<td class="sumA" style="border-top: 1px solid black; border-left: 1px solid black;">-</td>
</tr>
<tr class="rowB">
<td {if $exercise.displaynames}colspan="3"{else}colspan="1"{/if} style="border-right: 1px solid black;">Nesplněno</td>
{section name=subtaskPos loop=$subtaskList}
<td class="center">-</td>
{/section}
{section name=taskPos loop=$taskList}
{if $smarty.section.taskPos.first}
{assign var="tskBorder" value="style=\"border-left: 1px solid black;\""}
{else}
{assign var="tskBorder" value=""}
{/if}
<td class="center"{$tskBorder}>{$statData.negTaskCount[taskPos]}</td>
{/section}
<td class="sumB" style="border-left: 1px solid black;">-</td>
<td class="sumB" style="border-left: 1px solid black;">{$statData.negative}</td>
<td class="sumB" style="border-left: 1px solid black;">-</td>
</tr>
</table>
{else}
{if $evaluation.do_grades}
<p>Seznam studentů je prázdný.</p>
{else}
<p>Toto cvičení nemá ještě přiřazen seznam studentů.</p>
{/if}
{/if}
<h2>Minimální počty bodů</h2>
<p>
{section name=taskPos loop=$taskList}
Minimální průchozí počet bodů za skupinu úloh <strong>{$taskList[taskPos].title}</strong>
{if $taskList[taskPos].minpts > 0 }
je <strong>{$taskList[taskPos].minpts}</strong>.
{else}
nebyl stanoven.
{/if}
{/section}
</p>
<p>
{if $evaluation.do_grades}
Minimální počet bodů nutný pro absolvování předmětu je <strong>{$evaluation.pts_E}</strong>.
{else}
Minimální počet bodů nutný pro získání zápočtu je <strong>{$evaluation.pts_E}</strong>.
{/if}