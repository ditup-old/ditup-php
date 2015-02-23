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

$page=new PageWithHeader($data['loggedin'], $this->profile);
$page->title('profile::' . $data['member'] . '::edit');

$page->add(print_r($data,true).'<div>edit profile of ' . $data['member'] . '</div>
<form method="post" action="">
<table>
<tbody>
    <tr>
        <th>birthday</th>
        <td><input type="checkbox" name="v_age" '.($data['profile']['v_age']?'checked="checked"':'').' /></td>
        <td><input type="date" name="birthday" value="'.$data['profile']['birthday'].'"></td>
        <td>' . generate_errors('birthday', $errors) . '</td>
    </tr>
    <tr>
        <th>gender</th>
        <td><input type="checkbox" name="v_gender" '.($data['profile']['v_gender']?'checked="checked"':'').' /></td>
        <td>
        <select name="gender">
            <option ' . ($data['profile']['gender']=='male'   ? 'selected="selected"' : '') . '>male</option>
            <option ' . ($data['profile']['gender']=='female' ? 'selected="selected"' : '') . '>female</option>
            <option ' . ($data['profile']['gender']=='other'  ? 'selected="selected"' : '') . '>other</option>
        </select>
        </td>
        <td>' . generate_errors('gender', $errors) . '</td>
    </tr>
    <tr>
        <th>web</th>
        <td><input type="checkbox" name="v_website" '.($data['profile']['v_website']?'checked="checked"':'').' /></td>
        <td><input type="text" name="website" value="'.$data['profile']['website'].'" placeholder="website" /></td>
        <td>' . generate_errors('website', $errors) . '</td>
    </tr>
    <tr>
        <th></th>
        <td><input type="checkbox" name="v_bewelcome" '.($data['profile']['v_bewelcome']?'checked="checked"':'').' /></td>
        <td><input type="text" name="bewelcome" value="' . $data['profile']['bewelcome'] . '" placeholder="bewelcome username" /></td>
        <td>' . generate_errors('bewelcome', $errors) . '</td>
    </tr>
    <tr>
        <th>about me</th>
        <td><input type="checkbox" name="v_about" '.($data['profile']['v_about']?'checked="checked"':'').' /></td>
        <td><textarea name="about" placeholder="about me">' . $data['profile']['about'] . '</textarea></td>
        <td>' . generate_errors('about', $errors) . '</td>
    </tr>
    <tr>
        <th></th>
        <td></td>
        <td><input type="submit" name="submit" value="update profile" /></td>
        <td>' . generate_errors('submit', $errors) . '</td>
    </tr>
</tbody>
</table>
</form>

<div>
<!--upload profile picture-->
<form enctype="multipart/form-data" action="" method="post">
Add/update profile picture (png, jpg, max 2M)
<input name="profile-picture" type="file">
<input type="hidden" name="MAX_FILE_SIZE" value="2097152" />
<input type="submit" value="upload photo">
</form>
</div>
');

echo $page->generate();

