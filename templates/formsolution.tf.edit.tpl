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
V případě, že se v řešení úlohy nevyskytuje některý z koeficientů,
ponechte jeho hodnotu prázdnou. Pokud je některý z pólů reálné číslo, ponechte
prázdnou hodnotu jeho imaginární části. Zlomky, které Vám v řešení vyšly,
převeďte prosím na desetinná čísla.
</p>
<p>
Odpovídat můžete pouze jednou.
</p>
<form name="solutionform" action="?act=save,formsolution,{$subtask.id}" method="post" >
{section name=pId loop=$parts}
<h3>Úloha {$subtask.ttitle}-{$assignment.assignmnt_id|string_format:"%05d"}{$parts[pId].part}</h3>
  <p>
  Přenosová funkce systému je
  <div style="width: 80ex;">
  <div style="border-bottom: 1px solid black; padding-bottom: 1pt; width: 100%; text-align: center;">
  1
  </div>
  <div style=" padding-top: 1pt; width: 100%; text-align: center;">
  {assign var=temp value=$parts[pId].part}
  (&nbsp;{$varList.$temp}&nbsp;-&nbsp;(&nbsp;<input type="text" name="a[{$parts[pId].part}]" size="3">+<input type="text" name="b[{$parts[pId].part}]" size="3">i&nbsp;))
  (&nbsp;{$varList.$temp}&nbsp;-&nbsp;(&nbsp;<input type="text" name="c[{$parts[pId].part}]" size="3">+<input type="text" name="d[{$parts[pId].part}]" size="3">i&nbsp;))
  (&nbsp;{$varList.$temp}&nbsp;-&nbsp;(&nbsp;<input type="text" name="e[{$parts[pId].part}]" size="3">+<input type="text" name="f[{$parts[pId].part}]" size="3">i&nbsp;)
  </div>
  </div>
  </p>
  <p>
  Systém je
  <input type="radio" name="g[{$parts[pId].part}]" value="2">nestabilní
  <input type="radio" name="g[{$parts[pId].part}]" value="1">na mezi stability
  <input type="radio" name="g[{$parts[pId].part}]" value="0">stabilní.
  </p>
{/section}
<p>
<input type="submit" value="Odeslat řešení">
<input type="reset" value="Vymazat">
</p>
</form>
