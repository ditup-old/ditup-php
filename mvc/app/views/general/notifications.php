<?php

require_once '../app/views/general/PageWithHeader.php';


$page=new PageWithHeader($this->loggedin, $this->profile);
$page->title('notifications of user '.$this->profile['username']);
$content = '<div>';
foreach($data['notifications'] as $notice){
    $content.='<a href="'.$notice['url'].'"><div>'.$notice['text'].'</div></a>';
}
$content.='</div>';

$page->css('/css/notifications.css');

$page->add($content);

echo $page->generate();

