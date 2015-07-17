<p>
Data úlohy <i>{$task.title}</i> z předmětu <i>{$lecture.title}</i> ({$lecture.code})
byla změněna.
</p>
<form action="" method="get">
<input type="hidden" name="act" value="admin,task,{$lecture.id}">
<input type="submit" value="Pokračovat">
</form>
