<?php
$nav_links = array(
    "/artists"=>"artists",
    "/exhibitions"=>"exhibitions",
    "/news"=>"news",
    "/contact"=>"contact"
);
$title = "Birch Contemporary";
?>
<!DOCTYPE html>
<html lang="en">
    <head profile="http://www.w3.org/2005/10/profile">
        <title><?php echo $title; ?></title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no" />
        <link rel="shortcut icon" type="image/x-icon" href="/favicon.ico" />
        <link rel="icon" type="image/x-icon" href="/favicon.ico" />
        <link rel="stylesheet" href="/css/style.css" type="text/css" />
        <script type="text/javascript" src="/js/jquery-1.9.1.min.js"></script>
        <script type="text/javascript" src="/js/main.js"></script>
    </head>
    <body>
        <div id="frame">
            <header itemscope itemtype="http://schema.org/LocalBusiness">
                <h1><a href="/" itemprop="name"><span class="highlight">Birch</span> Contemporary</a></h1>
                <nav>
                    <ul>
                        <?php
                        $i = 0;
                        foreach ($nav_links as $url=>$title) {
                            $i ++;
                            echo '<li><a href="'.$url.'"';
                            if (isset($selected) && $selected == $url) {
                                echo ' class="selected"';
                            }
                            echo '>'.$title.'</a>';
                            if ($i < count($nav_links)) {
                                echo '<span class="divider">/</span>';
                            }
                            echo '</li>';
                        }
                        ?>
                    </ul>
                </nav>
                <aside itemscope itemtype="http://schema.org/PostalAddress" itemprop="address" id="gallery-address">
                    <span itemprop="streetAddress">129 Tecumseth Street</span>, <span itemprop="addressLocality">Toronto</span>, <span itemprop="addressCountry" itemscope itemtype="http://schema.org/Country"><span itemprop="name">Canada</span></span>
                </aside>
            </header>
            <div id="content">