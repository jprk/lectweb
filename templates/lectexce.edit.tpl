<h1>Vazba u�itel-cvi�en� pro p�edm�t {$lecture.code}</h1>
<form action="?act=save,stuexe,42" method="post">
<table border="1" cellpadding="4" cellspacing="1">
<tr>
<th>Cvi��c�</th>
{section name=exercisePos loop=$exerciseList}
<th>{$exerciseList[exercisePos].day}<br>&nbsp;&nbsp;<small>{$exerciseList[exercisePos].from|date_format:"%H:%M"}-&nbsp;<br>&nbsp;-{$exerciseList[exercisePos].to|date_format:"%H:%M"}&nbsp;&nbsp;</small></th>
{/section}
</tr>
{section name=lPos loop=$lecturerList}
{if $smarty.section.lPos.iteration is even}
<tr class="rowA">
{else}
<tr class="rowB">
{/if}
<td>{$lecturerList[lPos].firstname} {$lecturerList[lPos].surname}</td>
{section name=ePos loop=$exerciseList}
<td class="center"><input type="radio" name="le_rel[{$exerciseList[ePos].id}]" value="{$lecturerList[lPos].id}"{$lecturerList[lPos].checked[ePos]}}></td>
{/section}
</tr>
{/section}
<tr><td colspan="12" class="center"><input type="submit" value="Ulo�it"></td></tr>
</table>
</form>
</p>
<hr size="1" color="black"/>
<p>
|&nbsp;&nbsp;<a href="?act=admin,exclist,1">zp�t na seznam cvi�en�</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="/predmety/msp/">zp�t na str�nky MSP</a>
</p>
