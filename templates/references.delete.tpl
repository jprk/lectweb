<h1>Smaz�n� reference</h1>
<form action="ctrl.php?act=realdelete,references,{$file.Id}" method="post">
<input type="hidden" name="Id" value="{$file.Id}">
<p>Opravdu si p�ejete smazat referenci s popisem <i>{$file.description}</i>?</p>
<input type="submit" value="Ano">
</form>
