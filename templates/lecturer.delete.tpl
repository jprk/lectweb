<h1>Smazání učitele</h1>
<form action="?act=realdelete,lecturer,{$lecturer.id}" method="post">
<input type="hidden" name="id" value="{$lecturer.id}">
<p>
Opravdu si přejete smazat údaje o učiteli <i>{$lecturer.firstname} {$lecturer.surname}</i>?
</p>
<input type="submit" value="Ano">
</form>
