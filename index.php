<?php
include_once('FbPost.class.php');

$postUrls = [
        'https://www.facebook.com/groups/kharkovgroup/permalink/1452263168118586/',
        'https://www.facebook.com/groups/natalia83/permalink/1713299582021444/',
        'https://www.facebook.com/groups/1756616864597062/permalink/1849312168660864/',
    ];

foreach ($postUrls as $postUrl){
    echo 'Получаем содержимое поста: ' . $postUrl . "<br>";

    $post = new FbPost($postUrl);
    echo $post->getData(). "<hr>";
}

?>