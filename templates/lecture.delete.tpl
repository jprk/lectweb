<form action="?act=realdelete,lecture,{$lectureInfo.id}" method="post">
<input type="hidden" name="id" value="{$lectureInfo.id}">
<p>
Opravdu si přejete smazat záznamy předmětu <i>{$lectureInfo.title} ({$lectureInfo.code})</i>?
</p>
<input type="submit" value="Ano">
</form>
