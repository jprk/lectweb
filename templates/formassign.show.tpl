{if $formassignment.catalogue}
<p>
V adresáři s generovanými soubory zadání byl vytvořen soubor s řešeními.
</p>
{elseif $formassignment.regenerate}
<p>
Zadání pro studenty byla ze zdrojových souborů vygenerována znovu,
přiřazení úkolů se nezměnilo.
</p>
{elseif $formassignment.copy}
<p>
Zkopírováno přiřazení úloh z úlohy {$copysubtask.title} a vygenerována
zadání pro studenty.
</p>
{elseif $formassignment.onlynew}
<p>
Byla vygenerována zadání pro následující studenty:
</p>
<ul>
{section name=sId loop=$studentList}
<li>{$studentList[sId].surname} {$studentList[sId].firstname} 
    ({$studentList[sId].yearno}/{$studentList[sId].groupno})</li>
{/section}
</ul>
<p>
Zadání pro ostatní studenty zůstala nezměněna.
</p>
{else}
<p>
Zadání pro studenty byla úspěšně vygenerována.
</p>
{/if}
<form action="?act=admin,formassign,{$lecture.id}" method="post">
<input type="submit" value="Pokračovat v administraci zadání">
</form>
