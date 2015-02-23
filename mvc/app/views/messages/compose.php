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
$message = isset($data['message']) ? $data['message'] : [
    'from-user' => ['username'=>''],
    'from-project' => ['projectname' => '', 'url' => ''],
    'to-users' => [],
    'to-projects' => [],
    'subject' => '',
    'message' => '',
    'create-time' => ''
];

print_r($message);


$page=new PageWithHeader($data['loggedin'], $this->profile);
$page->title('messages::compose');
$content = '
    <div>
        <div>Sending message</div>
        <form method="post" action="/messages/compose">
        <table>
        <tbody>
            <tr>
                <th>from user</th>
                <td>' . $data['user-me'] . '</td>
                <td></td>
            </tr>
            <tr>
                <th>from project</th>
                <td><input type="text" name="from-project" value="'.$message['from-project']['url'].'" placeholder="url" /></td>
                <td></td>
            </tr>
            <tr>
                <th>to users</th>
                <td><input type="text" name="to-users" value="';
for($i=0, $len=sizeof($message['to-users']); $i<$len; $i++){
    $content.=$message['to-users'][$i]['username'].($i<$len-1 ? ', ' : '');
}

$content.='" placeholder="username, username, username" /></td>
                <td>' . generate_errors('to-users', $errors) . '</td>
            </tr>
            <tr>
                <th>to projects</th>
                <td><input type="text" name="to-projects" value="';
for($i=0, $len=sizeof($message['to-projects']); $i<$len; $i++){
    $content.=$message['to-projects'][$i]['url'].($i<$len-1 ? ', ' : '');
}
$content.='" placeholder="url, url, url" /></td>
                <td>' . generate_errors('to-projects', $errors) . '</td>
            </tr>
            <tr>
                <th>subject</th>
                <td><input type="text" name="subject" value="'.$message['subject'].'" placeholder="message subject" /></td>
                <td>' . generate_errors('subject', $errors) . '</td>
            </tr>
            <tr>
                <th>message</th>
                <td><textarea name="message" placeholder="message body">'.$message['message'].'</textarea></td>
                <td>' . generate_errors('message', $errors) . '</td>
            </tr>
            <tr>
                <th><input type="hidden" name="create-time" value="'.$message['create-time'].'"></th>
                <td><input type="submit" name="submit" value="send" /><input type="submit" name="submit" value="save to drafts" /><input type="submit" name="submit" value="cancel" /></td>
                <td>' . generate_errors('submit', $errors) . '</td>
            </tr>
            </tbody>
        </table>
        </form>
    </div>
';

$page->css('/css/messages/compose.css');

$page->add($content);

echo $page->generate();

