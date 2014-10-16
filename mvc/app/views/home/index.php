<?php

require_once '../app/views/general/Page.php';
require_once '../app/views/general/Header.php';


$page=new Page;
$page->title('main page');
$page->add((new Header($data['loggedin'],$data['name']))->generate());

echo $page->generate();

