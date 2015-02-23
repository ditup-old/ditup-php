<?php

require_once '../app/views/general/PageWithHeader.php';


$page=new PageWithHeader($data['loggedin'], $this->profile);
$page->title('error');
$content = '
    <div>
'. $data['message'] .'
    </div>
';

$page->css('/css/error.css');

$page->add($content);

echo $page->generate();

