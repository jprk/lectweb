<form action="?act=realdelete,subtask,{$subtask.id}" method="post">
<input type="hidden" name="id" value="{$subtask.id}">
<p>
Opravdu si přejete smazat dílčí úlohu z předmětu <i>{$lecture.title}</i> ({$lecture.code})
nazvanou <i>{$subtask.title}</i>?
</p>
<input type="submit" value="Ano">
</form>
