<p>
Vítejte v neveřejné části webové prezentace předmětu {$lecture.title}. Na této
stránce máte možnost odevzdávat samostatně vypracované úlohy
(pokud je k tomu ten správný čas) a podívat se na bodové ohodnocení jak vašich
samostatných úloh, tak i testů a aktivity na cvičeních.
</p>
<p>
Pokud přejedete myší přes bodové hodnocení úloh, může se se otevřít tooltip
s poznámkou k danému bodovému ohodnocení. Pokud se nic nezobrazí, poznámka
je patrně prázdná. 
</p>
<h2>Údaje o Vás</h2>
<p>
{if $student.id > 100000 }
ČVUT id: {$student.id}<br/>
{/if}
id (pouze na zolotarev.fd.cvut.cz): {$student.twistid|string_format:"%010u"}<br/>
uživatelské jméno: {$student.login}<br/>
jméno a příjmení: {$student.firstname} {$student.surname}<br/>
email: {$student.email}
</p>
<h2>Samostatné úlohy</h2>
<p>
Je-li úloha v tabulce označena jako aktivní, je přes ikonu
<img src="images/famfamfam/report_add.png" alt="[zadání/odevzdat]" title="[zadání/odevzdat]" width="16" height="16">
či přímo proklikem odkazu "aktivní" přístupné zadání a odevzdávání úloh.  
</p>
<table class="admintable" border="0" cellpadding="2" cellspacing="1">
<tr class="newobject">
<th>Název úlohy</th>
<th colspan="2" style="width: 34ex">Odevzdání od-do</th>
<th>Aktivní?</th>
<th>&nbsp;</th>
<th>Odevzdáno?</th>
<th>Body/Max</th>
</tr>
{section name=aId loop=$studentSubtaskList}
{if $smarty.section.aId.iteration is even}
<tr class="rowA">
{else}
<tr class="rowB">
{/if}
<td>&nbsp;{$studentSubtaskList[aId].title}</td>
<td class="date" style="width: 17ex">{$studentSubtaskList[aId].datefrom|date_format:"%d.%m.%Y %H:%M"}</td>
<td class="date" style="width: 17ex">{$studentSubtaskList[aId].dateto|date_format:"%d.%m.%Y %H:%M"}</td>
<td class="center">
{if $studentSubtaskList[aId].active}
<a href="?act=show,subtask,{$studentSubtaskList[aId].id}">aktivní</a>
{else}
neaktivní
{/if}
</td>
<td class="center" style="width: 20px;">
{if $studentSubtaskList[aId].active}
<a href="?act=show,subtask,{$studentSubtaskList[aId].id}"><img src="images/famfamfam/report_add.png" alt="[zadání/odevzdat]" title="[zadání/odevzdat]" width="16" height="16"></a>
{else}
<img src="images/famfamfam/report.png" alt="[neaktivní]" title="neaktivní" width="16" height="16"></a>
{/if}
</td>
<td class="center">{if $studentSubtaskList[aId].haveSolution == 1}ano{else}ne{/if}</td>
<td class="center" title="{$studentSubtaskList[aId].comment}">{$studentSubtaskList[aId].pts}&nbsp;/&nbsp;{$studentSubtaskList[aId].maxpts}</td>
</tr>
{/section}
</table>

<h2>Výsledky</h2>
<table class="admintable" border="0" cellpadding="2" cellspacing="1">
<tr class="newobject">
{section name=subtaskPos loop=$subtaskList}
<th class="smaller" style="width: 4em;" title="{$subtaskList[subtaskPos].title}">{$subtaskList[subtaskPos].ttitle}</th>
{/section}
{section name=taskPos loop=$taskList}
<th class="smaller" style="width: 4em;">{$taskList[taskPos].title}</th>
{/section}
<th style="width: 5em;">Body ke zkoušce</th>
<th style="width: 5em;">Celkem</th>
{if $evaluation.do_grades}
{assign var="evalHdr" value="Známka"}
{else}
{assign var="evalHdr" value="Zápočet"}
{/if}
<th style="width: 5em;">{$evalHdr}</th>
</tr>
<tr class="rowA">
{section name=sPos loop=$studentList[0].subpoints}
<td class="subtskA" title="{$studentList[0].subpoints[sPos].comment}">{$studentList[0].subpoints[sPos].points}</td>
{/section}
{section name=taskPos loop=$taskList}
<td class="tskA{$studentList[0].taskclass[taskPos]}">{$studentList[0].taskpoints[taskPos]}</td>
{/section}
<td class="sumA{$studentList[0].sumclass}">{$studentList[0].exmpoints}</td>
<td class="sumA{$studentList[0].sumclass}">{$studentList[0].sumpoints}</td>
<td class="sumA{$studentList[0].sumclass}">{$studentList[0].gotcredit}</td>
</tr>
</table>

<h2>Minimální počty bodů</h2>
<p>
{section name=taskPos loop=$taskList}
Minimální počet bodů za <strong>{$taskList[taskPos].title}</strong>
{if $taskList[taskPos].minpts > 0 }
je <strong>{$taskList[taskPos].minpts}</strong>.
{else}
nebyl stanoven.
{/if}<br>
{/section}
</p>
<p>
{if $evaluation.do_grades}
Minimální počet bodů nutný pro absolvování předmětu je <strong>{$evaluation.pts_E}</strong>.
{else}
Minimální počet bodů nutný pro získání zápočtu je <strong>{$evaluation.pts_E}</strong>.
{/if}
</p>
