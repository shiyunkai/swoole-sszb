<?php
namespace app\index\controller;

class Index
{
    public function index()
    {
        print_r($_GET);
        return 'haha';
    }

    public function hello($name = 'ThinkPHP5')
    {
        return 'hello,' . $name;
    }
}
