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

$page = new Page($data['loggedin'], $data['user-me']);
//print_r($data);


$errors=isset($data['errors'])?$data['errors']:[];
$awaiting=$data['awaiting-user'];
$dit=$data['dit'];

$content = '
<form method="post" action="" >
<table>
<tbody>
explain to user <a href="/user/'.$awaiting.'">'.$awaiting.'</a> why you didn\'t accept her/him
    <tr>
        <td><textarea name="message">Sorry '.htmlspecialchars($awaiting).', you weren\'t accepted to '.$dit['type'].' '.$dit['url'].'</textarea></td>
        <td>'.htmlspecialchars(generate_errors('database', $errors)).'</td>
    </tr>
    <tr>
        <td><input type="submit" name="decline" value="decline and send message" /><input type="submit" name="cancel" value="cancel" /></td>
        <td>'.htmlspecialchars(generate_errors('database', $errors)).'</td>
    </tr>
</tbody>
</table>
</form>
';

$page->title($dit['type'].'::'.$dit['url'].'::accept-user-'.$awaiting);
$page->add($content);
exit($page->generate());
