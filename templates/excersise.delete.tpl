<form action="?act=realdelete,exercise,{$exercise.id}" method="post">
<input type="hidden" name="id" value="{$exercise.id}">
<p>
Opravdu si přejete smazat cvičení z předmětu <i>{$lecture.title}</i> ({$lecture.code})
cvičené v <i>{$exercise.day.name}</i>, od <i>{$exercise.from}</i> do <i>{$exercise.to}</i>
v místnosti <i>{$exercise.room}</i>, jehož cvičícím je <i>{$lecturer.firstname} {$lecturer.surname}</i>?
</p>
<input type="submit" value="Ano">
</form>
