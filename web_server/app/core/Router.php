<?php
/**
 * ルーティング定義配列とPATH＿INFOを受け取り、ルーティングパラメータを
 * 特定するクラス
 */
class Router
{
    protected $routes;

    /**
     * ルーティング定義配列をコンストラクターのパラメーターとして受け取り、
     * complieRoutesメソッドに渡して変換し、$routesプロパティとして設定
     */
    public function __construct($deffinitions)
    {
        $this->routes = $this->compileRoutes($deffinitions);
    }

    public function compileRoutes($deffinitions)
    {
        $routes = array();

        foreach ($deffinitions as $url => $params) {
            $tokens = explode('/', ltrim($url, '/'));
            foreach ($tokens as $i => $token) {
                if (0 === strpos($token, ':')) {
                    $name = substr($token, 1);
                    $token = '(?P<' . $name . '>[^/]+)';
                }
                $tokens[$i] = $token;
            }

            $pattern = '/' . implode('/', $tokens);
            $routes[$pattern] = $params;
        }

        return $routes;
    }

    public function resolve($path_info)
    {
        if ('/' !== substr($path_info, 0, 1)) {
            $path_info = '/' . $path_info;
        }

        foreach ($this->routes as $pattern => $params) {
            if (preg_match('#^' . $pattern . '$#', $path_info, $matches)) {
                $params = array_merge($params, $matches);

                return $params;
            }
        }

        return false;
    }
}