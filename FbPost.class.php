<?php

/**
 * Class FbPost - служит для получения содержимого поста из Facebook по его ссылке.
 *
 * Если пост общедоступный (от публичной страницы) получает его содержимое через Facebook API
 * Использует id и secret приложения разработчика класса.
 * При необходимости, их можно изменить обратившись к методам
 * setAppId($appId) и setAppSeret($appSecret)
 *
 * Если пост не общедоступный (посты от пользователей, посты от некоторых групп)
 * Получает содержимое страницы, разбирает построчно и выбирает содержимое
 *
 * Пример использования:
 * $postUrl = 'https://www.facebook.com/groups/kharkovgroup/permalink/1452263168118586/';
 * $post = new FbPost($postUrl);
 * echo $post->getData();
 *
 * @author Roman Romanov <r.romanov@protonmail.com>
 */
class FbPost
{
    private $postUrl;
    private static $appId = '348981855502122';
    private static $appSecret = 'fbe0c8aefe76aea75cc49e95b82ed6f9';

    public function __construct($_postUlr)
    {
        $this->postUrl = $_postUlr;
    }

    /**
     * Возвращает ссылку на пост
     *
     * @return mixed
     */
    public function getPostUrl()
    {
        return $this->postUrl;
    }

    /**
     * Устанавливает ссылку на пост
     *
     * @param mixed $postUrl
     */
    public function setPostUrl($postUrl)
    {
        $this->postUrl = $postUrl;
    }

    /**
     * Устанавливает appSecret Facebook приложения
     *
     * @param string $appSecret
     */
    public static function setAppSeret($appSecret)
    {
        self::$appSecret = $appSecret;
    }

    /**
     * Устанавливает id Facebook приложения
     *
     * @param string $appId
     */
    public static function setAppId($appId)
    {
        self::$appId = $appId;
    }
    

    /**
     * @param $_postUlr
     * @return array
     * @throws Exception если из ссылки не удаётся выделить id поста
     */
    private function getIdByLink($_postUlr){
        preg_match('/(?<=permalink\/)[0-9]{14,16}/', $_postUlr, $postId);
        if (empty($postId)) {
            throw new Exception('Не удаётся выделить id поста из заявленной ссылки!');
        }
        return end($postId);
    }


    /**
     * Получает html код страницы с постом
     *
     * @return string
     * @throws Exception если не удаётся получить содержимое страницы
     */
    private function getPostPageSource()
    {
        $ch = curl_init($this->postUrl);
        curl_setopt($ch, CURLOPT_POST, false);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.0; en-US; rv:1.7.12) Gecko/20050915 Firefox/1.0.7");
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $data = curl_exec($ch);
        if(!$data){
            throw new Exception('Не удаётся получить содержимое страницы!');
        }
        return curl_exec($ch);
    }

    /**
     * Получает html код содержимого поста
     *
     * @param $_string
     * @return string
     */
    private function getPostContentSource($_string)
    {
        $start = 'userContent"';
        $end = '</div>';

        $_string = ' ' . $_string;
        $ini = strpos($_string, $start);
        if ($ini == 0) return '';
        $ini += strlen($start);
        $len = strpos($_string, $end, $ini) - $ini;
        return substr($_string, $ini, $len);
    }

    /**
     * Получает содержимое из поста
     *
     * @return string
     * @throws Exception если не удаётся получить содержимое поста
     */
    public function getData()
    {

        if (strpos($this->postUrl, 'permalink')){

            $postId = $this->getIdByLink($this->postUrl);

            $response = file_get_contents(
                'https://graph.facebook.com/v2.8/'
                . urlencode($postId)
                . '?access_token=' . self::$appId . '|' . self::$appSecret);

            $response = json_decode($response);

            if (isset($response->message)) {
                return $response->message;
            } else {
                throw new Exception('Не удаётся получить содержимое поста!');
            }

        } else {
            $postSource = $this->getPostContentSource($this->getPostPageSource());
            if(!$postSource){
                throw new Exception('Не удаётся получить содержимое поста!');
            }
            return strip_tags('<div' . $postSource);
        }

    }

}

?>