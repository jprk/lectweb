{if $message}
<h1>D�kujeme</h1>
<p>
D�kujeme za zpr�vu. V� koment�� byl �sp�n� odesl�n.
</p>
<p>
Text odeslan� zpr�vy zn�:
</p>
<p class="message">
{$message|escape:"html"|nl2br}
</p>
{else}
<h1>Chyba</h1>
<p>
Va�e zpr�va byla pr�zdn� a nebyla nikam odesl�na.
</p>
{/if}