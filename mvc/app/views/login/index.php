<?php

require_once '../app/views/general/PageWithHeader.php';

$page=new PageWithHeader();
$page->title('login');

$values=[
    'username'=>(isset($data, $data['values'], $data['values']['username']) ? $data['values']['username'] : '')
];

$content='<form method="post" action="">
<table>
    <tr>
        <td>username</td>
        <td><input type="text" name="username" placeholder="username" value="' . $values['username'] . '" /></td>
    </tr>
    <tr>
        <td>password</td>
        <td><input type="password" name="password" placeholder="password" /></td>
    </tr>
    <tr>
        <td></td>
        <td><input type="checkbox" name="persistent" />Stay logged in</td>
    </tr>
    <tr>
        <td></td>
        <td><input type="submit" name="login" value="log in" /></td>
    </tr>
</table>
</form>
<a href="/lost-password">Forgot password?</a>';
$content.=isset($data['errors']) ? print_r($data['errors'],true) : '';

$page->add($content);

echo $page->generate();

