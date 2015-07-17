<table class="admintable" border="0" cellpadding="2" cellspacing="1">
{* disiplay tree icons only if the maximum hierarchyl level is greater than zero *}
{if $maxlevel > 0}
<colgroup width="16" span="{$maxlevel}">
</colgroup>
{/if}
<col>
<col width="80">
{if $isAdmin || $isLecturer }
<tr class="newobject">
<td colspan="{$topspan}">&nbsp;Přidat novou sekci</td>
<td class="smaller" 
  ><a href="ctrl.php?act=edit,section,0"><img src="images/edit.gif" alt="[edit]" width="16" height="16"></a></td>
</tr>
<tr class="rowB">
<td colspan="{$topspan}">&nbsp;/</td>
<td>&nbsp;</td>
</tr>
{/if}
{section name=sectionPos loop=$sections}
{if $smarty.section.sectionPos.iteration is even}
<tr class="rowB">
{else}
<tr class="rowA">
{/if}
{section name=indent loop=$sections[sectionPos].indents}
  {if $sections[sectionPos].indents[indent] == "I"}
  <td width="16" height="16"
  ><img src="images/treeI.gif" width="16" height="16" alt="tree element"></td>
  {elseif $sections[sectionPos].indents[indent] == "E"}
  <td width="16" height="16"
  ><img src="images/treeE.gif" width="16" height="16" alt="tree element"></td>
  {elseif $sections[sectionPos].indents[indent] == "L"}
  <td width="16" height="16"
  ><img src="images/treeL.gif" width="16" height="16" alt="tree element"></td>
  {elseif $sections[sectionPos].indents[indent] == " "}
  <td width="16" height="16"
  ><img src="images/treeX.gif" width="16" height="16" alt="tree element"></td>
  {/if}
{/section}
{if $sections[sectionPos].numIndents == 1}
<td>&nbsp;{$sections[sectionPos].mtitle}</td>
{else}
<td colspan="{$sections[sectionPos].numIndents}">&nbsp;{$sections[sectionPos].mtitle}</td>
{/if}
<td width="80" class="smaller"  valign="middle"
  >{if $isAdmin || $isLecturer}<a href="ctrl.php?act=edit,article,0&parent={$sections[sectionPos].id}"><img src="images/newarticle.gif" alt="[nový článek]" width="16" height="16"></a
  ><a href="ctrl.php?act=edit,file,0&objid={$sections[sectionPos].id}"><img src="images/newfile.gif" alt="[nový soubor]" width="16" height="16"></a
  ><a href="ctrl.php?act=edit,section,0&parent={$sections[sectionPos].id}"><img src="images/new.gif" alt="[nový potomek]" width="16" height="16"></a
  ><a href="ctrl.php?act=edit,section,{$sections[sectionPos].id}"><img src="images/edit.gif" alt="[edit]" width="16" height="16"></a
  ><a href="ctrl.php?act=delete,section,{$sections[sectionPos].id}"><img src="images/delete.gif" alt="[delete]" width="16" height="16"></a
  >{/if}</td>
</tr>
{/section}
</table>
