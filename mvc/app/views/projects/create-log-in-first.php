<?php

require_once '../app/views/general/PageWithHeader.php';


$page=new PageWithHeader($data['loggedin'], $data['user-me']);
$page->title('projects::create');

$content='
    <div>
        To create new project, you have to <a href="/login">log in</a> first. If you don\'t have account, you can <a href="/signup">sign up</a>.
    </div>
';


$page->add($content);

echo $page->generate();

