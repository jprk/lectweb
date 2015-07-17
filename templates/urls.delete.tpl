<form action="?act=realdelete,urls,{$url.id}" method="post">
<input type="hidden" name="id" value="{$url.id}">
<p>Opravdu si přejete smazat odkaz na <i>{$url.url}</i> s titulkem
<i>{$url.title}</i>?
</p>
<input type="submit" value="Ano">
</form>
