<?php

require_once('Page.php');
require_once('Header.php');

class PageWithHeader extends Page {
    protected $loggedin=false;
    protected $user_me='foo';
    protected $content='';

    function __construct($loggedin=false, $profile=['username'=> '?', 'unseen-messages'=>'?']){
        $this->css($this->root_path.'css/header.css');
        $this->loggedin = $loggedin;
        $this->user_me = is_array($profile) ? (isset($profile['username'])?$profile['username']:'not-logged-in') : $profile;
        $this->profile = (is_array($profile) && isset($profile['username'])) ? $profile : ['username' => $this->user_me, 'unseen-messages'=>'?'];
        return $this;
    }

    /**add content to function body, $content is string**/
    public function add($content){
        $this->content=$content;
        return $this;
    }

    public function generate(){
        $pg = new Page();
        foreach($this->head['css'] as $css){
            $pg->css($css);
        }
        $pg->title($this->head['title']);
        foreach($this->js as $js){
            $pg->js($js);
        }
        

        $hdr = (new Header($this->loggedin, $this->profile))->generate();
        $content = $hdr.$this->content;
        $pg->add($content);
        return $pg->generate();
    }
    
    public function setRootPath($path){
        $this->root_path=$path;
        return $this;
    }
}
