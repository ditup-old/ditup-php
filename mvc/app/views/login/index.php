<?php

require_once '../app/views/general/Page.php';
require_once '../app/views/general/Header.php';


$page=new Page;
$page->title('main page');
$page->add((new Header())->generate());

$values=[
    'username'=>(isset($data, $data['values'], $data['values']['username']) ? $data['values']['username'] : '')
];

$content='<form method="post" action="">
<table>
    <tr><td>username</td><td><input type="text" name="username" placeholder="username" value="' . $values['username'] . '" /></td></tr>
    <tr><td>password</td><td><input type="password" name="password" placeholder="password" /></td></tr>
    <tr><td></td><td><input type="submit" name="login" value="log in"></td></tr>
</table>
</form>';

$page->add($content);

echo $page->generate();

