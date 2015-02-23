<?php

require_once '../app/views/general/PageWithHeader.php';


$page=new PageWithHeader($data['loggedin'], $this->profile);
$page->title('404 Page Not Found');
$content = '
    <div>
        404 Page Not Found.</div>
    <div>
    If this shouldn\'t happen (i.e. standard navigation through website), please, <a href="/feedback">report</a> to the developers. Help this platform improve.
    </div>
    <div>
    Thank you.
    </div>
';

$page->css('/css/404.css');

$page->add($content);

echo $page->generate();

