<h1>Smaz�n� studenta</h1>
<form action="?act=realdelete,student,{$student.id}" method="post">
<input type="hidden" name="id" value="{$student.id}">
<p>
Opravdu si p�ejete smazat �daje o studentovi <i>{$student.firstname} {$student.surname}</i>?
</p>
<input type="submit" value="Ano">
</form>
