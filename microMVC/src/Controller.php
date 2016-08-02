<?php

namespace jikai\microMVC;
use jikai\Components\Validator;

abstract class Controller
{
    private $view;
    private $validator;
    protected $errorMsg;
    public $filter=array();

    public function validate(array $array,array $rules)
    {
        if(empty($this->validator)) $this->validator=new Validator();
        $res=$this->validator->validate($array,$rules);
        if(!$res) $this->errorMsg=$this->validator->errorMsg;
        return $res;
    }

    public function render($view,$var=array())
    {
        if(empty($this->view)) $this->view=Factory::View();
        $this->view->render($view,$var);
    }

    public function renderPart($view,$var=array())
    {
        if(empty($this->view)) $this->view=Factory::View();
        $this->view->clearLayout();
        $this->view->render($view,$var);
    }

    public function layout($fileName,$var=array())
    {
        if(empty($this->view)) $this->view=Factory::View();
        $this->view->layout($fileName,$var);
    }

    public function method()
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    public function redirect($uri)
    {
        Tool::redirect($uri);
    }

    public function filter($filter,$operation='enable')
    {
        $this->filter[$filter]=$operation;
    }


}