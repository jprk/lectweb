<p>
Hodnocení úlohy číslo {$subtask.id} s názvem <i>{$subtask.title}</i>
za studenta <i>{$student.firstname} {$student.surname}</i> (login {$student.login})
v souboru <i>{$file.origfname}</i> bylo uloženo do systému.
</p>
<form action="#{$student.login}" method="get">
<input type="hidden" name="act" value="show,solution,{$subtask.id}">
<input type="hidden" name="order" value="{$order}">
<input type="submit" value="Zpět na seznam odevzdaných řešení úlohy {$subtask.ttitle}">
</form>
