<p>
Cvičení z předmětu <i>{$lecture.title}</i> ({$lecture.code})
cvičené v <i>{$exercise.day.name}</i>, od <i>{$exercise.from}</i> do <i>{$exercise.to}</i>
v místnosti <i>{$exercise.room}</i>, jehož cvičícím je <i>{$lecturer.firstname} {$lecturer.surname}</i>
bylo smazáno z databáze. 
</p>
<form action="?act=admin,exercise,{$exercise.lecture_id}" method="post">
<input type="submit" value="Pokračovat">
</form>
