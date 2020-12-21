<?php
// Мда...

if (isset($_POST['submit'])) {
    $rr = $_POST['pass'];
    if ($rr === '1234') {
        $_SESSION['ok'] = true;
        header('Location: /', true, 301);
    }
}

?>
<form action="" method="POST">
    <input type="password" name="pass"/>
    <input type="submit" name="submit"/>
</form>

