<?php
/**
 * Controllerクラス
 */
abstract class Controller
{
    protected $controller_name;
    protected $action_name;
    protected $application;
    protected $request;
    protected $response;
    protected $session;
    protected $db_manager;

    public function __construct($application)
    {
        $this->controller_name = strtolower(substr(get_class($this), 0, 10));

        $this->application = $application;
        $this->request     = $application->gerRequest();
        $this->response    = $application->gerResponse();
        $this->session     = $application->gerSession();
        $this->db_manager  = $application->getDbManager();
    }

    /**
     * Controller::run()メソッド
     * Applicationクラスから呼び出されアクションを実行するメソッド
     *
     * */
    public function run($action, $params = array())
    {
        $this->action_name = $action;

        // メソッドの存在をチェック
        $action_method = $action . 'Action';
        if (!method_exists($this, $action_method)) {
            $this->forward404();
        }

        // アクション実行
        $content = $this->$action_method($params);

        return $content;
    }
}