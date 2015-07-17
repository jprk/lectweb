<h1>Smazání vyhodnocení</h1>
<form action="?act=realdelete,evaluation,{$evaluation.id}" method="post">
<input type="hidden" name="id" value="{$evaluation.id}">
<p>
Opravdu si přejete vymazat předpis vyhodnocení úloh z předmětu <i>{$lecture.title}</i> ({$lecture.code})
ve školním roce <i>{$evaluation.schoolyear}</i> nazvaný <i>{$evaluation.title}</i>?
</p>
<input type="submit" value="Ano">
</form>
