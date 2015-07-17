<h1>{if $adminMode}<span class="editimg"
  ><a href="ctrl.php?act=edit,section,{$section.Id}"
  ><img src="images/edit.gif" alt="[edit]" width="16" height="16"></a></span
  >{/if}{$section.title|escape:"html"}</h1>
<p><img src="ctrl.php?act=show,files,{$sectionImg.Id}" alt="{$sectionImg.description}"></p>
<p>&nbsp;</p>
<h2>Kontakt</h2>
{$section.text}
