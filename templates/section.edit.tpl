{literal}
<script language="JavaScript">

var SHOW = "block";
var HIDE = "none";

function setDisplayMode ( elemId, displayMode )
{
    // Ignore this piece of code for broken browsers
    if ( document.getElementById )
	{
	    // Get object by its id tag
		this.elem = document.getElementById ( elemId );
		// If the object with this id exists ...
		if ( this.elem )
		{
			// ... set the proper display mode ("block" or "none")
		    this.elem.style.display = displayMode;
		}
	}
}

function setIval1From ( obj )
{
	document.sectionform.ival1.value = obj.value;
}

function displayCorrespondingSelect ( obj )
{
	this.sval = obj.options[obj.selectedIndex].value;
		
	if ( this.sval == "people" )
	{
	  // Display section selection in element "in_row_a"
	  // and hide selection in element "in_row_s"
	  setDisplayMode ( "in_row_s1", HIDE );
	  setDisplayMode ( "in_row_s2", HIDE );
	  setDisplayMode ( "in_row_l1", HIDE );
	  setDisplayMode ( "in_row_l2", HIDE );
	  setDisplayMode ( "in_row_a1", SHOW );
	  setDisplayMode ( "in_row_a2", SHOW );
	  setIval1From ( document.sectionform.objid_a );
	}
	else if ( this.sval == "exclistsect" )
	{
	  // Display lecture selection in element "in_row_s"
	  // and hide selection in element "in_row_a"
	  setDisplayMode ( "in_row_s1", HIDE );
	  setDisplayMode ( "in_row_s2", HIDE );
	  setDisplayMode ( "in_row_a1", HIDE );
	  setDisplayMode ( "in_row_a2", HIDE );
	  setDisplayMode ( "in_row_l1", SHOW );
	  setDisplayMode ( "in_row_l2", SHOW );
	  setIval1From ( document.sectionform.objid_l );
	}
	else
	{
	  // Display section selection in element "in_row_s"
	  // and hide selection in element "in_row_a"
	  setDisplayMode ( "in_row_s1", SHOW );
	  setDisplayMode ( "in_row_s2", SHOW );
	  setDisplayMode ( "in_row_a1", HIDE );
	  setDisplayMode ( "in_row_a2", HIDE );
	  setDisplayMode ( "in_row_l1", HIDE );
	  setDisplayMode ( "in_row_l2", HIDE );
	  setIval1From ( document.sectionform.objid_s );
	}
}

function initSelection ()
{
	displayCorrespondingSelect ( document.sectionform.type );
}

// Add onLoad handler
ON_LOAD[ON_LOAD.length] = initSelection;

</script>
{/literal}
<form name="sectionform" action="?payload={$lecture.code|strtolower}/node/save/section/{$section.id}" method="post">
<input type="hidden" name="id" value="{$section.id}">
<input type="hidden" name="lecture_id" value="{$section.lecture_id}">
<input type="hidden" name="ival1" value="{$section.ival1}">
<table class="admintable" border="0" cellpadding="2" cellspacing="1">
<tr class="rowA">
<td class="itemtitle">Rodičovská sekce</td>
<td>
<select name="parent" style="width: 100%;">
{html_options options=$section_parents selected=$section.parent}
</select>
</td>
</tr>
<tr class="rowB">
<td class="itemtitle">Typ sekce</td>
<td>
<select name="type" style="width: 100%;" onchange="displayCorrespondingSelect(this);">
{html_options options=$sectypes selected=$section.type}
</select>
</td>
</tr>
<tr class="rowA">
<td class="itemtitle">Název sekce v menu</td>
<td><input type="text" name="mtitle" style="width: 100%;" value="{$section.mtitle|escape:"html"}"></td>
</tr>
<tr class="rowB">
<td class="itemtitle">Název sekce</td>
<td><input type="text" name="title" style="width: 100%;" value="{$section.title|escape:"html"}"></td>
</tr>
<tr class="rowA">
<td class="itemtitle">Pozice</td>
<td><input type="text" name="position" maxlength="3" size="3" value="{$section.position}"></td>
</tr>
<tr class="rowB">
<td class="itemtitle">Přesměrování</td>
<td><input type="text" name="redirect" maxlength="255" style="width: 100%;" value="{$section.redirect}"></td>
</tr>
</tr>
<tr class="rowB">
<td class="itemtitle"
  ><span id="in_row_s1" style="display: block;">(ival1)</span
  ><span id="in_row_a1" style="display: none;">Kategorie výpisu osob</span
  ><span id="in_row_l1" style="display: none;">Předmět</span
  ></td>
<td>
<select id="in_row_s2" name="objid_s" style="width: 100%; display: block;" onchange="setIval1From(this);">
<option label="Nepoužito" value="0">Nepoužito</option>
</select>
<select id="in_row_a2" name="objid_a" style="width: 100%; display: none;" onchange="setIval1From(this);">
{html_options options=$personcats selected=$section.ival1}
</select>
<select id="in_row_l2" name="objid_l" style="width: 100%; display: none;" onchange="setIval1From(this);">
{html_options options=$lectureSelect selected=$section.ival1}
</select>
</td>
</tr>
<tr class="rowA">
<td colspan="2">
<textarea id="edcTextArea" name="text" style="width: 100%; height: 420px;">
{$section.text|escape:"html"}
</textarea>
</td>
</tr>
<tr class="rowA">
<td>&nbsp;</td>
<td>
<input type="submit" name="doShow" value="Uložit a zobrazit">
<input type="submit" value="Uložit a na strom sekcí">
<input type="reset" value="Vymazat">
</td>
</tr>
</table>
</form>
<p><small>
<script language="javascript">
  document.write(document.compatMode);
</script>
</small></p>

