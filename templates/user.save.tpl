<p>
Změněné údaje o uživateli <i>{$user.firstname} {$user.surname}</i>
(login: <i>{$user.login}</i>, id {$user.id}) byly uloženy do databáze.
</p>
{if $passchanged}
<p>
Uživateli bylo změněno heslo, na adresu <tt>{$user.email}</tt> byl odeslán
informační e-mail.
</p>
{/if}
<form action="" method="get">
<input type="hidden" name="act" value="admin,user,42">
<input type="submit" value="Pokračovat">
</form>
