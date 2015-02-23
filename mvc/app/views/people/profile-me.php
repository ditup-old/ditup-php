<?php

require_once '../app/views/general/PageWithHeader.php';

$page=new PageWithHeader($data['loggedin'], $this->profile);
$page->title('profile::'.$data['user']);

$page->add('<div>profile of ' . $data['member'] . ' (<a href="/user/' . $data['member'] . '/edit">edit</a>)</div>');

echo $page->generate();

