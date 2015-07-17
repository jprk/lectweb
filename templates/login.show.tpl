{if $isHTTPS}
    {if $loginfailed == 1}
        <p>
            Při pokusu o přihlášení jste udali nesprávné uživatelské jméno nebo heslo.
            Zadejte prosím uživatelské jméno a heslo pro přístup do neveřejné oblasti
            stránek předmětu znovu:
        </p>
    {else}
        {*<p>
        Studentské loginy pro LS 2008/2009 nejsou ještě funkční. Připomínám,
        že vaše uživatelská jména a hesla vám budou zaslána poštou, bohužel není
        v našich silách použít ta, která máte na sítích
        FD (ono to funguje, ale pouze pro některé studenty a pouze v Praze).
        </p>*}
        <p>
            Zadejte uživatelské jméno a heslo pro přístup do neveřejné oblasti stránek
            předmětu:
        </p>
    {/if}
    <form role="form" action="{cmslink act="verify" obj="login" id="42" plain="true"}" method="post">
        <div class="form-group">
            <label for="usernameInput">Uživatelské jméno</label>
            <input type="text" name="username" value="{$oldusername}" class="form-control" id="usernameInput" placeholder="Uživatelské jméno"></td>
        </div>
        <div class="form-group">
            <label for="passwordInput">Heslo</label>
            <input type="password" name="password" class="form-control" id="passwordInput" placeholder="Heslo">
        </div>
        <button type="submit" class="btn btn-default">Přihlásit</button>
    </form>
{else}
    <p>
        {*Studentské loginy by měly být funkční, ověřte si to, prosím. Připomínáme,
        že vaše uživatelská jména a hesla vám byla zaslána elektronickou poštou
        na adresy, uvedené v KOSu. Mohlo by vám teoreticky fungovat i přihlášení
        heslem, které máte na Novellovské síti FD v Praze, ale ručit za to nemůžeme.
        Pokud něco nefunguje, dejte prosím mailem vědět Janu Přikrylovi na
        <a href="mailto:prikryl@fd.cvut.cz">prikryl@fd.cvut.cz</a>
        a zprávu nějak smysluplně pojmenujte, aby ji nepřehlédl.*}
        Studentské loginy by měly být funkční. Studenti v Praze se mohou od
        LS&nbsp;2011/2012 přihlašovat jednotnými přihlašovacími údaji ČVUT FD,
        uživatelské jméno a heslo by mělo být totožné s uživatelským jménem a heslem
        na počítačové síti FD v Praze. Studenti v Děčíně obdrží v okamžiku, kdy se nám
        je - vzhledem ke změně rozhraní KOSu - podaří do systému začlenit, e-mail
        s uživatelským jménem a heslem, obdobně, jako tomu bylo v předešlých letech
    </p>
    <p>
        Z bezpečnostních důvodů probíhá přihlašování po šifrovaném HTTP spojení.
        Je možné, že některé verze prohlížečů nebudou považovat certifikační autoritu,
        kterou používáme, za důvěryhodnou (záleží to hlavně na tom, jaké kořenové
        certifikáty jste si do vašeho počítače nainstalovali, naše certifikáty vydává
        nizozemská organizace
        <a href="https://www.terena.org/activities/tcs/faq/general.html">TERENA</a>).
        Prosím, přidejte si v takovém případě náš certifikát do seznamu důvěryhodných
        certifikátů, opravdu jde jen o to, aby vaše heslo (a vaše výsledky) neběhaly
        po síti jako čistý text.
    </p>
    <p>
        Pokud něco nefunguje, dejte prosím mailem vědět Janu Přikrylovi na
        <a href="mailto:prikryl@fd.cvut.cz">prikryl@fd.cvut.cz</a>
        a zprávu nějak smysluplně pojmenujte, aby ji nepřehlédl.
    </p>
    <p>
        V přihlašování pokračujte {cmslink https="true" obj="login" act="show" id="42" text="zde"}.
    </p>
{/if}