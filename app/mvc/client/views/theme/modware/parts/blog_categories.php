<?php if (!empty($this->categories)): ?>

    <ul class="blog__categories">

        <?php foreach ($this->categories as $category): ?>

            <a class="<?php echo !empty($this->category) ? $this->category["id"] === $category["id"] ? "active" : "" : ""; ?>"
               href="<?php echo CloudStore\App\Engine\Core\Router::getHost() . "/blog/category/" . $category["id"]; ?>"><?php echo $category["name"]; ?></a>

        <?php endforeach; ?>

    </ul>

<?php endif; ?>