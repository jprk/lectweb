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
{section name=mId loop=$menuHierList}
    {if $menuHierList[mId].submenu}
        {if $menuHierList[mId].open}
                <ul class="nav">
        {else}
                </ul>
        {/if}
    {else}
        {if $menuHierList[mId].hilit}
                <li class="active">
        {else}
                <li>
        {/if}
                    {cmslink act="show" obj="section" id={$menuHierList[mId].id} text={$menuHierList[mId].mtitle}}
                </li>
    {/if}
                        {* <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">Dropdown <b class="caret"></b></a>
                            <ul class="dropdown-menu">
                                <li><a href="#">Action</a></li>
                                <li><a href="#">Another action</a></li>
                                <li><a href="#">Something else here</a></li>
                                <li class="divider"></li>
                                <li class="dropdown-header">Nav header</li>
                                <li><a href="#">Separated link</a></li>
                                <li><a href="#">One more separated link</a></li>
                            </ul>
                        </li> *}
{/section}
            {if $isAdmin || $isLecturer}
                <li class="bg-primary text-info">
                    {cmslink act="admin" obj="section" id="{$lecture.id}" text="administrace"}
                </li>
            {/if}
            </ul>
        </div><!--/.nav-collapse -->
    </div>
</div>
