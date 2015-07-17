<h1>Vyhodnocení smazáno</h1>
<p>
Předpis vyhodnocení úloh z předmětu <i>{$lecture.title}</i> ({$lecture.code})
ve školním roce <i>{$evaluation.schoolyear}</i> nazvaný <i>{$evaluation.title}</i>
byl nenávratně vymazán.
</p>
<form action="" method="get">
<input type="hidden" name="act" value="admin,evaluation,{$lecture.id}">
<input type="submit" value="Pokračovat">
</form>
