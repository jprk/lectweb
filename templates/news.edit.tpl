{literal}
<script language="JavaScript">
//<!--
function textCounter ( field, countId, maxlimit )
{
	// The lenght of the field.value will be updated _after_ this call.
	// Therefore the first invocation of textCounted over an input
	// field will report the length 0, but the actual length is 1 ...
	// var clen = field.value.length + 1;
	var fieldValue = field.value;
	var textLength = parseInt ( fieldValue.length );
	// If the text is too long, trim it.
	if ( textLength > maxlimit )
	{
		field.value = field.value.substring (0, maxlimit);
	}
	// Update the 'characters left' counter otherwise.
	else if ( document.getElementById )
	{ 
		var counter	= document.getElementById (countId);
		// if ( counter ) counter.childNodes[0].nodeValue = maxlimit - field.value.length;
		if ( counter ) counter.childNodes[0].nodeValue = maxlimit-textLength;
	}
}

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

function setObjectIdFrom ( obj )
{
	document.newsform.object_id.value = obj.value;
}

function hideAll ()
{
{/literal}{foreach from=$newsTypeSelect.loop item=st key=sk}
	setDisplayMode ( "in_span_{$st.rowid}", HIDE );
	setDisplayMode ( "in_select_{$st.rowid}", HIDE );
{/foreach}{literal}}

function displayCorrespondingSelect ( obj )
{
	// Remember the selected value
	var sId = parseInt ( obj.options[obj.selectedIndex].value );
	
	switch ( sId )
	{
{/literal}{foreach from=$newsTypeSelect.loop item=st key=sk}
	case {$sk}:
		// Display selection in element "in_row_{$st.rowid}"
	  	// and hide selection in other "in_row_..." elements
		hideAll();
	  	setDisplayMode ( "in_span_{$st.rowid}", SHOW );
	  	setDisplayMode ( "in_select_{$st.rowid}", SHOW );
	  	setObjectIdFrom ( document.newsform.ref_{$st.rowid} );
		break;
{/foreach}{literal}
	default:
		alert ( "Neznámý index výběru (sId="+sId+")!" );
	}
}

function initSelection ()
{
	displayCorrespondingSelect ( document.newsform.type );
}

// Add onLoad handler
ON_LOAD[ON_LOAD.length] = initSelection;
//-->
</script>
{/literal}
<form name="newsform" action="?act=save,news,{$news.id}" method="post">
<input type="hidden" name="id" value="{$news.id}">
<input type="hidden" name="object_id" value="-1">
<input type="hidden" name="author_id" value="{$news.author_id}">
<table class="admintable" border="0" cellpadding="4" cellspacing="1">
<tr class="rowB">
<td class="itemtitle">Titulek<br/>(zbývá <span id="newstitle">40</span> znaků)</td>
<td><input type="text" name="title" style="width: 100%;" value="{$news.title|escape:"html"}" onkeyup="javascript:textCounter(this,'newstitle',40);"></td>
</tr>
<tr class="rowA">
<td class="itemtitle">Text novinky<br/>(zbývá <span id="newstext">360</span> znaků)</td>
<td>
<textarea name="text" style="width: 400px; height: 200px;" onKeyUp="javascript:textCounter(this,'newstext',360);">{$news.text}</textarea>
</td>
</tr>
<tr class="rowB">
<td class="itemtitle">Typ novinky</td>
<td>
<select name="type" style="width: 100%;" onchange="displayCorrespondingSelect(this);">
{html_options options=$newsTypeSelect.options selected=$news.type}
</select>
</tr>
{* ----- Selectors for particular object IDs ----- *}
<tr class="rowA">
<td class="itemtitle">
{foreach from=$newsTypeSelect.loop item=st}<span id="in_span_{$st.rowid}" >{$st.title}</span>{/foreach}
</td>
<td>
{foreach from=$newsTypeSelect.loop item=st}
<select id="in_select_{$st.rowid}" name="ref_{$st.rowid}" style="width: 100%;" onchange="setObjectIdFrom(this);">
{html_options options=$st.options selected=$news.object_id}
</select>
{/foreach}
</td>
</tr>
<tr class="rowB">
<td class="itemtitle">Ukazovat od</td>
<td><input type="text" name="datefrom" maxlength="16" size="16" value="{$news.datefrom|date_format:"%d.%m.%Y %H:%M"}">&nbsp;&nbsp;<img src="images/calendar.gif" alt="[kalendář]" onClick="openCalendar('newsform','datefrom');"></td>
</tr>
<tr class="rowA">
<td class="itemtitle">Ukazovat do</td>
<td><input type="text" name="dateto" maxlength="16" size="16" value="{$news.dateto|date_format:"%d.%m.%Y %H:%M"}">&nbsp;&nbsp;<img src="images/calendar.gif" alt="[kalendář]" onClick="openCalendar('newsform','dateto');"></td>
</tr>
<tr class="rowB">
<td>&nbsp;</td>
<td>
<input type="submit" value="Uložit">
<input type="reset" value="Vymazat">
</td>
</tr>
</table>
</form>
