<?php

class MiniBlogApplication extends Application
{
    protected $login_action = array('account', 'signin');

    public function getRootDir()
    {
        return dirname(__FILE__);
    }

    protected function registerRoutes()
    {
        // ルーティング定義配列を返す
        return array(
            '/account' => array('controller' => 'account', 'action' => 'index'),
            '/account/:action' => array('controller'  => 'account'),
        );
    }

    protected function configure()
    {
        // DBの接続設定
        $this->db_manager->connect('master', array(
            'dsn'      => 'mysql:dbname=mini_blog;host=mysql_server',
            'user'     => 'root',
            'password' => 'root'
        ));
    }
}