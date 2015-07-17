<h1>Èlánek byl zmìnen</h1>
<p>
Èlánek s názvem <i>{$article.title}</i> byl zmìnìn.
</p>
{if $article.returntoparent}
<form action="?act=show,section,{$article.parent}" method="post">
<input type="submit" value="Zpìt na sekci">
</form>
{else}
<form action="?act=admin,article,42" method="post">
<input type="submit" value="Pokraèovat v administraci èlánkù">
</form>
{/if}