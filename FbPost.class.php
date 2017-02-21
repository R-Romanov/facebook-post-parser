<?php

/**
 * Class FbPost - служит для получения содержимого поста из Facebook по его ссылке.
 *
 * Использует id и secret приложения разработчика класса.
 * При необходимости, их можно изменить обратившись к методам
 * setAppId($appId) и setAppSeret($appSecret)
 *
 * Пример исспользования:
 * $postUrl = 'https://www.facebook.com/groups/kharkovgroup/permalink/1452263168118586/';
 * $post = new FbPost($postUrl);
 * echo $post->getData();
 *
 * @author Roman Romanov <r.romanov@protonmail.com>
 */
class FbPost
{
    private $id;
    private static $appId = '348981855502122';
    private static $appSecret = 'fbe0c8aefe76aea75cc49e95b82ed6f9';

    public function __construct($_postUlr)
    {
        preg_match('/(?<=permalink\/)[0-9]{14,16}/', $_postUlr, $postId);
        if (empty($postId)) {
            throw new Exception('Не удаётся выделить id поста из заявленной ссылки!');
        }
        $this->id = end($postId);
    }

    /**
     * Устанавливает id поста
     *
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
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
     * Возвращает id поста
     *
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Получает содержимое из поста
     *
     * @return string
     * @throws Exception если не удаётся получить содержимое поста
     */
    public function getData()
    {
        $response = file_get_contents(
            'https://graph.facebook.com/v2.8/'
            . urlencode($this->id)
            . '?access_token=' . self::$appId . '|' . self::$appSecret);

        $response = json_decode($response);

        if (isset($response->message)) {
            return $response->message;
        } else {
            throw new Exception('Не удаётся получить содержимое поста!');
        }
    }

}

?>