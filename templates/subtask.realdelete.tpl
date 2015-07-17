<p>
Dílčí úloha <i>{$subtask.title}</i> z předmětu <i>{$lecture.title}</i> ({$lecture.code})
byla nenávratně smazána.
</p>
<form action="?act=admin,subtask,{$lecture.id}" method="post">
<input type="submit" value="Pokračovat">
</form>
