<?php

require_once '../app/views/general/PageWithHeader.php';

$page=new PageWithHeader($data['loggedin'],$data['name']);
$page->title('main page');

echo $page->generate();

