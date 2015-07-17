<p>
Zvolte školní rok, jehož výsledky si přejete zobrazit:
</p>
<form name="yearform" action="?act=save,schoolyear,{$lecture.id}" method="post">
<table class="admintable" border="0" cellpadding="2" cellspacing="1">
<tr class="rowA">
<td>Školní rok:</td>
<td>
<select name="schoolyear_start" style="width: 100%;">
{html_options options=$yearSelect selected=$schoolyear_start}
</select>
</td>
</tr>
<tr class="rowB">
<td>&nbsp;</td>
<td>
<input type="submit" value="Přepnout na zvolený rok">
<input type="reset" value="Vymazat">
</td>
</tr>
</table>
