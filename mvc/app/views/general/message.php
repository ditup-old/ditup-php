<?php

require_once '../app/views/general/PageWithHeader.php';


$page=new PageWithHeader($data['loggedin'], $data['user-me']);
$page->title('message');
$content = '
    <div>
'. $data['message'] .'
    </div>
';

$page->css('/css/message.css');

$page->add($content);

echo $page->generate();

