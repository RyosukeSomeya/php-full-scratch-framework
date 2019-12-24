<?php


class View
{
    protected $base_dir;
    protected $details;
    protected $layout_variables = array();

    public function __construct($base_dir, $defaults = array())
    {
        // ビューファイルを格納しているviewsディレクトリへの絶対パスを指定
        $this->base_dir = $base_dir;

        // ビューファイルに変数を渡す際、
        // デフォルトで渡す変数を設定できるようにする
        $this->defaults = $defaults;
    }

    public function setLayoutVar($name, $value)
    {
        $this->layout_variables[$name]  =  $value;
    }

    // ビューファイルを読み込むメソッド
    /**
     * $_path      ビューファイルへのパス
     * $_variables ビューファイルにわたす変数
     * $_layout    レイアウトファイル名
     */
    public function render($_path, $_variables = array(),  $_layout = false)
    {
        $_file = $this->base_dir . '/' . $_path . '.php';

        extract(array_merge($this->defaults, $_variables));

        // アウトプットバッファリング
        ob_start();
        ob_implicit_flush(0);

        require $_file;

        $content  = ob_get_clean();

        if ($_layout) {
            $content = $this->render($_layout,
                array_merge($this->layout_variables, array(
                    '_content' => $content,
                )
            ));
        }
    }

    public function  escape($string)
    {
        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }
}