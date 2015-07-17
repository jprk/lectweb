<h1>{if $adminMode}<span class="editimg"
  ><a href="ctrl.php?act=edit,section,{$section.id}"
  ><img src="images/edit.gif" alt="[edit]" width="16" height="16"></a></span
  >{/if}{$section.title}</h1>
{if $sectionImg}
<img class="secimg" width="180" src="ctrl.php?act=show,files,{$sectionImg.id}" alt="{$sectionImg.description}">
{/if}
<h2>Pøesmìrování</h2>
<p>
Údaje na této stránce odkazují na adresu
<a href="{$section.redirect}" target="_blank">{$section.redirect}</a>.
Odkaz se vám otevøe v novém oknì.
</p>
