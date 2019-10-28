<?php
namespace app\index\controller;

class Index
{
    public function index()
    {
        print_r($_GET['ms']);
        return 'haha';
    }

    public function hello($name = 'ThinkPHP5')
    {
        return 'hello,' . $name;
    }
}
