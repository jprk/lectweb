<form name="evaluationForm" action="?act=save,evaluation,{$evaluation.id}" method="post">
<input type="hidden" name="id"         value="{$evaluation.id}">
<input type="hidden" name="lecture_id" value="{$lecture.id}">
<table class="admintable" border="0" cellpadding="2" cellspacing="1">
<tr class="rowA">
<td class="itemtitle">Název</td>
<td><input type="text" name="title" maxlength="255" style="width: 100%;" value="{$evaluation.title}"></td>
</tr>
<tr class="rowB">
<td class="itemtitle">Předmět</td>
<td width="70%">
<input type="text" readonly="readonly" style="background-color: #fbfbf0; width: 100%;" value="{$lecture.code} - {$lecture.title}">
</td>
</tr>
<tr class="rowA">
<td class="itemtitle">Školní rok</td>
<td>
<select name="year" style="width: 100%; font-size: 8pt;">
{html_options options=$yearSelect selected=$evaluation.year}
</select>
</td>
</tr>
<tr class="rowB">
<td class="itemtitle">Známkování?</td>
<td>
{html_radios name="do_grades" options=$yesno selected=$evaluation.do_grades}
</td>
</tr>
<tr class="rowA">
<td class="itemtitle">Známka A - "výborně" od</td>
<td><input type="text" name="pts_A" maxlength="2" size="2" value="{$evaluation.pts_A}"> bodů</td>
</tr>
<tr class="rowB">
<td class="itemtitle">Známka B - "velmi dobře" od</td>
<td><input type="text" name="pts_B" maxlength="2" size="2" value="{$evaluation.pts_B}"> bodů</td>
</tr>
<tr class="rowA">
<td class="itemtitle">Známka C - "dobře" od</td>
<td><input type="text" name="pts_C" maxlength="2" size="2" value="{$evaluation.pts_C}"> bodů</td>
</tr>
<tr class="rowB">
<td class="itemtitle">Známka D - "uspokojivě" od</td>
<td><input type="text" name="pts_D" maxlength="2" size="2" value="{$evaluation.pts_D}"> bodů</td>
</tr>
<tr class="rowA">
<td class="itemtitle">Známka E - "dostatečně" od / minimum bodů</td>
<td><input type="text" name="pts_E" maxlength="2" size="2" value="{$evaluation.pts_E}"> bodů</td>
</tr>
<tr class="submitrow">
<td>&nbsp;</td>
<td>
<input type="submit" value="Uložit">
<input type="reset" value="Vymazat">
</td>
</tr>
</table>
</form>
