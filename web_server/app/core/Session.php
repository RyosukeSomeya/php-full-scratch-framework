<?php
/**
 * セッション情報を管理するクラス
 */
class Session
{
    protected static $sessionStarted       = false;
    protected static $sessionIdRegenerated = false;

    public function __construct()
    {
        if (!self::$sessionStarted) {
            session_start();

            self::$sessionStarted = true;
        }
    }

    public function set($name, $value)
    {
        $_SESSION[$name] = $value;
    }

    public function get($name, $default = null)
    {
        if (isset($_SESSION[$name])) {
            return $_SESSION[$name];
        }

        return $default;
    }

    public function remove($name)
    {
        // $_SESSIONから指定した値を取り除く
        unset($_SESSION[$name]);
    }

    public function clear()
    {
        // $_SESSIONを空にする
        $_SESSION = array();
    }

    public function regenerate($destroy = true)
    {
        if (!self::$sessionIdRegenerated) {
            // session_regenerate_id()
            // セッションIDを新しく発行する関数
            // 現在のセッションIDを新しいものと置き換える。
            // その際、session情報は維持される。
            session_regenerate_id($destroy);

            self::$sessionIdRegenerated = true;
        }
    }

    public function setAuthenticated($bool)
    {
        $this->set('_authenticated', (bool)$bool);

        // ログイン後にセッションIDを再発行
        $this->regenerate();
    }

    public function isAuthenticated()
    {
        return $this->get('_authenticated', false);
    }
}