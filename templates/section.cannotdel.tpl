<h1>Tuto sekci nelze smazat</h1>
<p>
Sekci s n�zvem <i>'{$section.title}'</i> nen� mo�n� moment�ln� smazat.
</p>
<p>
D�vod (�i d�vody) jsou n�sleduj�c�:
</p>
<ul>
{if $haveArticles}
<li>Sekce obsahuje �l�nky.</li>
{/if}
{if $haveFiles}
<li>Sekce obsahuje odkazy na soubory.</li>
{/if}
{if $noPermission}
<li>Ke smaz�n� sekce nem�te dostate�n� opr�vn�n�.</li>
{/if}
</ul>
<form action="?act=show,section,{$section.id}" method="post">
<input type="submit" value="Zp�t na sekci">
</form>

