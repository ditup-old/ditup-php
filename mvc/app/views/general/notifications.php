<?php

require_once '../app/views/general/PageWithHeader.php';


$page=new PageWithHeader($this->loggedin, $this->profile);
$page->title('notifications of user '.$this->profile['username']);
$content = '<div>';
if(empty($data['notifications'])){
    $content.='you have no notifications';
}
else{
    foreach($data['notifications'] as $notice){
        $content.='<a href="/notifications/'.$notice['id'].'/go" ><div style="height:20px; background-color:green; margin:3px; '.($notice['view-time']===null?'font-weight:bold':'').'">'.$notice['text'].'</div></a> (<a href="/notifications/'.$notice['id'].'/delete">delete</a>)';
    }
}
$content.='</div>';
//$content.=print_r($data['notifications'], true);

$page->css('/css/notifications.css');

$page->add($content);

echo $page->generate();

