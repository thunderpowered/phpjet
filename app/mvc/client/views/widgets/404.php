<!DOCTYPE HTML>
<html style="background-color:#fff">
    <head>
        <title>404 Not Found</title>
        <link href="<?php echo THEME_STATIC_URL; ?>css/shopengine.all.css" rel="stylesheet" type="text/css" media="all">
    </head>
<body>
    <div class="page-empty text-center">
        <h1>Страница не найдена</h1>
        <p>Запрашиваемой страницы не существует</p>
        <hr>
        <p>
            <a href="<?= CloudStore\App\Engine\Core\Router::getHost() ?>" class="btn">Продолжить покупку</a>
</p>
</div>
</body>
</html>

