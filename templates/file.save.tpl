<p>
Soubor <i>{$file.origfname}</i> s popisem <i>{$file.description}</i> byl změněn.
</p>
{if $file.returntoparent}
<form action="?act=show,section,{$file.objid}" method="post">
<input type="submit" value="Zpět na sekci">
</form>
{else}
<form action="?act=admin,file,42" method="post">
<input type="submit" value="Pokračovat v administraci souborů">
</form>
{/if}
