<?php

if(!isset($page)) {
    include "../error/50x.html";
    return;
}

if(!$file = $page->getFile()) {
    include "../error/404.html";
    exit(0);
}

include 'header.php';
include $file;
include 'footer.php';