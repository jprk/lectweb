<form action="?act=save,user,{$user.id}" method="post">
<input type="hidden" name="id" value="{$user.id}">
<table class="admintable" border="0" cellpadding="2" cellspacing="1">
<tr class="rowA">
<td class="itemtitle">Jméno</td>
<td><input type="text" name="firstname" maxlength="255" size="30" value="{$user.firstname}"></td>
</tr>
<tr class="rowB">
<td class="itemtitle">Příjmení</td>
<td><input type="text" name="surname" maxlength="255" size="50" value="{$user.surname}"></td>
</tr>
<tr class="rowA">
<td class="itemtitle">E-mail</td>
<td><input type="text" name="email" maxlength="255" size="24" value="{$user.email}"></td>
</tr>
<tr class="rowB">
<td class="itemtitle">Login</td>
<td><input type="text" name="login" maxlength="32" size="32" value="{$user.login}"></td>
</tr>
<tr class="rowA">
<td class="itemtitle">Heslo</td>
{* Do not change the maxlength without changing the length of the password
   mask string in UserBean.class.php! *}
<td
  ><input type="password" name="pass1" maxlength="32" size="32" value="{$user.password}"
  ><br/><input type="password" name="pass2" maxlength="32" size="32" value="{$user.password}"
  ><br/><small>Heslo je třeba zadat dvakrát. Nepřejete-li si heslo změnit, 
  ponechte obě políčka tak, jak jsou.</small>
</td>
</tr>
<tr class="rowB">
<td class="itemtitle">Role</td>
<td>
<select name="role" style="width: 100%; font-size: 8pt;">
{html_options options=$roles selected=$user.role}
</select>
</td>
</tr>
<tr class="rowA">
<td>&nbsp;</td>
<td>
<input type="submit" value="Uložit">
<input type="reset" value="Vymazat">
</td>
</tr>
</table>
</form>
