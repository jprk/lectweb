<h1>Smazání úlohy</h1>
<form action="?act=realdelete,task,{$task.id}" method="post">
<input type="hidden" name="id" value="{$task.id}">
<p>
Opravdu si přejete smazat úlohu z předmětu <i>{$lecture.title}</i> ({$lecture.code})
nazvanou <i>{$task.title}</i>?
</p>
<input type="submit" value="Ano">
</form>
