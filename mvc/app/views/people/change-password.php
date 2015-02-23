<?php

require_once '../app/views/general/PageWithHeader.php';


function generate_errors($fieldname, $errors){
    if(isset($errors[$fieldname])){
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


$page=new PageWithHeader($data['loggedin'], $this->profile);
$page->title('change password');



$page->add('<div>You can change your password here:</div>
<form method="post" action="">
<table>
<tbody>
    <tr>
        <td>Old Password</td>
        <td><input type="password" name="old-password" placeholder="old password" /></td>
        <td>'.(isset($data['errors'], $data['errors']['old-password'])?generate_errors('old-password', $data['errors']):'').'</td>
    </tr>
    <tr>
        <td>New Password</td>
        <td><input type="password" name="new-password" placeholder="new password" ></td>
        <td>'.(isset($data['errors'], $data['errors']['new-password'])?generate_errors('new-password', $data['errors']):'').'</td>
    </tr>
    <tr>
        <td>Retype New One</td>
        <td><input type="password" name="new-password2" placeholder="retype new password"/></td>
        <td>'.(isset($data['errors'], $data['errors']['new-password2'])?generate_errors('new-password2', $data['errors']):'').'</td>
    </tr>
    <tr>
        <td></td>
        <td><input type="submit" name="submit" value="Change Password" /></td>
        <td></td>
    </tr>
</tbody>
</table>
</form>

');

echo $page->generate();

