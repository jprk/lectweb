{literal}
<script type="text/javascript">
function validnumber ( obj )
{
	if ( isNaN ( obj.value ))
	{
		/* Remember the object id so that we can construct the appropriate
		   `getElementById()` stanza. */
		objid = obj.id;
        /* Value of obj cannot be represented as a number. */
		alert ( "Hodnota '" + obj.value + "' není číslo!" );
		/* And focus the field again */
		setTimeout ( "document.getElementById(objid).focus();", 1 );
		setTimeout ( "document.getElementById(objid).select();", 1 );
		return false;
	}
}
</script>
{/literal}
<p>
V případě, že se v řešení úlohy nevyskytuje některý z koeficientů A-F,
ponechte jeho hodnotu prázdnou. V řešení se vyskytují pouze celá čísla.
Systém automaticky provádí konverzi vložených hodnot na celá čísla.
</p>
<p>
Odpovídat můžete pouze jednou.
</p>
<form name="solutionform" action="?act=save,formsolution,{$subtask.id}" method="post" >
{section name=pId loop=$parts}
<h3>Úloha {$subtask.ttitle}-{$assignment.assignmnt_id|string_format:"%05d"}{$parts[pId].part}</h2>
<table cellspacing="1" cellpadding="0">
<tr><td>A =</td><td><input type="text" id="fs_a{$parts[pId].part}" name="a[{$parts[pId].part}]" onblur="validnumber(this);"></td></tr>
<tr><td>B =</td><td><input type="text" id="fs_b{$parts[pId].part}" name="b[{$parts[pId].part}]" onblur="validnumber(this);"></td></tr>
<tr><td>C =</td><td><input type="text" id="fs_c{$parts[pId].part}" name="c[{$parts[pId].part}]" onblur="validnumber(this);"></td></tr>
<tr><td>D =</td><td><input type="text" id="fs_d{$parts[pId].part}" name="d[{$parts[pId].part}]" onblur="validnumber(this);"></td></tr>
<tr><td>E =</td><td><input type="text" id="fs_e{$parts[pId].part}" name="e[{$parts[pId].part}]" onblur="validnumber(this);"></td></tr>
<tr><td>F =</td><td><input type="text" id="fs_f{$parts[pId].part}" name="f[{$parts[pId].part}]" onblur="validnumber(this);"></td></tr>
</table>
{/section}
<p>
<input type="submit" value="Odeslat řešení">
<input type="reset" value="Vymazat">
</p>
</form>
