<h1>Chyba</h1>
<form action="?act=admin,user,{$user.id}" method="post">
<input type="hidden" name="id" value="{$user.id}">
<p>
Při zpracování požadavku došlo k chybě:
<em>Heslo nemůže být prázdné.</em>
</p>
<input type="submit" value="Ano">
</form>
