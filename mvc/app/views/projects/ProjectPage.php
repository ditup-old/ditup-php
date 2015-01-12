<?php
namespace Mrkvon\Ditup\View\Project;

use \PageWithHeader;

require_once(trim($_SERVER['DOCUMENT_ROOT']).'/mvc/app/views/general/PageWithHeader.php');

class ProjectPage extends PageWithHeader {

    /****standard use
    $pp = new ProjectPage($loggedin,$user-me);
    $pp->setMember(true);
    $pp->setAdmin(true);
    $pp->title('blabla');
    $pp->css($css_path);
    $pp->css($css_path);
    $pp->js($js_file_path);
    $pp->add($content);

    $pp->generate();
    ****/

    private $projectname = 'foo';
    private $url = 'foo';
    private $title2 = '';
    private $is_member = false;
    private $is_admin = false;
    private $subtitle = '';

    public function setMember($is_member){
        $this -> is_member = $is_member;
    }

    public function setAdmin($is_admin){
        $this -> is_admin = $is_admin;
    }

    public function setProjectname($projectname, $url){
        $this->projectname = $projectname;
        $this->url = $url;
    }

    public function setSubtitle($subtitle){
        $this->subtitle = $subtitle;
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
        $pgh->title('project::' . $this->projectname . ($this->title2===''?'':'::'.$this->title2));

        $begin_body='
    <div>
        <!--each of these menu things will be optional. no need to "support" or "follow" etc. <br /-->
        <div class="project-header" >
            <!--img class="header-avatar" src="" /-->
            <h1 class="header-project-name">'. $this->projectname . '</h1>
            <span>'.$this->subtitle.'</span>
            <ul class="header-action-menu">'
                .(
                    (!$this->is_member && $this->loggedin)
                    ?
                    '
                <li><a href="/project/'. $this->url .'/join">join</a></li>'
                    :
                    ''
                )
                .(
                    (!$this->is_member && $this->loggedin)
                    ?
                    '
                <li><a href="/project/'. $this->url .'/follow">follow</a></li>'
                    :
                    ''
                ).
                '
                <li>make as friend project to your project</li>
                <li>support</li>
                <li>chat, comment</li>'
                .(
                    ($this->loggedin && $this->is_admin)
                    ?
                    '
                <li><a href="/project/'. $this->url .'/edit">edit project</a></li>'
                    :
                    ''
                )
                .'
            </ul>
        </div>
        <nav class="side-menu">
            <!--(side) info menu-->
            <ul>
                <li><a href="/project/' . $this->url . '">general summary (landing page)</a></li>
                <li><a href="/project/' . $this->url . '/info">detailed information</a></li>
                <li><a href="/project/' . $this->url . '/people">people involved</a></li>
                <li><a href="/project/' . $this->url . '/location">location</a></li>
            </ul>
        </nav>
        <div class="project-content">';
        $end_body='
        </div>
    </div>';
        $pgh->css('/css/project.css');
        $pgh->add($begin_body.$this->content.$end_body);
        return $pgh->generate();
    }
}
