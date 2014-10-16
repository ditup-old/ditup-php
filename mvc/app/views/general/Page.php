<?php

class Page{
  
    /**head is basic data necessary for creating page head*/
    /**title:page title, css: array of stylesheets*/
    private $head=array('title'=>'','css'=>array());
    private $body='';
    /**scripts are [{link:"",properties{name:value}}] **/
    private $js=array();
    private $root_path='/';


    function __construct(){
        return $this;
    }

    /**add content to function body, $content is string**/
    public function add($content){
        $this->body.=$content;
        return $this;
    }

    public function title($title){
        $this->head['title']=$title;
        return $this;
    }
    
    public function css($link){
        $this->head['css'][]=$link;
        return $this;
    }
  
    public function js($link,$properties=array()){
        $script=array('link'=>$link,'properties'=>array());
        foreach($properties as $name=>$value){
        $script['properties'][$name]=$value;
        }
        $this->js[]=$script;
        return $this;
    }
  
  
    public function generate(){
        $code='';
    
    
    /*********write basic*/
        $code.=<<<_END
<!DOCTYPE html>
<html lang="en">
_END;
    /*********output head*************/
        $code.=<<<_END
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
_END;
        $code.='<title>ditup::'.$this->head['title'].'</title>';
    //     $code.='<link rel="icon" type="image/png" href="'.$this->root_path.'img/livegraph_icon.png" />';
//         $code.='<link rel="stylesheet" type="text/css" href="'.$this->root_path.'css/reset.css" />';
        //$code.='<link rel="stylesheet" type="text/css" href="'.$this->root_path.'css/topbar.css" />';

        foreach($this->head['css'] as $csslink){
        $code.='<link rel="stylesheet" type="text/css" href="'.$csslink.'" />';
        }
        $code.="</head>";
        /**********output body************/
        $code.='<body style="background:#fff;"><div id="main_wrapper" style="height:100%;width:100%;position:absolute;">';
        
    /************output header*********/
        
        

        
        /**end output header*********************/
        $code.= $this->body;

        $code.='</div>';

        /**scripts adding**/
        
        foreach($this->js as $script)
        {
            $code.='<script src="'.$script['link'].'" ';
            //data-main="js/App" 
            foreach($script['properties'] as $name=>$value){
                $code.=$name.'="'.$value.'" ';
            }
            
            $code.='></script>';
        }
        
        $code.='</body>';
        /**********finish*******/
        $code.='</html>';
        return $code;
    }
    
    public function setRootPath($path){
        $this->root_path=$path;
        return $this;
    }
}
