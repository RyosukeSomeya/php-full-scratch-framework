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
}