<h1>Vyhodnocen� smaz�no</h1>
<p>
P�edpis vyhodnocen� �loh z p�edm�tu <i>{$lecture.title}</i> ({$lecture.code})
ve �koln�m roce <i>{$evaluation.schoolyear}</i> nazvan� <i>{$evaluation.title}</i>
byl nen�vratn� vymaz�n.
</p>
<form action="" method="get">
<input type="hidden" name="act" value="admin,evaluation,{$lecture.id}">
<input type="submit" value="Pokra�ovat">
</form>
