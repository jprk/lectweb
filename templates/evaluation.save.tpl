<p>
Data o vyhodnocení úkolů v předmětu <i>{$lecture.title}</i> ({$lecture.code})
ve školním roce <i>{$evaluation.schoolyear}</i> nazvaná <i>{$evaluation.title}</i> byla změněna.
</p>
<form action="" method="get">
<input type="hidden" name="act" value="admin,evaluation,{$lecture.id}">
<input type="submit" value="Pokračovat">
</form>
