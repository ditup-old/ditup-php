<?php

require_once '../app/views/general/PageWithHeader.php';
require_once '../app/core/Settings.php';

$settings=Mrkvon\Ditup\Core\Settings::USER['settings'];
//print_r($settings);

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

$content=print_r($data,true).'<div>change settings of user <a href="/user/'.$data['member'].'">' . $data['member'] . '</a></div>
<form method="post" action="">
<table>
<tbody>
    <tr><th>user profile</th><td></td><td></td></tr>
    <tr>
        <th>visibility</th>
        <td>
            <select name="visibility">
';
foreach($settings['visibility'] as $value=>$name){
    $content.='                <option ' . ($data['settings']['visibility']==$value  ? 'selected="selected"' : '') . ' value="'.$value.'" >'.$name."</option>\n";
}
$content.='            </select>
        </td>
        <td>' . generate_errors('visibility', $errors) . '</td>
    </tr>
    <tr>
        <th>searchability</th>
        <td>
            <select name="searchability">
';
foreach($settings['searchability'] as $value=>$name){
    $content.='                <option ' . ($data['settings']['searchability']==$value  ? 'selected="selected"' : '') . ' value="'.$value.'" >'.$name."</option>\n";
}
$content.='            </select>
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
';
$page->add($content);
echo $page->generate();

