<table class="admintable" border="0" cellpadding="4" cellspacing="1">
<tr class="newobject">
<td colspan="4">Přidat novinku</td>
<td width="32" class="smaller" 
  ><a href="?act=edit,news,0"><img src="images/add.gif" alt="[přidat]" title="přidat novinku" width="16" height="16"></a></td>
</tr>
{section name=nId loop=$fullNewsList}
{if $smarty.section.nId.iteration is even}
<tr class="rowA">
{else}
<tr class="rowB">
{/if}
<td>{$fullNewsList[nId].title}<br/><span class="smaller">{$fullNewsList[nId].text}</span></td>
<td class="smaller"><img src="images/{$fullNewsList[nId].i_src}" alt="[{$fullNewsList[nId].i_alt}]" title="{$fullNewsList[nId].i_alt}"></td>
<td class="smaller">{$fullNewsList[nId].author.login}</td>
<td class="smaller">{$fullNewsList[nId].datefrom|date_format:"%d.%m.%Y %H:%M"}</td>
<td width="32" class="smaller" valign="middle"
  ><a href="?act=edit,news,{$fullNewsList[nId].id}"  ><img src="images/edit.gif"   alt="[změnit]" title="změnit novinku" width="16" height="16"></a
  ><a href="?act=delete,news,{$fullNewsList[nId].id}"><img src="images/delete.gif" alt="[smazat]" title="smazat novinku" width="16" height="16"></a></td>
</tr>
{/section}
</table>
