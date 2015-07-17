<h2>Informace o úloze</h2>
<p>
<strong>Aktivní od:</strong> {$subtask.datefrom|date_format:"%d.%m.%Y"}<br>
<strong>Aktivní do:</strong> {$subtask.dateto|date_format:"%d.%m.%Y"}<br>
<strong>Bodové maximum:</strong> {$subtask.maxpts}
</p>
<h3>Text zadání</h3>
{$subtask.assignment}
{if $assignment.file_id}
<p>
Soubor se zadáním si stáhněte <a href="?act=show,file,{$assignment.file_id}">zde</a>.
</p>
{/if}
<h3>Odevzdání</h3>
<p>
{if $subtask.active}
{if $subtask.isformassignment}
Formulář pro odevzdání úlohy je <a href="?act=edit,formsolution,{$subtask.id}">zde</a>.
{elseif $subtask.issimuassignment}
Při odevzdání simulinkového modelu postupujte následujícím způsobem:
<ol>
    <li>Vytvořte Simulinkový model a uložte jej jako .mdl soubor.
    <li>Tento soubor nahrajte pomocí formuláře pod tímto textem.
</ol>
Formulář pro odevzdání úlohy je <a href="?act=edit,formsolution,{$subtask.id}">zde</a>.
{elseif $subtask.ispdfassignment}
Při odevzdání PDF souboru postupujte následujícím způsobem:
<ol>
    <li>Vytvořte ve Vašem oblíbeném textovém procesoru soubor odpovídající
        požadavkům zadání.
    <li>Tento soubor zkonvertujte do PDF (OpenOffice nebo Lyx to umí rovnou,
        jinak použijte virtuální tiskárnu do PDF, jako je například PDFCreator).
    <li>Tento soubor nahrajte pomocí formuláře pod tímto textem.
</ol>
Formulář pro odevzdání úlohy je <a href="?act=edit,formsolution,{$subtask.id}">zde</a>.
{elseif $subtask.islpdfassignment}
<p>
Vložte PDF soubor s vypracovaným řešením zadání. Odpovídat můžete pouze jednou.
</p>
<form name="solutionform" action="?act=save,formsolution,{$subtask.id}" method="post" enctype="multipart/form-data">
<input type="hidden" name="MAX_FILE_SIZE" value="16000000">
<table class="admintable" border="0" cellpadding="2" cellspacing="1">
<tr class="rowA">
  <td class="itemtitle" width="100%">Soubor s popisem řešení úlohy (.pdf)</td>
  <td>
    <input type="file" name="pdf[1]" size="70%"><br>
  </td>
</tr>
<tr class="rowB">
  <td>&nbsp;</td>
  <td>
    <input type="submit" value="Odeslat řešení">
    <input type="reset"  value="Vymazat">
  </td>
</tr>
</table>
</form>
{else}
Pokud Vaše semestrální práce sestává pouze z jednoho PDF souboru, nahrajte
jej prosím na náš server pomocí formuláře pod tímto textem.
<p>
V opačném prípadě prosím postupujte následujícím způsobem:
<ol>
	<li>Podle pokynů uvedených výše připravte ZIP archív se všemi soubory své
	    práce. Soubory je třeba správným způsobem pojmenovat, archív také,
	    vše bz mělo být uvedeno výše.
	<li>Tento archív nesmí být větší, než 8MB. Pokud tomu tak je, patrně
	    jse přibalili zcela nepotřebný balast (protokoly kompilátoru,
		spustitelné soubory a podobně). 
	<li>Tento archív nahrajte pomocí formuláře pod tímto textem.
	<li>Po úspěšném vložení vašich výsledků do databáze se tato úloha zobrazí
	    na Vaší domovské stránce jako odevzdaná.
</ol>
<p>
Odpovídat můžete pouze jednou. Úlohy se opravují ručně, opravu typicky
nezvládáme v čase kratším, než jeden týden.
</p>
<form name="solutionform" action="?act=save,formsolution,{$subtask.id}" method="post" enctype="multipart/form-data">
<input type="hidden" name="MAX_FILE_SIZE" value="16000000">
<table class="admintable" border="0" cellpadding="2" cellspacing="1">
<tr class="rowA">
  <td>Soubor s řešením:&nbsp;</td>
  <td><input type="file" name="zip[1]" size="50"></td>
</tr>
<tr class="rowB">
  <td>&nbsp;</td>
  <td>
    <input type="submit" value="Odeslat řešení">
    <input type="reset"  value="Vymazat">
  </td>
</tr>
</table>
</p>
</form>
{/if}
{else}
Úloha není aktivní a není ji možno odevzdat.
{/if}
</p>
