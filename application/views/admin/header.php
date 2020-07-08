<!DOCTYPE html>
    <html>
        <head>
            <title>Birch Contemporary Admin</title>
            <script type="text/javascript" src="/js/jquery-1.9.1.min.js"></script>
            <script type="text/javascript" src="/js/admin.js"></script>
            <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
            <link rel="stylesheet" type="text/css" href="/js/jquery-ui-1.10.3.custom/css/ui-lightness/jquery-ui-1.10.3.custom.min.css" />
            <link rel="stylesheet" type="text/css" href="/css/admin.css" />
        </head>
        <body>
        <?php
        if (empty($no_menu)):
        ?>
            <nav id="menu">
                <ul>
                    <li><a href="/admin/user/edit">My Account</a></li>
                    <li><a href="/admin/exhibitions">Exhibitions</a></li>
                    <li><a href="/admin/artists">Artists</a></li>
                    <li><a href="/admin/images">Images</a></li>
                    <li><a href="/admin/news">News</a></li>
                    <?php if (!empty($user) && $user["superuser"]): ?>
                        <li><a href="/admin/users">Users</a></li>
                    <?php endif; ?>
                    <li><a href="/admin/logout">Logout</a></li>
                </ul>
            </nav>
        <?php
        endif;
        ?>
            <div id="content">