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
if(isset($_SESSION['msg'])) {
    ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?=$_SESSION['msg']?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php
    unset($_SESSION['msg']);
}
include $file;
include 'footer.php';