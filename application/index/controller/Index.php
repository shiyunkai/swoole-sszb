<?php
namespace app\index\controller;

class Index
{
    public function index()
    {
        //return 'haha2';
    }

    public function hello($name = 'ThinkPHP5')
    {
        return 'hellodb,' . $name;
    }


}
