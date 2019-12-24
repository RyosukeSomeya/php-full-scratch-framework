<?php
/**
 *  フレームワークの中心
 * - Coreディレクトリ内の各クラスのオブジェクトの管理を行う
 * - ルーティング定義、コントローラーの実行、レスポンスの送信などアプリケーションの
 *   流れを管理
 */

// 抽象クラスなので、必ず継承されて子クラスで具体的に私用される
abstract class Application
{
    /**
     * 主にApplicationインスタンスの初期化処理
     */
    protected $debug = false;
    protected $request;
    protected $response;
    protected $session;
    protected $db_manager;

    public function __construct($debug = false)
    {
        $this->setDebugMode($debug);
        $this->initialize();
        $this->configure();
    }

    // デバッグモードに応じてエラー表示処理を変更する
    protected function setDebugMode($debug)
    {
        if ($debug) {
            $this->debug = true;
            ini_set('display_errors', 1);
            error_reporting(-1);
        } else {
            $this->debug = false;
            ini_set('display_errors', 0);
        }
    }

    // アプリケーションの初期化
    // 各クラスのインスタンスを生成
    protected function initialize()
    {
        $this->request    = new Request();
        $this->response   = new Response();
        $this->session    = new Session();
        $this->db_manager = new DbManager();
        $this->router     = new Router($this->registerRoutes());
    }

    protected function configure()
    {
        // 個別のアプリケーション固有の設定を記述する
    }

    // アプリケーションのルートティレクトリへのパスを返す
    // アプリケーションごととなるよう抽象メソッドとして定義
    abstract public function getRootDir();

    abstract public function registerRoutes();

    public function isDebugMode()
    {
        return $this->debug;
    }

    public function getRequest()
    {
        return $this->request;
    }

    public function getResponse()
    {
        return $this->response;
    }

    public function getSession()
    {
        return $this->session;
    }

    public function getDbManager()
    {
        return $this->db_manager;
    }

    public function getControllerDir()
    {
        return $this->getRootDir() . '/controllers';
    }

    public function getViewDir()
    {
        return $this->getRootDir() . '/views';
    }

    public function getModelDir()
    {
        return $this->getRootDir() . '/models';
    }

    public function getWebDir()
    {
        return $this->getRootDir() . '/web';
    }

    public function run()
    {
        try {
            //Routerクラスのresolveメソッドからコントローラ名とアクション名を特定
            $params = $this->router->resolve($this->request->getPathInfo());
            if ($params === false) {
                throw new HttpNotFoundException('No route found for' . $this->request->getPathInfo());
            }

            $controller = $params['controller'];
            $action     = $params['action'];

            // アクションの実行
            $this->runAction($controller, $action, $params);
        } catch (HttpNotFoundException $e) {
            $this->render404Page($e);

        } catch (UnauthorizedActionException $e) {
            list($controller, $action) = $this->login_action;
            $this->runAction($controller, $action);
        }

        // レスポンスを返す
        $this->response->send();
    }

    /**
     * コントローラーの呼び出し・実行等
     */
    public function runAction($controller_name, $action, $params = array())
    {
        // コントローラ名を生成
        $controller_class = ucfirst($controller_name) . 'Controller'; // ucfirst()先頭を大文字にする関数

        $controller = $this->findController($controller_class);
        if ($controller === false) {
            throw new HttpNotFoundException($controller_class . ' contoroller is not found.');
        }

        $content = $controller->run($action, $params);

        $this->response->setContent($content);
    }

    protected function findController($controller_class)
    {
        if (!class_exists($controller_class)) {
            // コントローラのクラスが読み込み済みでない場合、クラスファイルのパスを生成し次の処理へ
            $controller_file = $this->getControllerDir() . '/' . $controller_class . '.php';
        }

        //ファイルが存在し読み込み可能か判定
        if (!is_readable($controller_file)) {
            return false;
        } else {
            // クラスファイルの読み込みを実行
            require_once $controller_file;

            if (!class_exists($controller_class)) {
                return false;
            }
        }
        // contorollerのクラスの生成
        return new $controller_class($this);
    }

    protected function render404Page($e)
    {
        $this->response->setStatusCode(404, 'Not Found');
        $message = $this->isDebugMode() ? $e->getMessage(): 'Page not found.';
        $message = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');

        $this->response->setContent(<<<EOF
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>404</title>
</head>
<body>
    { $message }
</body>
</html>
EOF
        );
    }
}