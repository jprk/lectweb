<script language=javascript>

var allChecked = false;
var presChecked = false;
var distChecked = false;
var presElems = [ {section name=sId loop=$studentList}{if $studentList[sId].groupno < 90}'student{$studentList[sId].id}',{/if}{/section}null];
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

function markPres ( )
{
    presChecked = ! presChecked;
    var length = presElems.length;
    for ( i = 0 ; i < length ; i++ )
    {
        var elem = document.getElementById( presElems[i] );
        if ( elem.type == 'checkbox' )
        {
            elem.checked = presChecked;
        }
    }
}
</script>
{/literal}
<form id="stupassForm" action="?act=save,stupass,42" method="post">
<input type="hidden" name="id"         value="{$lecture.id}">
<table class="admintable" border="0" cellpadding="2" cellspacing="1">
<tr class="newobject">
<th>&nbsp;</th>
<th>Příjmení a jméno</th>
<th>Ročník / Skupina</th>
</tr>
{section name=sId loop=$studentList}
{if $smarty.section.sId.iteration is even}
<tr class="rowA">
{else}
<tr class="rowB">
{/if}
<td width="5%" align="center"><input type="checkbox" id="student{$studentList[sId].id}" name="pwreplace[{$studentList[sId].id}]"></td>
<td>&nbsp;{$studentList[sId].surname} {$studentList[sId].firstname}</td>
<td width="5%" align="center">{$studentList[sId].yearno}/{$studentList[sId].groupno}</td>
</tr>
{/section}
<tr class="newobject">
<td width="5%" align="center"><input id="markall" type="checkbox" name="markall" onclick="markAll('stupassForm');"></td>
<td colspan="3">&nbsp;Označit / odznačit vše</td>
</tr>
<tr class="newobject">
<td width="5%" align="center"><input id="markall" type="checkbox" name="markpres" onclick="markPres();"></td>
<td colspan="3">&nbsp;Označit / odznačit studenty denního studia v Praze</td>
</tr>
<tr class="newobject">
<td>&nbsp;</td>
<td colspan="3">
<input type="submit" value="Uložit">
<input type="reset" value="Vymazat">
</td>
</tr>
</table>
</form>
