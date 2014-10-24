<?php

require_once '../app/views/general/Page.php';
require_once '../app/views/general/Header.php';


$page=new Page;
$page->title('main page');
$page->add((new Header($data['loggedin'], $data['user']))->generate());

$page->add('<div>profile of ' . $data['member'] . ' (<a href="/people/' . $data['member'] . '/edit">edit</a>)</div>');

echo $page->generate();

