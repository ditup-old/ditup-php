<?php

require_once '../app/views/general/PageWithHeader.php';
use \PageWithHeader as Page;

$page = new Page($data['loggedin'], $data['user-me']);
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

//print_r($data);

//types of dits present
$dit_opt = ['idea', 'project', 'interest', 'topic', 'issue'];

$values=$data['profile'];
$errors=isset($data['errors'])?$data['errors']:[];
$content = '
<form method = "post" action="" >
<table>
<tbody>
    <tr>
        <td>Dit Name</td>
        <td><input type="text" name="ditname" value="'.$values['ditname'].'" /></td>
        <td>'.generate_errors('ditname', $errors).'</td>
    </tr>
    <tr>
        <td>Url</td>
        <td>'.$values['url'].'<!--input type="text" name="url" value="'.$values['url'].'" /--></td>
        <td><a href="edit-url">edit url</a></td>
    </tr>
    <tr>
        <td>dit type</td>
        <td>
            <select name="type" >';
foreach($dit_opt as $option){
    $content.='
                <option value="'.$option.'" '.($option==$values['type']?'selected="selected"':'').'>'.$option.'</option>';
}
$content.=                '
            </select>
        </td>
        <td>'.generate_errors('type', $errors).'</td>
    </tr>
    <tr>
        <td>Subtitle</td>
        <td><input type="text" name="subtitle" value="'.$values['subtitle'].'" /></td>
        <td>'.generate_errors('subtitle', $errors).'</td>
    </tr>
    <tr>
        <td>Description</td>
        <td><textarea name="description">'.$values['description'].'</textarea></td>
        <td>'.generate_errors('description', $errors).'</td>
    </tr>
    <tr>
        <td></td>
        <td><input type="submit" name="save" value="update profile" /> <a href="/dit/'.$values['url'].'">cancel</a></td>
        <td>'.generate_errors('save', $errors).'</td>
    </tr>
    <tr>
        <td></td>
        <td><a href="/'.(isset($values['old-type'])?$values['old-type']:$values['type']).'/'.$values['url'].'/edit">refresh values</a></td>
        <td>'.generate_errors('database', $errors).'</td>
    </tr>
</tbody>
</table>
</form>
';

$page->title((isset($values['old-type'])?$values['old-type']:$values['type']).'::'.$values['url'].'::edit');
$page->add($content);
echo $page->generate();

