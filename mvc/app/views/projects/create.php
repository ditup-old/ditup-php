<?php

require_once '../app/views/general/PageWithHeader.php';

function generate_errors($fieldname, $errors){
    if(isset($errors[$fieldname])){
        $ret = '';
        if(is_array($errors[$fieldname])){
            foreach($errors[$fieldname] as $err){
                $ret .= $err;
            }
        }
        elseif(is_string($errors[$fieldname])){
            $ret .= $errors[$fieldname];
        }

        return $ret;
    }
    else return '';
}

$page=new PageWithHeader($data['loggedin'], $data['user-me']);
$page->title('projects::create');

$data['values'] = isset($data['values'])?$data['values']:['projectname' => '', 'url' => '', 'subtitle' => '', 'description' => ''];
$data['errors'] = isset($data['errors'])?$data['errors']:[];

$content='
<h2>create new project</h2>
<form action="" method="post" >
<table>
    <tr>
        <td>project name</td>
        <td><input type="text" name="projectname" value="' . $data['values']['projectname'] . '" /></td>
        <td>'.generate_errors('projectname', $data['errors']).'</td>
    </tr>
    <tr>
        <td>project url</td>
        <td><input type="text" name="url" value="' . $data['values']['url'] . '" /></td>
        <td>'.generate_errors('url', $data['errors']).'</td>
    </tr>
    <tr>
        <td>subtitle/summary</td>
        <td><input type="text" name="subtitle" value="' . $data['values']['subtitle'] . '"/></td>
        <td>'.(isset($data['errors']['subtitle'])?$data['errors']['subtitle']:'').'</td>
    </tr>
    <tr>
        <td>description</td>
        <td><textarea name="description" >' . $data['values']['description'] . '</textarea></td>
        <td>' . (isset($data['errors']['description'])?$data['errors']['description']:'') . '</td>
    </tr>
    <tr>
        <td></td>
        <td><input type="submit" name="create" value="create new project" /></td>
        <td>' . (isset($data['errors']['create'])?$data['errors']['create']:'') . '</td>
    </tr>
</table>
</form>
';


$page->add($content);

echo $page->generate();

