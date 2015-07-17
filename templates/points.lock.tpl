{if $locklogin}
{if $lockstolen}
<p>
Bodové hodnocení bylo od {$locktime|date_format:"%d.%m.%Y %H:%M:%S"} uzamčeno pro změny
uživatelem <i>{$locklogin}</i>. Vzhledem k tomu, že tento uživatel překročil
maximální dobu uzamčení bodového hodnocení (30 minut), jste nyní vlastníkem
zámku. Možná by nebylo od věci se s uživatelem <i>{$locklogin}</i> pro jistotu
spojit. 
</p>
{else}
<p>
Od {$locktime|date_format:"%d.%m.%Y %H:%M:%S"} vlastníte zámek na změny bodového hodnocení.
Přesvědčte se, že stránku needitujete ve dvou exemplářích.
</p>
{/if}
{/if}
<p>
Máte nyní výhradní právo měnit údaje s bodovým ohodnocemím studentů. Zámek
vyprší za 30 minut, uvolní se také okamžitě poté, co data uložíte.
</p>

