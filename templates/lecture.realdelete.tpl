<p>
Záznamy předmětu <i>{$lectureInfo.title} ({$lectureInfo.code})</i> byly nenávratně smazány.
</p>
{* lecture.id is the id of currently active lecture, not of the edited one *}
<form action="" method="get">
<input type="hidden" name="act" value="admin,lecture,{$lecture.id}">
<input type="submit" value="Pokračovat">
</form>
