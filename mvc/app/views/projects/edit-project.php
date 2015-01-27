<?php

require_once '../app/views/general/PageWithHeader.php';
use \PageWithHeader as Page;

$page = new Page($data['loggedin'], $data['user-me']);
print_r($data);

$content = '
<form method = "post" action="" >
<table>
<tbody>
    <tr>
        <td>Project Name</td>
        <td><input type="text" name="projectname" value="'.$data['projectname'].'" /></td>
        <td></td>
    </tr>
    <tr>
        <td>Url</td>
        <td><input type="text" name="url" value="'.$data['url'].'" /></td>
        <td></td>
    </tr>
    <tr>
        <td>Subtitle</td>
        <td><input type="text" name="subtitle" value="'.$data['subtitle'].'" /></td>
        <td></td>
    </tr>
    <tr>
        <td>Description</td>
        <td><textarea name="description">'.$data['description'].'</textarea></td>
        <td></td>
    </tr>
    <tr>
        <td></td>
        <td><input type="submit" name="update" value="update profile" /></td>
        <td></td>
    </tr>
</tbody>
</table>
</form>
';

$page->add($content);
echo $page->generate();

