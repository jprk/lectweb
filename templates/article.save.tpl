<h1>�l�nek byl zm�nen</h1>
<p>
�l�nek s n�zvem <i>{$article.title}</i> byl zm�n�n.
</p>
{if $article.returntoparent}
<form action="?act=show,section,{$article.parent}" method="post">
<input type="submit" value="Zp�t na sekci">
</form>
{else}
<form action="?act=admin,article,42" method="post">
<input type="submit" value="Pokra�ovat v administraci �l�nk�">
</form>
{/if}