<h1>Smaz�n� �l�nku</h1>
<form action="ctrl.php?act=realdelete,article,{$article.id}" method="post">
<input type="hidden" name="id" value="{$article.id}">
<input type="hidden" name="returntoparent" value="{$article.returntoparent}">
<p>Opravdu si p�ejete smazat �l�nek s n�zvem <i>{$article.title}</i>?</p>
<input type="submit" value="Ano">
</form>

