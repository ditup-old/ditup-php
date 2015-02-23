<?php

/**
 * This is a form for joining dits. It consists of message to current members/admins (people who will decide = approve/decline the joining), and join field.
 *
 */

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
$values = isset($data['values']) ? $data['values'] : [
    'request-message' => ''
];
//print_r($values);

$dit_url=$data['url'];
$message_from_dit=$data['message-from-dit'];

$page=new PageWithHeader($data['loggedin'], $this->profile);
$page->title('dit::'.$data['url'].'::join');
$content = '
    <div>
        <div>Sending join request for dit <a href="/dit/'.$dit_url.'">'.$dit_url.'</a></div>
        <p>'.$message_from_dit.'</p>
        <form method="post" action="">
        <table>
        <tbody>
            <tr>
                <th>message</th>
                <td><textarea name="request-message" placeholder="request-message">' . $values['request-message'] . '</textarea></td>
                <td>' . generate_errors('request-message', $errors) . '</td>
            </tr>
            <tr>
                <th></th>
                <td><input type="submit" name="submit" value="send join request" /><input type="submit" name="submit" value="cancel" /></td>
                <td>' . generate_errors('submit', $errors) . '</td>
            </tr>
            </tbody>
        </table>
        </form>
    </div>
';

$page->css('/css/dits/join-form.css');

$page->add($content);

echo $page->generate();

