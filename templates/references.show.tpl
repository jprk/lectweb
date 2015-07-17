<h1><span class="editimg"
  ><a href="ctrl.php?act=edit,section,{$section.Id}"
  ><img src="images/edit.gif" alt="[edit]" width="16" height="16"></a></span
  >{$section.title}</h1>
{$section.text}
<table width="100%" border="0" cellpadding="0" cellspacing="0">
<colgroup width="50%">
<col>
<col>
</colgroup>
{section name=referencePos loop=$referenceList}
{if $smarty.section.referencePos.index is even}
<tr>
{/if}
<td align="center">
<div class="reference">
<img src="ctrl.php?act=show,files,{$referenceList[referencePos].Id}" alt="[logo]">
<p>
{$referenceList[referencePos].description|escape:"html"}
</p>
</div>
</td>
{if ( $smarty.section.referencePos.index is odd ) or ( $smarty.section.referencePos.last and $referenceListOdd )}
{if ( $smarty.section.referencePos.last and $referenceListOdd ) }
<td>&nbsp;</td>
{/if}
</tr>
{/if}
{/section}
</table>
