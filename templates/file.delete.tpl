<form action="?act=realdelete,file,{$file.id}" method="post">
<input type="hidden" name="id" value="{$file.id}">
<input type="hidden" name="returntoparent" value="{$file.returntoparent}">
<p>Opravdu si přejete smazat soubor <i>{$file.origfname}</i> s popisem <i>{$file.description}</i>?</p>
<input type="submit" value="Ano">
</form>
