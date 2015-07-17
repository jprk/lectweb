<h1>Smazání uživatele</h1>
<form action="ctrl.php?act=realdelete,user,{$user.id}" method="post">
<input type="hidden" name="id" value="{$user.id}">
<p>
Opravdu si přejete smazat uživatele <i>{$user.firstname} {$user.surname}</i>
(login: <i>{$user.login}</i>, id {$user.id})?
</p>
<input type="submit" value="Ano">
</form>
