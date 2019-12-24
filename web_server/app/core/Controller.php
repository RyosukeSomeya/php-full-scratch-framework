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

    protected function render($variables = array(), $templete = null, $layout = 'layout')
    {
        $defaults = array(
            'request'  => $this->request,
            'base_url' => $this->request>getBaseUrl(),
            'session'  => $this->session,
        );

        // Viewクラスをインスタンス化
        $view = new View($this->application->getViewDir(), $defaults);

        if (is_null($template)) {
            $templete = $this->action_name;
        }

        $path = $this->controller_name . '/' . $template;

        return $view->render($path, $variables, $layout);
    }
}