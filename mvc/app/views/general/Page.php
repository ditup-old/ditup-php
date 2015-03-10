<?php

class Page{
  
    /**head is basic data necessary for creating page head*/
    /**title:page title, css: array of stylesheets*/
    protected $root_path='/';
    protected $head=array('title'=>'','css'=>['/css/reset.css', '/fonts/font-awesome-4.3.0/css/font-awesome.min.css']);
    protected $body='';
    /**scripts are [{link:"",properties{name:value}}] **/
    protected $js=[
    ];


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
        $code.='
    <title>ditup::'.$this->head['title'].'</title>';
    //     $code.='<link rel="icon" type="image/png" href="'.$this->root_path.'img/livegraph_icon.png" />';
//         $code.='<link rel="stylesheet" type="text/css" href="'.$this->root_path.'css/reset.css" />';
        //$code.='<link rel="stylesheet" type="text/css" href="'.$this->root_path.'css/topbar.css" />';

        foreach($this->head['css'] as $csslink){
            $code.='
    <link rel="stylesheet" type="text/css" href="'.$csslink.'" />';
        }
        $code.='
</head>';
        /**********output body************/
        $code.='
<body style="background:#fff;">';
        
    /************output header*********/
        
        

        
        /**end output header*********************/
        $code.="\n";
        $code.= $this->body;

        /**scripts adding**/
        
        foreach($this->js as $script)
        {
            //exit(print_r($script, true));
            $code.='<script src="'.$script['link'].'" ';
            //data-main="js/App" 
            foreach($script['properties'] as $name=>$value){
                $code.=$name.'="'.$value.'" ';
            }
            
            $code.='></script>';
        }
        
        $code.='
</body>';
        /**********finish*******/
        $code.='
</html>';
        return $code;
    }
    
    public function setRootPath($path){
        $this->root_path=$path;
        return $this;
    }
}
