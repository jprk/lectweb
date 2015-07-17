<h1>Vyhodnocení smazáno</h1>
<p>
Pøedpis vyhodnocení úloh z pøedmìtu <i>{$lecture.title}</i> ({$lecture.code})
ve školním roce <i>{$evaluation.schoolyear}</i> nazvaný <i>{$evaluation.title}</i>
byl nenávratnì vymazán.
</p>
<form action="" method="get">
<input type="hidden" name="act" value="admin,evaluation,{$lecture.id}">
<input type="submit" value="Pokraèovat">
</form>
