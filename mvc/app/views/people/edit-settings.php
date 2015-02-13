<?php

require_once '../app/views/general/PageWithHeader.php';

function generate_errors($fieldname, $errors){
    if(is_array($errors) && isset($errors[$fieldname])){
        $ret = '';
        if(is_array($errors[$fieldname])){
            for($i=0, $len=sizeof($errors[$fieldname]); $i<$len; $i++){
                $ret .= $errors[$fieldname][$i];
                $ret .= isset($errors[$fieldname][$i+1]) ? '; ' : '';
            }
        }
        elseif(is_string($errors[$fieldname])){
            $ret .= $errors[$fieldname];
        }

        return $ret;
    }
    else return '';
}

$errors = isset($data['errors']) ? $data['errors'] : [];

$page=new PageWithHeader($data['loggedin'], $data['user-me']);
$page->title('profile::' . $data['member'] . '::edit');

$page->add(print_r($data,true).'<div>change settings of user <a href="/user/'.$data['member'].'">' . $data['member'] . '</a></div>
<form method="post" action="">
<table>
<tbody>
    <tr><th>user profile</th><td></td><td></td></tr>
    <tr>
        <th>visibility</th>
        <td>
            <select name="visibility">
                <option ' . ($data['settings']['visibility']=='everybody'  ? 'selected="selected"' : '') . ' value="everybody" >everybody</option>
                <option ' . ($data['settings']['visibility']=='logged'  ? 'selected="selected"' : '') . ' value="" ></option>
                <option ' . ($data['settings']['visibility']=='nobody'  ? 'selected="selected"' : '') . ' value="nobody" >nobody</option>
            </select>
        </td>
        <td>' . generate_errors('visibility', $errors) . '</td>
    </tr>
    <tr>
        <th>searchability</th>
        <td>
            <select name="searchability">
                <option ' . ($data['settings']['searchability']=='everybody' ? 'selected="selected"' : '') . ' value="everybody" >everybody</option>
                <option ' . ($data['settings']['searchability']=='' ? 'selected="selected"' : '') . ' value="" ></option>
                <option ' . ($data['settings']['searchability']=='nobody' ? 'selected="selected"' : '') . ' value="nobody" >nobody</option>
            </select>
        </td>
        <td>' . generate_errors('searchability', $errors) . '</td>
    </tr>
    <tr>
        <th></th>
        <td><input type="submit" name="save" value="save settings"></td>
        <td>' . generate_errors('save', $errors) . '</td>
    </tr>
</tbody>
</table>
</form>
');

echo $page->generate();

