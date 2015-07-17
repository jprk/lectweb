<p>
Body studentů navštěvujících předmět {$lecture.title} ({$lecture.code})
byly uloženy do databáze a zámek bodového hodnocení byl uvolněn.
</p>
<form action="?act=edit,points,{$lecture.id}&type=lec" method="post">
<input type="submit" value="Zpět na body studentů tohoto předmětu">
</form>
<form action="?act=admin,exclist,{$lecture.id}" method="post">
<input type="submit" value="Zpět na administraci cvičení">
</form>
