<h1>Sekce byla vymaz�na</h1>
<p>
Sekce s n�zvem <i>{$section.title}</i> byla z datab�ze vymaz�na.
</p>
{if $section.returntoparent}
<form action="?act=show,section,{$section.parent}" method="post">
<input type="submit" value="Zp�t na rodi�ovskou sekci">
</form>
{else}
<form action="?act=admin,section,42" method="post">
<input type="submit" value="Pokra�ovat na administraci sekc�">
</form>
{/if}
