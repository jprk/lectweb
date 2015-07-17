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
<h1>Seznam studentů na cvičení</h1>
<p>
<table bgcolor="black" border="0" cellpadding="4" cellspacing="1">
<tr bgcolor="white">
<td><strong>Předmět:</strong></td>
<td>{$lecture.code}</td>
</tr>
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
</p>
<h2>Seznam studentů</h2>
{if $studentList}
<table bgcolor="black" style="border: 1px solid black;" border="0" cellpadding="10" cellspacing="1">
<tr class="rowA">
<th style="width: 1em; text-align: center; border-bottom: 1px solid black;">#</th>
<th style="width: 6em; text-align: left; border-bottom: 1px solid black;">Příjmení</th>
<th style="width: 5em; text-align: left; border-bottom: 1px solid black;">Jméno</th>
<th style="width: 2em; border-bottom: 1px solid black; border-right: 1px solid black;">Ročník / skupina</th>
</tr>
{section name=studentPos loop=$studentList}
{if $smarty.section.studentPos.iteration is even}
<tr class="rowA" onmouseover="roll(this);" onmouseout="rollback(this);">
{else}
<tr class="rowB" onmouseover="roll(this);" onmouseout="rollback(this);">
{/if}
<td class="center">{$smarty.section.studentPos.iteration}</td>
<td>{$studentList[studentPos].surname}</td>
<td>{$studentList[studentPos].firstname}</td>
<td class="center" style="border-right: 1px solid black;">{$studentList[studentPos].yearno}/{$studentList[studentPos].groupno}</td>
</tr>
{/section}
</table>
{else}
<p>Toto cvičení nemá ještě přiřazen seznam studentů.</p>
{/if}
<hr>
<p>
{strip}
{if $isAdmin || $isLecturer}
<a href="?act=admin,exclist,{$lecture.id}">zpět na administraci cvičení</a>
{else}
<a href="?act=show,exclist,{$lecture.id}">zpět na seznam cvičení</a>
{/if}
{/strip}
&nbsp;|&nbsp;<a href="/predmety/msp/">zpět na stránky MSP</a>
</p>
