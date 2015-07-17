<table class="admintable" border="0" cellpadding="2" cellspacing="1">
<tr class="newobject">
<td colspan="4">Přidat dalšího studenta</td>
<td width="40" class="smaller" valign="middle"
  ><a href="?act=edit,student,0"><img src="images/famfamfam/report_add.png" title="přidat studenta" alt="[nový student]" width="16" height="16"></a></td>
</tr>
{section name=sId loop=$studentList}
{if $smarty.section.sId.iteration is even}
<tr class="rowA">
{else}
<tr class="rowB">
{/if}
<td>{$studentList[sId].surname} {$studentList[sId].firstname}</td>
<td>{$studentList[sId].yearno}</td>
<td>{$studentList[sId].groupno}</td>
<td width="32" class="smaller" valign="middle"
  ><a href="?act=admin,formsolution,{$studentList[sId].id}"    ><img src="images/famfamfam/page_add.png"       alt="[nahrát]"     title="nahrát odevzdané úlohy" width="16" height="16"></a
  ><a href="?act=admin,extension,{$studentList[sId].id}&mode=2"><img src="images/famfamfam/bell_add.png"       alt="[prodloužit]" title="prodloužit"             width="16" height="16"></a></td>
<td width="48" class="smaller" valign="middle"
  ><a href="?act=edit,student,{$studentList[sId].id}"          ><img src="images/famfamfam/report_edit.png"    alt="[změnit]"     title="změnit"     width="16" height="16"></a
  ><a href="?act=delete,student,{$studentList[sId].id}"        ><img src="images/famfamfam/report_delete.png"  alt="[smazat]"     title="smazat"     width="16" height="16"></a
  ><a href="?act=admin,login,{$studentList[sId].id}"           ><img src="images/famfamfam/application_go.png" alt="[přepnout]"   title="přepnout"   width="16" height="16"></a></td>
</tr>
{/section}
</table>
