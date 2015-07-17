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

function setObjIDFrom ( obj )
{
	document.fileeditform.objid.value = obj.value;
}

function displayCorrespondingSelect ( obj )
{
	this.sval = obj.options[obj.selectedIndex].value;
		
	if ( this.sval != "4" && this.sval != "5" )
	{
	  // Display section selection in element "in_row_s"
	  // and hide selection in element "in_row_a"
	  setDisplayMode ( "in_row_s1", SHOW );
	  setDisplayMode ( "in_row_s2", SHOW );
	  setDisplayMode ( "in_row_a1", HIDE );
	  setDisplayMode ( "in_row_a2", HIDE );
	  setObjIDFrom ( document.fileeditform.objid_s );
	}
	else
	{
	  // Display section selection in element "in_row_a"
	  // and hide selection in element "in_row_s"
	  setDisplayMode ( "in_row_s1", HIDE );
	  setDisplayMode ( "in_row_s2", HIDE );
	  setDisplayMode ( "in_row_a1", SHOW );
	  setDisplayMode ( "in_row_a2", SHOW );
	  setObjIDFrom ( document.fileeditform.objid_a );
	}
}

function initSelection ()
{
	displayCorrespondingSelect ( document.fileeditform.type );
}

// Add onLoad handler
ON_LOAD[ON_LOAD.length] = initSelection;

</script>
{/literal}
<form name="fileeditform" action="?act=save,file,{$file.id}" method="post" enctype="multipart/form-data">
<input type="hidden" name="id" value="{$file.id}">
<input type="hidden" name="MAX_FILE_SIZE" value="16000000">
<input type="hidden" name="objid" value="-1">
<input type="hidden" name="uid" value="{$uid}">
<input type="hidden" name="returntoparent" value="{$file.returntoparent}">
<table class="admintable" border="0" cellpadding="2" cellspacing="1">
<tr class="rowA">
<td class="itemtitle">Soubor</td>
<td>
<!--input type="file" name="userfile" style="width: 100%;"><br-->
<input type="file" name="userfile" size="100%"><br>
<small>Prohlížeč toto pole z bezpečnostních důvodů vždy maže. Nechcete-li 
nahrát nový soubor, nechte toto pole klidně nevyplněné.</small>
</td>
</tr>
<tr class="rowB">
<td class="itemtitle">Popis</td>
<td><input type="text" name="description" maxlength="255" style="width: 100%;" value="{$file.description|escape:"html"}"></td>
</tr>
<tr class="rowA">
<td class="itemtitle">Typ</td>
<td>
<select name="type" style="width: 100%;" onchange="displayCorrespondingSelect(this);">
{html_options options=$fileTypes selected=$file.type}
</select>
</td>
</tr>
<tr class="rowB">
<td class="itemtitle"
  ><span id="in_row_s1" style="display: block;">Rodičovská sekce</span
  ><span id="in_row_a1" style="display: none;">Rodičovský článek</span
  ></td>
<td>
<select id="in_row_s2" name="objid_s" style="width: 100%; display: block;" onchange="setObjIDFrom(this);">
{html_options options=$section_parents selected=$file.objid}
</select>
<select id="in_row_a2" name="objid_a" style="width: 100%; display: none;" onchange="setObjIDFrom(this);">
{html_options options=$article_parents selected=$file.objid}
</select>
</td>
</tr>
{* toto je nějaký relikt
<tr class="rowB">
<td class="itemtitle"></td>
<td id="in_row_a" class="rowB">
</td>
</tr>
*}
<tr class="rowA">
<td class="itemtitle">Pozice</td>
<td><input type="text" name="position" maxlength="3" size="3" value="{$file.position}"></td>
</tr>
<tr class="rowB">
<td>&nbsp;</td>
<td>
<input type="submit" value="Odeslat">
<input type="reset" value="Vymazat">
</td>
</tr>
</table>
</form>
