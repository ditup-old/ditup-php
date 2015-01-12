<?php
namespace Mrkvon\Ditup\View\User;

use \PageWithHeader;

require_once(trim($_SERVER['DOCUMENT_ROOT']).'/mvc/app/views/general/PageWithHeader.php');

class UserPage extends PageWithHeader {

    /****standard use
    $up = new UserPage($loggedin,$user-me);
    $up->setMe(true);
    $up->title('blabla');
    $up->css($css_path);
    $up->js($js_file_path);
    $up->add($content);

    $up->generate();
    ****/

    private $username = 'foo';
    private $title2 = '';
    private $is_me = false;

    public function setMe($is_me){
        $this -> is_me = $is_me;
    }

    public function setUsername($username){
        $this->username = $username;
    }

    public function title($title){
        $this->title2 = $title;
        return $this;
    }
    
    public function generate(){
        $pgh = new PageWithHeader($this->loggedin, $this->user_me);
        foreach($this->js as $js){
            $pgh->js($js);
        }
        foreach($this->head['css'] as $css){
            $pgh->css($css);
        }
        $pgh->title('user::' . $this->username . ($this->title2===''?'':'::'.$this->title2));

        $begin_body='
    <div>
        <!--each of these menu things will be optional-->
        <div class="profile-header" >
            <img class="header-avatar" src="" /><h1 class="header-user-name">'. $this->username . '</h1>
            <ul class="header-action-menu">'.
            (
            $this->is_me ? 
            ''
            :
            '
                <li>message</li>'
            ).
            (
            $this->is_me ? 
            ''
            :
            '
                <li>create connection (friends etc.)</li>'
            ).
            '
                <li>follow</li>
                <li>reference, comment</li>
                <li>chat</li>'.
            (
            $this->is_me ? 
            '
                <li><a href="/user/'.$this->username.'/edit">edit profile</a></li>'
            :
            ''
            ).
            '
            </ul>
        </div>
        <nav class="side-menu">
            <!--(side) info menu-->
            <ul>
                <li><a href="/user/' . $this->username . '">general summary (landing page)</a></li>
                <li><a href="/user/' . $this->username . '/info">personal info</a></li>
                <li><a href="/user/' . $this->username . '/projects">projects</a></li>
                <li><a href="/user/' . $this->username . '/interests">interests, what she wants to do</a></li>
                <li><a href="/user/' . $this->username . '/activity">recent activity</a></li>
                <li><a href="/user/' . $this->username . '/connections">connections (friends)</a></li>
                <li><a href="/user/' . $this->username . '/references">references (' . '0' . ')</a></li>
            </ul>
        </nav>
        <div class="user-profile-content">';
        $end_body='
        </div>
    </div>';
        $pgh->css('/css/profile.css');
        $pgh->add($begin_body.$this->content.$end_body);
        return $pgh->generate();
    }
}
