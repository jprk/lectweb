<p>
Bodové ohodnocení studentů navštěvujících cvičení z&nbsp;předmětu
{$lecture.title} ({$lecture.code}) v&nbsp;{$exercise.day.name}
od {$exercise.from|date_format:"%H:%M"} do {$exercise.to|date_format:"%H:%M"}
bylo změneno.
</p>
<form action="?act=edit,points,{$exercise.id}&type=exc" method="post">
<input type="submit" value="Zpět na body studentů tohoto cvičení">
</form>
<form action="?act=admin,exclist,{$exercise.lecture_id}" method="post">
<input type="submit" value="Zpět na administraci cvičení">
</form>
