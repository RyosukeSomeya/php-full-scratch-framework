<?php
/**
 *
 * autoloadに関する処理をまとめたクラス
 *
 */
class ClassLoader
{
    protected $diers;

    /**
     * PHPにオートローダークラスを登録する処理
     */
    public function register()
    {
        // 自分自身(ClassLoaderクラス)のloadclassメソッドを実行
        spl_autoload_register(array($this, 'loadClass'));
    }

    public function registerDir($dir)
    {
        // $dirsプロパティに渡された$dir（欲しいクラス名）を格納
        $this->dirs[] = $dir;
    }

    /**
     * autoloadが実行された際にクラスファイルを読み込む処理
     * $classには、呼び出された未定義のクラス名が渡ってくる
     */
    public function loadClass($class)
    {
        // $dirsに格納されている名前のクラスファイルがあるか探す処理
        foreach ($this->dirs as $dir) {
            $file = $dir . '/' . $class . '.php'; //pathを作成
            if (is_readable($file)) {
                require $file;

                return;
            }
        }
    }
}