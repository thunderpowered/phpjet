<!DOCTYPE HTML>
<html style="background-color:#fff">

<head>
    <title>404 Not Found</title>

    <?php $this->includeCSS("css/shopengine.libs.css"); ?>
    <?php $this->includeCSS("css/shopengine.all.css"); ?>
</head>
<body>
<div class="page-empty text-center">

    <h1>Страница не найдена</h1>
    <p>Запрашиваемой страницы не существует</p>
    <hr>
    <p>
        <a href="<?= \CloudStore\CloudStore::$app->router->getHost() ?>" class="btn">Продолжить покупку</a>
    </p>
</div>
</body>
</html>

