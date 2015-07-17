<p>
Údaje o předmětu <i>{$lectureInfo.title} ({$lectureInfo.code})</i> byly změněny.
</p>
<form action="" method="get">
{* lecture.id is the id of currently active lecture, not of the edited one *}
<input type="hidden" name="act" value="admin,lecture,{$lecture.id}">
<input type="submit" value="Pokračovat">
</form>
