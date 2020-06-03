<?php

if(!isset($page)) {
    include "../error/50x.html";
    return;
}

include 'header.php';
include $page->getFile();
include 'footer.php';