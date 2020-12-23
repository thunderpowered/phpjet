<!DOCTYPE HTML>
<html style="background-color:#fff">
    <head>
        <title>Ошибка на сайте</title>

        <style>
            .text-center {
                padding:15px;
            }
        </style>

    </head>

    <body>
    <div class="page-empty text-center">
        <h1>Произошла ошибка на сайте</h1>
        <p>Произошла ошибка на сайте. Мы уже получили сообщение об этом. Попробуйте, пожалуйста, еще раз через некоторое время.</p>
        <hr>
        <p>
            <a href="<?= \CloudStore\CloudStore::$app->router->getHost() ?>" class="btn">Вернуться на главную</a>
        </p>
    </div>
    </body>

</html>