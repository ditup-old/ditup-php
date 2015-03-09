<?php

require_once '../app/views/general/PageWithHeader.php';


$page=new PageWithHeader($this->loggedin, $this->profile);
$page->title('search output');

$users=$data['list']['users'];

$userlinks='';
foreach($users as $user){
    $username=$user['username'];
    $userlinks.="<li><a href=\"/user/{$username}\">{$username}</a></li>";
}

$user_list = (empty($userlinks) ? '<div>no users found</div>' : "<div><h2>users</h2><ul>{$userlinks}</ul></div>");

$dits=$data['list']['dits'];

$ditlinks='';
foreach($dits as $dit){
    $ditname=$dit['ditname'];
    $url=$dit['url'];
    $type=$dit['type'];
    $ditlinks.="<li><a href=\"/{$type}/{$url}\">{$type}::{$ditname}</a></li>";
}

$dit_list = (empty($ditlinks) ? '<div>no dits found</div>' : "<div><h2>dits</h2><ul>{$ditlinks}</ul></div>");

$output="{$user_list}{$dit_list}";

$content = <<<_EOF
    <div>
    <code>
        {$output}
    </code>
    </div>
_EOF;
//$content.=print_r($data['notifications'], true);

$page->css('/css/notifications.css');

$page->add($content);

echo $page->generate();

