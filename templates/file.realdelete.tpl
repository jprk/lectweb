<p>
Soubor <i>{$file.origfname}</i> s popisem <i>{$file.description}</i>
byl nenávratně vymazán.
</p>
{if $file.returntoparent}
<form action="?act=show,{$objtypestring},{$file.objid}" method="post">
<input type="submit" value="Zpět na nadřazený objekt">
</form>
{else}
<form action="?act=admin,file,42" method="post">
<input type="submit" value="Pokračovat ve správě souborů">
</form>
{/if}
