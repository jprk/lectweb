<div class="sidebar-nav">
    <div class="navbar navbar-default" role="navigation">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".sidebar-navbar-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <span class="visible-xs navbar-brand">Hlavní menu</span>
        </div>
        <div class="navbar-collapse collapse sidebar-navbar-collapse">
            <ul class="nav navbar-nav">
                <li class="bg-primary">Administrace<br/>[{$schoolyear}]</li>
                <li>{adminlink act="admin" obj="lecture" id=$lecture.id text="Správa předmětů"}</li>
                <li>{cmslink class="link_yellow" act="admin" obj="schoolyear" id=$lecture.id text="Změna školního roku"}</li>
                <li>{cmslink class="link_yellow" act="admin" obj="section" id=$lecture.id text="Sekce"}</li>
                <li>{cmslink class="link_yellow" act="admin" obj="article" id=$lecture.id text="Články"}</li>
                <li>{cmslink class="link_yellow" act="admin" obj="file" id=$lecture.id text="Soubory"}</li>
                <li>{cmslink class="link_yellow" act="admin" obj="urls" id=$lecture.id text="Odkazy"}</li>
                {* Users and lecturers *}
                <li>{cmslink class="link_yellow" act="admin" obj="user" id=$lecture.id text="Uživatelé"}</li>
                <li>{adminlink act="admin" obj="role" id=$lecture.id text="Správa oprávnění"}</li>
                <li>{adminlink act="admin" obj="lecturer" id=$lecture.id text="Správa seznamu učitelů pro vyučované předměty"}</li>
                {* Students *}
                <li>{cmslink act="admin" obj="student" id=$lecture.id text="Studenti"}</li>
                <li>{cmslink act="show" obj="stulec" id=$lecture.id text="Seznam všech výsledků studentů {$lecture.code}"}</li>
                <li>{cmslink act="show" obj="stulec" id=$lecture.id get="restype=2" text="Seznam studentů s nárokem na zápočet"}</li>
                <li>{cmslink act="show" obj="stulec" id=$lecture.id get="restype=3" text="Seznam studentů bez nároku na zápočet"}</li>
                <li>{adminlink act="admin" obj="import" id=$lecture.id          text="Import seznamu studentů"}</li>
                <li>{adminlink act="edit" obj="stupass" id=$lecture.id text="Hesla studentů"}</li>
                <li>{cmslink act="edit" obj="points" id=$lecture.id get="type=lec" text="Bodovat celý ročník"}</li>
                {* News *}
                <li>{cmslink act="admin" obj="news" id=$lecture.id text="Novinky"}</li>
                {* Notes *}
                <li>{cmslink act="admin" obj="note" id=$lecture.id text="Poznámky pro učitele"}</li>
                {* Exercises *}
                <li>{cmslink act="admin" obj="exclist" id=$lecture.id text="Cvičení"}</li>
                <li>{cmslink act="admin" obj="note" id=$lecture.id text="Přiřazení studentů na cvičení {$lecture.code}"}</li>
                <li>{adminlink act="admin" obj="exercise" id=$lecture.id text="Správa termínů cvičení"}</li>
                {* Task and subtasks *}
                <li>{cmslink act="admin" obj="subtask" id=$lecture.id text="Dílčí úkoly"}</li>
                <li>{cmslink act="admin" obj="task" id=$lecture.id text="Úkoly"}</li>
                <li>{cmslink act="edit"  obj="tsksub" id=$lecture.id text="Vazba dílčích úkolů na úkoly"}</li>
                <li>{cmslink act="admin" obj="evaluation" id=$lecture.id text="Vyhodnocení"}</li>
                <li>{cmslink act="admin" obj="solution" id=$lecture.id text="Řešení"}</li>
                <li>{cmslink act="admin" obj="formassign" id=$lecture.id text="Nahrát týdenní úlohy"}</li>
            </ul>
        </div><!--/.nav-collapse -->
    </div>
</div>
