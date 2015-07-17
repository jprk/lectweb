<h1>Smazání sekce</h1>
<form action="?act=realdelete,section,{$section.id}" method="post">
<input type="hidden" name="id" value="{$section.id}">
<input type="hidden" name="returntoparent" value="{$section.returntoparent}">
<p>Opravdu si pøejete smazat sekci s názvem <i>'{$section.title}'</i>?</p>
<input type="submit" value="Ano">
</form>
