<h1>Tuto sekci nelze smazat</h1>
<p>
Sekci s názvem <i>'{$section.title}'</i> není možné momentálnì smazat.
</p>
<p>
Dùvod (èi dùvody) jsou následující:
</p>
<ul>
{if $haveArticles}
<li>Sekce obsahuje èlánky.</li>
{/if}
{if $haveFiles}
<li>Sekce obsahuje odkazy na soubory.</li>
{/if}
{if $noPermission}
<li>Ke smazání sekce nemáte dostateèná oprávnìní.</li>
{/if}
</ul>
<form action="?act=show,section,{$section.id}" method="post">
<input type="submit" value="Zpìt na sekci">
</form>

