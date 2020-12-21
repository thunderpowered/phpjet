<?php

namespace CloudStore\App\Engine\Tools;

use CloudStore\App\Engine\ActiveRecord\Tables\Products;
use CloudStore\App\Engine\Config\Config;
use CloudStore\App\Engine\Core\Router;
use CloudStore\App\Engine\Core\Store;
use CloudStore\CloudStore;

/**
 * Class ProductManager
 * @package CloudStore\App\Engine\Tools
 * @deprecated
 */
class ProductManager
{

    public static $sql;
    public static $params;
    public static $num;
    public static $count;

    public static function load(array $conditions = null, int $amount = 20, bool $mainPage = false): array
    {
        // fuck...
        // todo: first of all, i need to entirely redone all the code about products, cuz it's just a complete fucking mess
        ProductManager::$count = Products::count($conditions);

        if (!ProductManager::$count) {
            return array();
        }

        ProductManager::$num = $amount;

        $pagination = CloudStore::$app->tool->paginator->preparePagination(ProductManager::$count, $amount);

        // Sorting by amount in stock
        $orderBy = [
            "quantity_available" => "DESC",
            "quantity_stock" => "DESC",
            "quantity_possible" => "DESC"
        ];

        if ($mainPage) {
            $orderBy = array_merge($orderBy, array("products_main" => "DESC"));
        } else {
            $orderBy = array_merge($orderBy, $pagination["orderBy"]);
        }


        if ($amount) {

            $products = Products::get($conditions, $orderBy, $pagination["limit"]);
        } else {

            $products = Products::get($conditions, $orderBy);
        }

        return ProductManager::proceedProducts($products);
    }

    public static function loadExec(string $sql, array $params = NULL, int $num = 20, bool $main = false)
    {

        // Temp
        //@todo add new method CloudStore::$app->store->countExec() or something like this (i'm talking about name)
        // And remove static properties maybe. For what are they needed in this situation?

        // TODO: bad idea, very-very bad idea.
        ProductManager::$count = count(Store::execGet($sql, $params));

        if (!ProductManager::$count) {
            return false;
        }

        ProductManager::$num = $num;

        $array = Paginator::preparePagination(ProductManager::$count, $num);

        $quantityOrderBy = "quantity_available DESC, quantity_stock DESC, quantity_possible DESC";

        if ($main) {
            $orderBy = "products_main DESC, $quantityOrderBy, viewed DESC";
        } else {
            $orderBy = $quantityOrderBy . ", " . $array["sorting_str"];
        }

        if ($num) {

            $products = Store::execGet($sql . "ORDER BY " . $orderBy . $array["orderByString"], $params, false);
        } else {

            $products = Store::execGet($sql, $params);
        }

        return ProductManager::proceedProducts($products);
    }

    public static function updateRating(int $id = 0): bool
    {
        $product = CloudStore::$app->store->loadOne("products", ["id" => $id]);
        if (!$product) {
            return false;
        }

        $reviews = Store::load("reviews", ["product" => $id]);
        if (!$reviews) {
            Store::update("products", ["rating" => 0], ["id" => $id]);
            return false;
        }

        $amount = count($reviews);
        $sum = 0;
        for ($i = 0; $i < $amount; $i++) {
            $sum += $reviews[$i]["rating"];
        }

        $rating = $sum / $amount;

        // TODO: why is this check?
        if ($rating < 1 || $rating > 5) {
            Store::update("products", ["rating" => 0], ["id" => $id]);
            return false;
        }

        $rounded = round($rating);
        Store::update("products", ["rating" => $rounded], ["id" => $id]);

        return true;
    }

    public static function getOrderData($id)
    {

        $info = CloudStore::$app->store->loadOne("orders", ["orders_id" => $id]);

        $products = Store::execGet("SELECT * FROM order_products o 
        LEFT JOIN products p ON o.products_handle = p.handle AND o.store = p.store  
        LEFT JOIN products_inventory pi ON o.products_modification = pi.id AND o.store = pi.store 
        WHERE o.orders_final_id = :id AND p.title <> '' AND o.store = :store", [
            ":id" => $id, ":store" => Config::$config["site_id"]
        ]);

        foreach ($products as $key => $product) {
            if ($product["products_modification"] !== "0" && $modification = CloudStore::$app->store->loadOne("products_inventory", ["product" => $product["id"], "id" => $product["products_modification"]])) {
                $products[$key]["title"] = $product["title"] . " [" . $modification["modification"] . "]";
            }
        }

        return [
            'info' => $info,
            'products' => $products
        ];
    }

    /**
     * @param $price
     * @return array
     */
    public static function usePoints($price)
    {
        if (!Request::getSession('user_is_logged')) {
            return $price;
        }

        $id = Request::getSession('user_id');

        $user = CloudStore::$app->store->loadOne("users", ["users_id" => $id]);

        $points = $user['users_points'];

        //60% of price
        $p60 = ($price / 100) * 60;

        if ($p60 >= $points) {
            $final = $price - $points;
            $points = 0;
        } elseif ($p60 < $points) {
            $final = $price - $p60;
            $points = $points - ceil($p60);
        }

        $delta = $price - $final;

        Request::setSession('checkout_new_points', $points);
        Request::setSession('checkout_price_delta', $delta);

        return [
            'final' => $final,
            'points' => $points,
            'delta' => $delta
        ];
    }

    // TODO: refactor
    /**
     * @param Products[] $rows
     * @return array
     */
    public static function proceedProducts(array $rows): array
    {
        // todo: function proceedProducts should be entirely redone
        return [];

        if (!$rows) {
            return [];
        }

        $products = array();
        foreach ($rows as $product) {

            if ($product->old_price !== 0) {
                $sales_news_popular = '<div class="product-tag product-tag--absolute" aria-hidden="true">%</div>';
            } else {
                $sales_news_popular = '';
            }

            $image = CloudStore::$app->tool->utils->resizeImage(IMAGES . $product->image, null, null, $product->title, 'px');
            $thumb = CloudStore::$app->tool->utils->resizeImage(THUMBNAILS . $product->image, null, null, $product->title, 'px');

            /**
             * Calculate old price
             */
            $oldPrice = (float)$product->old_price;
            if ($oldPrice) {
                $oldPrice = CloudStore::$app->tool->utils->asPrice($product->old_price);
            } else {
                $oldPrice = null;
            }

            $categoryID = 0;
            $categoryURL = "all";

            $cat = null;
            $modifications = null;

            $products_shipping_date = (int)$product->products_shipping_date;

            // TODO: why is it?
            if ($products_shipping_date > 5000) {
                $products_shipping_date = 5000;
            }

            $status = self::getStatus($product->quantity_available, $product->quantity_stock, $products_shipping_date, $product->quantity_possible, $product->products_possible_shipping_date, $product->products_shipping_expectation, $product->id);

            /**
             * Calculate some shipping information
             */
            $date_1 = new \DateTime("now");

            $products_shipping_date = date("Y-m-j", time() + $products_shipping_date * (86400));
            $date_2 = \DateTime::createFromFormat('Y-m-j', $products_shipping_date);

            $diff = $date_2->diff($date_1);
            $shipping_diff = $diff->format("%d");

            /**
             * Prepare the description
             */
            $description = $product->description;
            $controllerName = CloudStore::$app->router->getControllerName();
            if ($controllerName === 'catalog' || $controllerName === 'products') {
                $description = self::prepareDescription($product->description, $product->id);
            }

            /**
             * Favorite - is product favorite to specific user
             */
            $favorite = false;
            $ip = CloudStore::$app->system->request->getUserIP();
            if (CloudStore::$app->store->loadOne("favorites", ["product" => $product->id, "ip" => $ip])) {
                $favorite = true;
            }

            /**
             * MyRating - rating for each user
             */
            $myRating = CloudStore::$app->store->loadOne("reviews", ["product" => $product->id, "ip" => $ip]);
            if ($myRating) {
                $myRating = $myRating["rating"];
            } else {
                $myRating = 0;
            }

            /**
             * SKU - Stock Keeping Unit
             */
            $sku = "Арт. " . ($product->products_sku ? htmlentities($product->products_sku) : $product->products_sku);

            /**
             * Create final array
             */
            // TODO: normalize array
            $products[] = array(
                'id' => $product->id,
                'handle' => $product->handle,
                'sales' => $sales_news_popular,
                'image' => $image,
                'image_link' => $product->image,
                'image_thumb' => $thumb,
                'image_thumb_lnk' => 'thumbnails/' . $product->image,
                'title' => $product->title,
                'description' => $description,
                'message' => $product->message,
                'desc_clear' => CloudStore::$app->tool->utils->removeSpecialChars($product->description),
                'count' => $status['count'],
                'modifications' => $modifications,
                'old_price' => $oldPrice,
                'price' => Utils::asPrice($product->price),
                'price_int' => round($product->price),
                'brand' => $product->brand,
                'category' => CloudStore::$app->tool->utils->removeSpecialChars($cat),
                'category_full' => $cat,
                'category_link' => $categoryURL,
                'category_id' => $categoryID,
                'shipping_date' => $status['shipping_date'],
                'shipping_avail' => $status['shipping_avail'],
                'shipping_diff' => $shipping_diff,
                'products_sku' => $sku,
                'viewed' => $product->viewed,
                'favorite' => $favorite,
                'rating' => $product->rating,
                'my_rating' => $myRating
            );

            $oldPrice = NULL;
            $sales_news_popular = NULL;
        }

        return $products;
    }

    public static function getCategories(int $id)
    {

        $categories = Store::execGet("SELECT * FROM products_category pc INNER JOIN category c ON pc.category_id = c.category_id AND pc.store = c.store WHERE pc.id = ? AND pc.store = ?", [$id, \CloudStore\App\Engine\Config\Config::$config["site_id"]]);

        if (!$categories) {
            return false;
        }

        // TODO: no HTML in ENGINE
        foreach ($categories as $_category) {
            if (!empty($category)) {
                $category .= ", " . "<a href=\"" . Router::getHost() . "/catalog/" . $_category["category_handle"] . "\">" . $_category["name"] . "</a>";
            } else {
                $category = "<a href=\"" . Router::getHost() . "/catalog/" . $_category["category_handle"] . "\">" . $_category["name"] . "</a>";
            }
        }

        return $category;
    }

    public static function getModifications(int $id, array $row): array
    {

        $modifications = Store::load("products_inventory", ["product" => $id]);
        if (!$modifications) {
            return [];
        }

        foreach ($modifications as $key => $modification) {

            $status = self::getStatus($modification['quantity_available'], $modification['quantity_stock'], $modification['products_shipping_date'], $modification['quantity_possible'], $modification['products_possible_shipping_date'], $row['products_shipping_expectation'], $row['id']);

            $modifications[$key]["status"] = $status;

            if (!$status['shipping_avail']) {
                unset($modifications[$key]);
            }
        }

        return $modifications;
    }

    public static function getStatus($count, $quantity, $date, $possible_quantity, $possible_date, $expectation, $id)
    {
        $shipping_date = null;
        $shipping_avail = null;

        $date = intval($date);
        $date = date("Y-m-j", time() + $date * (86400));

        $count = self::getcount($count, $quantity, $expectation, $date, $id);

        // @todo remove
        if ($possible_date === "0000-00-00") {

            $possible_date = null;
        }

        if ($count > 0) {

            $shipping_date = "сегодня";
            $shipping_avail = true;
        } elseif ($count <= 0 AND $count > (0 - $quantity)) {

            $shipping_date = Utils::getLocalTime("j F Y", strtotime($date)) . ' г.';

            // Get difference

            if ($date) {

                $date_1 = \DateTime::createFromFormat('j-m-Y', date("j-m-Y"));
                $date_2 = \DateTime::createFromFormat('Y-m-j', $date);

                $diff = $date_2->diff($date_1);

                if ($diff AND $diff->format("%d") === "1") {

                    $shipping_date = "завтра";
                }
            }

            $shipping_avail = true;
        } elseif ($count <= 0 AND $count < (0 - $quantity) AND $possible_date AND ($count > (0 - ($quantity + $possible_quantity)))) {

            $shipping_date = Utils::getLocalTime("j F Y", strtotime($possible_date)) . ' г.';

            // Get difference (without DateTime Object)
            $date_1 = \DateTime::createFromFormat('j-m-Y', date("j-m-Y"));
            $date_2 = \DateTime::createFromFormat('Y-m-j', $possible_date);

            $diff = $date_2->diff($date_1);

            if ($diff AND $diff->format("%d") === "1") {

                $shipping_date = "завтра";
            }

            $shipping_avail = true;
        } elseif (+$count === 0 AND +$count === (0 - $quantity) AND +$possible_quantity !== 0 AND $possible_date) {

            // Get difference
            $date_1 = \DateTime::createFromFormat('j-m-Y', date("j-m-Y"));
            $date_2 = \DateTime::createFromFormat('Y-m-j', $possible_date);

            $shipping_date = Utils::getLocalTime("j F Y", $date_2->getTimestamp()) . ' г.';

            $diff = $date_2->diff($date_1);

            if ($diff AND $diff->format("%d") === "1") {

                $shipping_date = "завтра";
            }

            $shipping_avail = true;
        } else {

            $shipping_date = 'нет в наличии';
            $shipping_avail = false;
        }

        return [
            'shipping_date' => $shipping_date,
            'shipping_avail' => $shipping_avail,
            'count' => $count
        ];
    }

    public static function getCount($count, $quantity, $expectation, $date, $id)
    {
        if ($expectation === '1') {

            if (strtotime($date) - time() <= 0) {

                $count = (intval($count) + intval($quantity));

                self::updateCount($count, $id);

                return $count;
            } else {

                return $count;
            }
        } else {

            return $count;
        }
    }

    public static function updateCount($count, $id)
    {
        return Store::update("products", ["quantity_available" => $count, "products_shipping_expectation" => 0], ["id" => $id]);
    }

    public static function prepareDescription($description, $id)
    {
        $specifications = Store::load("sef_filters", ["id" => $id], ["filter_order" => "ASC"], [], false);

        // Sorting
        if ($specifications) {

            $zeros = [];
            foreach ($specifications as $key => $value) {

                if ($value["filter_order"] === "0") {
                    $zeros[] = $specifications[$key];

                    unset($specifications[$key]);
                }
            }

            $specifications = array_merge($specifications, $zeros);
        }

        $table = NULL;

        for ($i = 0, $c = count($specifications); $i < $c; $i++) {

            if ($i === 0) {

                if ($specifications[$i]['filter_category'] === "1") {
                    $table .= "<h4>" . $specifications[$i]['attribute_name'] . "</h4>";
                    $table .= "<table class=\"product-single__table-specifications\">";
                } else {
                    $table .= "<table class=\"product-single__table-specifications\">";

                    if (empty($specifications[$i]['value_name'])) {
                        continue;
                    }

                    $table .= "<tr>"
                        . "<td class=\"product-single__table-left\"><div><span>" . $specifications[$i]['attribute_name'] . "<span></div><div class=\"dotted\"></div></td>"
                        . "<td class=\"product-single__table-right\"><div><span>" . $specifications[$i]['value_name'] . "<span></div></td>"
                        . "</tr>";
                }

                if (($i + 1) >= count($specifications)) {
                    $table .= "</table>";
                }
            } elseif (($i + 1) >= count($specifications)) {

                if (!empty($specifications[$i]['value_name'])) {
                    $table .= "<tr>"
                        . "<td class=\"product-single__table-left\"><div><span>" . $specifications[$i]['attribute_name'] . "<span></div><div class=\"dotted\"></div></td>"
                        . "<td class=\"product-single__table-right\"><div><span>" . $specifications[$i]['value_name'] . "<span></div></td>"
                        . "</tr>";
                }

                $table .= "</table>";
            } else {

                if ($specifications[$i]['filter_category'] === "1") {
                    $table .= "</table>";
                    $table .= "<h4>" . $specifications[$i]['attribute_name'] . "</h4>";
                    $table .= "<table class=\"product-single__table-specifications\">";
                } else {
                    if (empty($specifications[$i]['value_name'])) {
                        continue;
                    }

                    $table .= "<tr>"
                        . "<td class=\"product-single__table-left\"><div><span>" . $specifications[$i]['attribute_name'] . "<span></div><div class=\"dotted\"></div></td>"
                        . "<td class=\"product-single__table-right\"><div><span>" . $specifications[$i]['value_name'] . "</span></div></td>"
                        . "</tr>";
                }
            }
        }

        if ($specifications) {
            return str_replace("{{SPECIFICATIONS}}", $table, $description);
        } else {
            return str_replace("{{SPECIFICATIONS}}", "", $description);
        }
    }
}
