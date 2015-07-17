<h3>Webové rozhraní KOSu</h3>
<p>
Zadejte prosím cestu k CSV souboru se jmény a ČVUT ID studentů. Tento soubor
lze vyexportovat z WebKOSu následujícím postupem:
</p>
<ul>
<li>zvolte zobrazení prezenčních seznamů přes volby <em>Předměty / Prezenční
    seznamy</em>,</li>
<li>zvolte semestr a předmět,</li>
<li>klikněte na volbu <em>Exportovat</em>,</li>
<li>soubor si z prohlížeče uložte a do vstupního pole, uvedeného níže, k němu
    zadejte cestu.</li>
</ul>
<form action="?act=edit,import,42" method="post" enctype="multipart/form-data" name="kosimport" id="kosimport">
<input type="hidden" name="format" value="2">
  <p>
    <input type="file" name="kosfile">
  </p>
  <p>
    <input type="checkbox" name="addyear" value="1">
    Zvýšit ročník studentů o jedna (je to
    potřeba při importu studentů v září, kdy ještě nedošlo ke kompletnímu
    překlopení KOSu na nový školní rok a čerstvě zapsaní studenti mají ročník
    nastaven na nula).
  </p>
  <p>
    <input type="submit" name="Submit" value="Doplnit e-maily a loginy z LDAP serveru FD">
  </p>
</form>
<h3>Stará (terminálová) verze KOSu</h3>
<p>
Zadejte prosím cestu k textovému souboru se jmény a rodnými čísly studentů
v kódování CP1250. Tento soubor lze vyexportovat z&nbsp;terminálové verze KOSu
následujícím postupem:
</p>
<ul>
<li>zvolte požadovaný předmět přes volby <em>Studium / zap. Předmětů - studenti
   / Zapsaní stud. </em>,</li>
<li>po vybrání předmětu si nechte mailem na svoji adresu v kódování 'W' poslat
   seznam studentů volbou <em>Vytisknout / ... / Seznam studentů + email
   adresy</em> a poslat,</li>
<li>výsledný soubor si z mailu uložte a do vstupního pole, uvedeného níže,
   k němu zadejte cestu.</li>
</ul>
<form action="?act=edit,import,42" method="post" enctype="multipart/form-data" name="kosimport" id="kosimport">
<input type="hidden" name="format" value="1">
  <p>
    <input type="file" name="kosfile">
  </p>
  <p>
    <input type="checkbox" name="addyear" value="1">
    Zvýšit ročník studentů o jedna (je to
    potřeba při importu studentů v září, kdy ještě nedošlo ke kompletnímu
    překlopení KOSu na nový školní rok a čerstvě zapsaní studenti mají ročník
    nastaven na nula).
  </p>
  <p>
    <input type="submit" name="Submit" value="Ověřit formát">
  </p>
</form>
