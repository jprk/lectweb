<h1>Chyba</h1>
<form action="?act=admin,user,{$user.id}" method="post">
<input type="hidden" name="id" value="{$user.id}">
<p>
Při zpracování požadavku došlo k chybě:
<em>Zadali jste dvakrát nové heslo, ale hesla se neshodují.</em>
Heslo zůstává původní. Pokud jste změnili ještě další údaje,
tyto údaje byly v databázi změněny.
</p>
<input type="submit" value="Ano">
</form>
