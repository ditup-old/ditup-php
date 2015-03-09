<?php

require_once '../app/views/general/PageWithHeader.php';


$page=new PageWithHeader($this->loggedin, $this->profile);
$page->title('search form');
$content=<<<_EOF
    <div>
    <form method="post" action="">
    <input type="text" name="search-string" />
    <button type="submit" name="search"><span class="fa fa-search"></span></button>

    </form>
    </div>

_EOF;
//$content.=print_r($data['notifications'], true);

$page->css('/css/notifications.css');

$page->add($content);

echo $page->generate();

