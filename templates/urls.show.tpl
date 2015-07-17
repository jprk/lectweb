{if $urlsList}
<dl>
{section name=urlsPos loop=$urlsList}
<dt><strong><a href="{$urlsList[urlsPos].url}" target="_blank">{$urlsList[urlsPos].title}</a></strong></dt>
<dd>{$urlsList[urlsPos].description}</dd>
{/section}
</dl>
{else}
<p>
K tomuto předmětu zatím vyučující nezadal žádné odkazy na webové stránky.
</p>
{/if}