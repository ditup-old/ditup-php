<?php

require_once '../app/views/general/PageWithHeader.php';
use \PageWithHeader as Page;

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

$page = new Page($data['loggedin'], $this->profile);
//print_r($data);

$values=$data['profile'];
$errors=isset($data['errors'])?$data['errors']:[];
$content = '
<h2>edit url of dit <i>'.$values['ditname'].'</i> with url <i>'.$values['url'].'</i></h2>
<div><!--disclaimer about bad practice when changing url-->
BE AWARE: Url is the unique identifier of your '.$values['type'].'. By using <a href="/'.$values['type'].'/'.$values['url'].'" target="_blank">url</a>, interested people may find and share it. Please be aware that by changing this url you may cause confusion if this '.$values['type'].' was already shared between people on the internet, in forums etc. If it is already established among people, it is very bad practice to change its url. You may cause frustration to people searching for this '.$values['type'].'. Moreover your old url will be freely available for other <a href="/dits" target="_blank">dits</a>. You will not have right to get your old url back, if somebody claims it. But it is your free choice, now educated.
</div>
<form method = "post" action="" >
<table>
<tbody>
    <tr>
        <td>dit name</td>
        <td>'.$values['ditname'].'</td>
    </tr>
    <tr>
        <td>old url</td>
        <td>'.$values['url'].'</td>
        <td></td>
    </tr>
    <tr>
        <td>new url</td>
        <td><input type="text" name="new-url" value="'.$values['new-url'].'" /></td>
        <td>'.generate_errors('url', $errors).'</td>
    </tr>
    <tr>
        <td></td>
        <td><input type="submit" name="save" value="change url" /> <a href="/dit/'.$values['url'].'/edit">cancel</a></td>
        <td>'.generate_errors('database', $errors).'</td>
    </tr>
</tbody>
</table>
</form>
';

$page->title($values['type'].'::'.$values['url'].'::edit-url');
$page->add($content);
echo $page->generate();

