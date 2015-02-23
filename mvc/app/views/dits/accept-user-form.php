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


$errors=isset($data['errors'])?$data['errors']:[];
$awaiting=$data['awaiting-user'];
$dit=$data['dit'];

$content = '
<form method="post" action="" >
<table>
<tbody>
write short welcoming message to user <a href="/user/'.$awaiting.'">'.$awaiting.'</a>
    <tr>
        <td><textarea name="message">Welcome, '.htmlspecialchars($awaiting).'</textarea></td>
        <td>'.htmlspecialchars(generate_errors('database', $errors)).'</td>
    </tr>
    <tr>
        <td><input type="submit" name="accept" value="accept and send message" /><input type="submit" name="cancel" value="cancel" /></td>
        <td>'.htmlspecialchars(generate_errors('database', $errors)).'</td>
    </tr>
</tbody>
</table>
</form>
';

$page->title($dit['type'].'::'.$dit['url'].'::accept-user-'.$awaiting);
$page->add($content);
exit($page->generate());
