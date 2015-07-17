<h1>Úloha smazána</h1>
<p>
Úloha <i>{$task.title}</i> z předmětu <i>{$lecture.title}</i> ({$lecture.code})
byla nenávratně smazána.
</p>
<form action="" method="get">
<input type="hidden" name="act" value="admin,task,{$lecture.id}">
<input type="submit" value="Pokračovat">
</form>
