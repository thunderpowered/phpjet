<!-- TODO match the Theme -->

<!DOCTYPE HTML>
<html style="background-color:#fff">

<head>
    <title>404 Not Found</title>
</head>
<body>
<div class="page-empty text-center">

    <h1>Страница не найдена</h1>
    <p>Запрашиваемой страницы не существует</p>
    <hr>
    <p>
        <a href="<?= \Jet\PHPJet::$app->router->getHost() ?>" class="btn">Вернуться на главную</a>
    </p>
</div>
</body>
</html>

