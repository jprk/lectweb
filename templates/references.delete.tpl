<h1>Smazání reference</h1>
<form action="ctrl.php?act=realdelete,references,{$file.Id}" method="post">
<input type="hidden" name="Id" value="{$file.Id}">
<p>Opravdu si pøejete smazat referenci s popisem <i>{$file.description}</i>?</p>
<input type="submit" value="Ano">
</form>
