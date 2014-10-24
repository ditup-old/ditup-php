<?php

require_once '../app/views/general/PageWithHeader.php';

$page=new PageWithHeader($data['loggedin'], $data['user']);
$page->title('people');

$page->add('<div>this is general people page</div>');

echo $page->generate();

