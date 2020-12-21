<section class="section--catalog preloaded">

    <div class="container">

        <div class="row">

            <div class="col-md-12">

                <div class="section--catalog__header">

                    <?php if (!empty($category)): $this->category = $category; ?>

                        <h1>Блог: <?php echo $category["name"]; ?></h1>

                    <?php elseif (!empty($tag)): ?>

                        <h1>Блог: <?php echo $tag["name"]; ?></h1>

                    <?php else: ?>

                        <h1>Блог</h1>

                    <?php endif; ?>

                </div>

            </div>

            <div class="col-md-3 col-md-push-9">

                <h3>Рубрики</h3>

                <?php $this->categories = $categories; ?>
                <?php $this->includePart("blog_categories"); ?>

            </div>


            <div class="col-md-9 col-md-pull-3">

                <?php if ($posts): ?>

                    <ul class="blog">

                        <?php foreach ($posts as $post): ?>

                            <li class="blog__article">

                                <h3><?php echo $post["title"]; ?></h3>
                                <span class="blog__snippet"><?php echo $post["snippet"]; ?></span>
                                <a class="blog__link" href="<?php echo CloudStore\App\Engine\Core\Router::getHost() . "/blog/" . $post["url"]; ?>">Подробнее</a>

                            </li>

                        <?php endforeach; ?>

                    </ul>

                <?php else: ?>

                    <span class="notice--article">Записей нет</span>

                <?php endif; ?>

            </div>


        </div>

    </div>

</section>