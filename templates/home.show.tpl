{* This is response to 'show/home/0', which occurs for example after timeout
   of the session elapses. *}
{if $lectureList}
<p>
Vyberte si prosím předmět:
</p>
<ul>
{section name=lecPos loop=$lectureList}
<li>
</li>
{/section}
</ul>
{else}
<p>
V systému nejsou založeny žádné předměty, nejprve nějaký založte (může to udělat
pouze správce aplikace) a pak se sem vraťte. 
</p>
{/if}
