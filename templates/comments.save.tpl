{if $message}
<h1>Dìkujeme</h1>
<p>
Dìkujeme za zprávu. Váš komentáø byl úspìšnì odeslán.
</p>
<p>
Text odeslané zprávy zní:
</p>
<p class="message">
{$message|escape:"html"|nl2br}
</p>
{else}
<h1>Chyba</h1>
<p>
Vaše zpráva byla prázdná a nebyla nikam odeslána.
</p>
{/if}