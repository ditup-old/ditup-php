<?php

require_once '../app/views/general/PageWithHeader.php';

$page=new PageWithHeader($data['loggedin'], $data['user']);
$page->title('main page');

$page->add('member ' . $data['member'] . ' was not found or is hidden from you');

echo $page->generate();

