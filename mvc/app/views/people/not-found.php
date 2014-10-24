<?php

require_once '../app/views/general/Page.php';
require_once '../app/views/general/Header.php';


$page=new Page;
$page->title('main page');
$page->add((new Header($data['loggedin'], $data['user']))->generate());

$page->add('member ' . $data['member'] . ' was not found or is hidden from you');

echo $page->generate();

