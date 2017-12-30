<?php
$navigation = \App\Helpers\NavigationManagerHelper::getNavigation(true, true);
//dd($navigation);
?>

<nav class="navbar-default navbar-static-side" role="navigation">
    <div class="sidebar-collapse">
        <ul class="nav metismenu" id="side-menu">
            <li class="nav-header">
                <div class="dropdown profile-element">
                    <span>
                        <img alt="image" class="img-circle" src="{{ URL::asset('img/profile.jpg') }}" />
                     </span>
                    <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                        <span class="clear">
                            <span class="block m-t-xs">
                                <strong class="font-bold">
                                    {{  Auth::User()->name }}
                                </strong>
                            </span>
                            <span class="text-muted text-xs block">
                                Role: Manager
                                <b class="caret"></b>
                            </span>
                        </span>
                    </a>
                    <ul class="dropdown-menu animated fadeInRight m-t-xs">
                        <li><a href="profile.html">Profile</a></li>
                        <li><a href="contacts.html">Contacts</a></li>
                        <li><a href="mailbox.html">Mailbox</a></li>
                        <li class="divider"></li>
                        <li><a href="{{ url('auth/logout') }}">Logout</a></li>
                    </ul>
                </div>
                <div class="logo-element">
                    C!
                </div>
            </li>

            <?php $count = 0 ?>
            <?php foreach ($navigation as $navigation_menu): ?>

                <?php if(empty($navigation_menu['pages'])): ?>
                    <li <?php if($navigation_menu['active']) { echo 'class="active"'; } ?>>
                        <a href="<?php echo '/' . $navigation_menu['route']; ?>">
                            <i class="<?php echo $navigation_menu['icon']; ?>"></i>
                            <span class="nav-label"><?php echo $navigation_menu['title']; ?></span>
                        </a>
                    </li>
                <?php else: ?>
                    <li <?php if($navigation_menu['active']) { echo 'class="active"'; } ?>>
                        <a href="<?php echo '/' . $navigation_menu['route']; ?>">
                            <i class="<?php echo $navigation_menu['icon']; ?>"></i>
                            <span class="nav-label"><?php echo $navigation_menu['title']; ?></span>
                            <span class="fa arrow"></span>
                        </a>
                        <ul class="nav nav-second-level">
                            <?php foreach($navigation_menu['pages'] as $page): ?>

                                <?php if(!$page['visible']) continue; ?>

                                <li <?php if($page['active']) { echo 'class="active"'; } ?>>
                                    <a href="<?php echo \App\Helpers\UrlHelper::getUrl($page['controller'], $page['action']) ?>">
                                        <?php echo $page['title']; ?>
                                    </a>
                                </li>
                            <?php endforeach ?>
                        </ul>
                    </li>
                <?php endif ?>

            <?php $count++ ?>
            <?php endforeach ?>

        </ul>

    </div>
</nav>

<div id="page-wrapper" class="gray-bg">

    @include('templates.topbar')

    <div id="content">
        @include('templates.modal-message')
        @include('templates.message')
    </div>

    @yield('content')

    @include('templates.footer')

</div>

@include('templates.right-sidebar')
