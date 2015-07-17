<script language=javascript>

var allChecked       = false;
var presPrahaChecked = false;
var presDecinChecked = false;
var distChecked      = false;

var presElemsPraha = [ {section name=sId loop=$studentList}{if $studentList[sId].groupno < 90}'student{$studentList[sId].id}',{/if}{/section}null];
var presElemsDecin = [ {section name=sId loop=$studentList}{if $studentList[sId].groupno == 99}'student{$studentList[sId].id}',{/if}{/section}null];
var distElems = [];

{literal}
function markAll ( formName )
{
    var formObj = document.getElementById( formName );
    if ( formObj )
    {
        allChecked = ! allChecked;
        if ( formObj.elements && formObj.elements.length )
        {
            var length = formObj.elements.length;
            for ( i = 0 ; i < length ; i++ )
            {
                var elem = formObj.elements[i];
                if ( elem.type == 'checkbox' && elem.id.substring(0,7) == 'student')
                {
                    elem.checked = allChecked;
                }
            } 
        }
    }
}

function markDist ( )
{
    distChecked = ! distChecked;
    var length = distElems.length;
    for ( i = 0 ; i < length ; i++ )
    {
        var elem = document.getElementById( distElems[i] );
        if ( elem.type == 'checkbox' )
        {
            elem.checked = distChecked;
        }
    }
}

function markPresPraha ( )
{
    presPrahaChecked = ! presPrahaChecked;
    var length = presElemsPraha.length;
    for ( i = 0 ; i < length ; i++ )
    {
        var elem = document.getElementById( presElemsPraha[i] );
        if ( elem.type == 'checkbox' )
        {
            elem.checked = presPrahaChecked;
        }
    }
}

function markPresDecin ( )
{
    presDecinChecked = ! presDecinChecked;
    var length = presElemsDecin.length;
    for ( i = 0 ; i < length ; i++ )
    {
        var elem = document.getElementById( presElemsDecin[i] );
        if ( elem.type == 'checkbox' )
        {
            elem.checked = presDecinChecked;
        }
    }
}
</script>
{/literal}
<p>
Vyberte studenty, jimž chcete změnit termín odevzdání úlohy <em>{$subtask.title}</em>.
</p>
<form id="extForm" action="?act=edit,extension,{$subtask.id}" method="post">
<input type="hidden" name="mode" value="{$mode}">
<table class="admintable" border="0" cellpadding="2" cellspacing="1">
<tr class="newobject">
<th>&nbsp;</th>
<th>Příjmení a jméno</th>
<th>Ročník / Skupina</th>
<th>Individuální termín</th>
</tr>
{section name=sId loop=$studentList}
{if $smarty.section.sId.iteration is even}
<tr class="rowA">
{else}
<tr class="rowB">
{/if}
<td width="5%" align="center"><input type="checkbox" id="student{$studentList[sId].id}" name="objids[{$studentList[sId].id}]"></td>
<td>&nbsp;{$studentList[sId].surname} {$studentList[sId].firstname}</td>
<td width="5%" align="center">{$studentList[sId].yearno}/{$studentList[sId].groupno}</td>
<td width="20%" style="padding: 0ex 1ex;">{$studentList[sId].dateto}</td>
</tr>
{/section}
<tr class="newobject">
<td width="5%" align="center"><input id="markall" type="checkbox" name="markall" onclick="markAll('extForm');"></td>
<td colspan="3">&nbsp;Označit / odznačit vše</td>
</tr>
<tr class="newobject">
<td width="5%" align="center"><input id="markall" type="checkbox" name="markpres" onclick="markPresPraha();"></td>
<td colspan="3">&nbsp;Označit / odznačit studenty denního studia v Praze</td>
</tr>
<tr class="newobject">
<td width="5%" align="center"><input id="markall" type="checkbox" name="markpres" onclick="markPresDecin();"></td>
<td colspan="3">&nbsp;Označit / odznačit studenty denního studia v Děčíně</td>
</tr>
<tr class="newobject">
<td>&nbsp;</td>
<td colspan="3">
<input type="submit" value="Zadat datum">
<input type="reset" value="Vymazat">
</td>
</tr>
</table>
</form>
