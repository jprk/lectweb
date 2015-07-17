<h1>Smazání poznámky</h1>
<form action="?act=realdelete,note,{$note.id}" method="post">
<input type="hidden" name="id" value="{$note.id}">
<p>Opravdu si přejete smazat poznámku s textem '<i>{$note.text}</i>' pořízenou
{$note.date|date_format:"%d.%m.%Y"}?
</p>
<input type="submit" value="Ano">
</form>
