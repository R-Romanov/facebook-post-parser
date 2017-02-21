#Class FbPost

Class FbPost - служит для получения содержимого поста из Facebook по его ссылке.
Получать можем только посты со станиц с неограниченным доуступом (публичные страницы групп).

Использует id и secret приложения разработчика класса. 
При необходимости, их можно изменить обратившись к методам
setAppId($appId) и setAppSeret($appSecret)

Пример исспользования:

```php
$postUrl = 'https://www.facebook.com/groups/kharkovgroup/permalink/1452263168118586/';
$post = new FbPost($postUrl);
echo $post->getData();
````