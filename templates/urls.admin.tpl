<table class="admintable" border="0" cellpadding="2" cellspacing="1">
<tr class="newobject">
<td class="itemtitle">Přidat odkaz</td>
<td width="40" class="smaller" 
  ><a href="?act=edit,urls,0"><img src="images/add.gif" alt="[add]" width="16" height="16"></a></td>
</tr>
{section name=urlsPos loop=$urlsList}
{if $smarty.section.urlsPos.iteration is even}
<tr class="rowA">
{else}
<tr class="rowB">
{/if}
<td class="itemtitle">{$urlsList[urlsPos].title}</td>
<td width="40" class="smaller" valign="middle"
  ><a href="?act=edit,urls,{$urlsList[urlsPos].id}"><img src="images/edit.gif" alt="[edit]" width="16" height="16"></a
  ><a href="?act=delete,urls,{$urlsList[urlsPos].id}"><img src="images/delete.gif" alt="[delete]" width="16" height="16"></a></td>
</tr>
{/section}
</table>
