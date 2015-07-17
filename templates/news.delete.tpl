<h1>Smazání novinky</h1>
<form action="?act=realdelete,news,{$news.id}" method="post">
<input type="hidden" name="id" value="{$news.id}">
<p>Opravdu si přejete smazat novinku s titulkem '<i>{$news.title}</i>' a textem '<i>{$news.text}</i>'?
</p>
<input type="submit" value="Ano">
</form>
