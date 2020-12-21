<?php

namespace CloudStore\App\MVC\Client\Models;

use CloudStore\App\Engine\ActiveRecord\Tables\Products;
use CloudStore\App\Engine\Config\Config;
use CloudStore\App\Engine\Core\Model;
use CloudStore\CloudStore;

/**
 * Class ModelProducts
 * @package CloudStore\App\MVC\Client\Models
 */
class ModelProducts extends Model
{
    /**
     * @var array
     */
    private $sortingRules = [
        'price-asc' => [
            'db_order_by' => ['price' => 'ASC'],
            'display_name' => 'Price: Ascending'
        ],
        'price-desc' => [
            'db_order_by' => ['price' => 'DESC'],
            'display_name' => 'Price: Descending'
        ],
        'popular' => [
            'db_order_by' => ['views' => 'DESC'],
            'display_name' => 'Most Popular'
        ],
        '__default' => [
            'db_order_by' => ['price' => 'DESC'],
            'display_name' => 'Price: Descending'
        ]
    ];
    /**
     * @var int
     */
    private $amount;

    /**
     * ModelProducts constructor.
     * @param string $name
     */
    public function __construct(string $name = "")
    {
        parent::__construct($name);
    }

    /**
     * @return int
     */
    public function getAmountOfLastSearch()
    {
        return $this->amount;
    }

    /**
     * @return array
     */
    public function getSortingRules(): array
    {
        return $this->sortingRules;
    }

    /**
     * @param Products $products
     * @return bool
     */
    public function updateViews(Products $products): bool
    {
        $products->views++;
        return $products->save();
    }

    /**
     * @param $data
     * @return bool
     * @deprecated
     */
    public function updateCount($data)
    {
        return false;
        if (Request::getSession("viewed_{$data['handle']}")) {
            return false;
        }

        $new_viewed = ++$data['viewed'];

        if (!S::update("products", ["viewed" => $new_viewed], ["handle" => $data['handle']])) {
            return false;
        }

        Request::setSession("viewed_{$data['handle']}", true);
        return true;
    }

    public function handler()
    {
        $handle = CloudStore::$app->router->getAction(false, false);
        // temp
        $getParam = strpos($handle, "?");
        if ($getParam) {
            $handle = substr($handle, 0, strlen($handle) - strlen(substr($handle, $getParam)));
        }

        //Long and hard process...
        $array = CloudStore::$app->store->execGet("SELECT * FROM products WHERE handle LIKE ? AND store = ? AND avail <> 0", ['%' . $handle, Config::$config["site_id"]]);

        if (!$array OR count($array) > 1) {

            return $this->redirect();
        }

        $handle = CloudStore::$app->tool->utils->makeHandler($handle, true);

        foreach ($array as $product) {

            $product_handle = CloudStore::$app->tool->utils->makeHandler($product['handle'], true);

            if ($handle === $product_handle) {

                $new_handle = $product['id'] . '-' . $product_handle;
                CloudStore::$app->tool->utils->regularRedirect("products", $new_handle);
            }
        }

        return $this->redirect();
    }

    /**
     * Just redirects to search page
     */
    public function redirect()
    {
        $handle = CloudStore::$app->router->getAction();
        $old_page = CloudStore::$app->router->getHost() . '/product/' . $handle;
        $handle = str_replace("-", "+", $handle);
        $new_page = CloudStore::$app->router->getHost() . '/search/?q=' . $handle;
        CloudStore::$app->store->collect("redirect", ["redirect_from" => $old_page, "redirect_to" => $new_page]);
        CloudStore::$app->tool->utils->regularRedirect("search", "?q=" . $handle);
    }

    /**
     * @param array $conditions
     * @param int $limit
     * @param bool $mainPage
     * @return array
     */
    public function load(array $conditions = array(), int $limit = 20, bool $mainPage = false): array
    {
        $conditions['trash'] = '!1';
        $this->amount = Products::count($conditions);
        if (!$this->amount ) {
            return [];
        }

        $pagination = CloudStore::$app->tool->paginator->preparePagination($this->amount , $limit, $this->sortingRules);

        // Sorting by amount in stock
        $orderBy = [
            // currently available (you can buy it now)
            "quantity_available" => "DESC",
            // available somewhere at the supplier (you can also buy it now, but you need to wait sometime before arrival)
            "quantity_stock" => "DESC",
            // unavailable at the moment, but you if need it, the store owner can order them for you
            "quantity_possible" => "DESC"
        ];

        // this is because admin can select products that will be shown on the main page
        if ($mainPage) {
            $orderBy = array_merge(["visible_main" => "DESC"], $orderBy);
        } else {
            $orderBy = array_merge($pagination["db_order_by"], $orderBy);
        }

        // using ActiveRecord
        $products = Products::getJoin([[
                'LEFT', 'products_likes', ['products_id' => 'id']
        ]], $conditions, $orderBy, $pagination['limit']);

        try {
            return $this->proceedProducts($products);
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * @param Products[] $products
     * @return array
     * @throws \Exception
     * @deprecated until not fixed (just look at all tasks)
     */
    public function proceedProducts(array $products = []): array
    {
        foreach ($products as $key => $product) {
            // these two functions
            $product->thumbnail = CloudStore::$app->tool->utils->getThumbnail($product->picture, null, null);

            // proceed old price, if it set of course
            if ($product->price_old) {
                $product->price_old = CloudStore::$app->tool->utils->asPrice($product->price_old);
            }
            // ... and actual price
            $product->price = CloudStore::$app->tool->utils->asPrice($product->price);

            if ($product->delivery_available > 5000) {
                $product->delivery_available = 5000;
            }

            // get availability status
            $product->status = $this->getStatus($product->quantity_available, $product->quantity_stock, $product->delivery_available, $product->quantity_possible, $product->delivery_possible, $product->delivery_stock, $product->id);

            // calculate shipping information
            $product->status['delivery_difference'] = $this->getStatusDifference($product->delivery_available);

            // prepare description
            $controllerName = CloudStore::$app->router->getControllerName();
            if ($controllerName === 'catalog' || $controllerName === 'products') {
                $product->description = $this->prepareDescription($product->description, $product->id);
            }

            // tn: products_reviews
            $product->rating = (int) $product->rating;
            // tn: products_likes
            $product->like = (bool) $product->like;

            // preparing SKU (Stock Keeping Unit)
            $product->sku = "Арт. " . ($product->sku ? htmlentities($product->sku) : $product->sku);
            $products[$key] = $product;
        }

        return $products;
    }

    /**
     * @param string $description
     * @param int $id
     * @return string
     * @deprecated
     * @todo this function should be completely redone
     */
    private function prepareDescription(string $description, int $id): string
    {
        return $description;
        $specifications = CloudStore::$app->store->load("sef_filters", ["id" => $id], ["filter_order" => "ASC"], [], false);

        // some weird way to sort specifications by
        // @todo make it with SQL, it's pretty easy
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

        // prepare specifications HTML
        // todo: maybe there are some ways to make it without putting html into PHP? Widgets, parts or something?
        // at least, in view class function getHTMLOutput() exists
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

        // put specifications in place
        if ($specifications) {
            return str_replace("{{SPECIFICATIONS}}", $table, $description);
        } else {
            // just cut it out
            return str_replace("{{SPECIFICATIONS}}", "", $description);
        }
    }

    /**
     * @param string $deliveryAvailable
     * @return string
     * @throws \Exception
     * @deprecated
     */
    private function getStatusDifference(string $deliveryAvailable): string
    {
        return $deliveryAvailable;
        $date_1 = new \DateTime("now");
        $products_shipping_date = date("Y-m-j", time() + $deliveryAvailable * (86400));
        $date_2 = \DateTime::createFromFormat('Y-m-j', $products_shipping_date);
        $diff = $date_2->diff($date_1);
        return $diff->format("%d");
    }

    /**
     * @param int $inStock
     * @param int $quantity
     * @param string $date
     * @param int $possibleQuantity
     * @param string $possibleDate
     * @param string $expectation
     * @param int $id
     * @return array
     * This function is too old, so it should be redone someday
     */
    private function getStatus(int $inStock, int $quantity, string $date, int $possibleQuantity, string $possibleDate, string $expectation, int $id): array
    {
        $shipping_date = null;
        $shipping_avail = null;

        $date = intval($date);
        $date = date("Y-m-j", time() + $date * (86400));

        $inStock = $this->getAmount($inStock, $quantity, $expectation, $date, $id);

        // @todo remove
        if ($possibleDate === "0000-00-00") {
            $possibleDate = null;
        }

        if ($inStock > 0) {
            $shipping_date = "Today";
            $shipping_avail = true;
        } elseif ($inStock <= 0 AND $inStock > (0 - $quantity)) {
            $shipping_date = CloudStore::$app->tool->utils->getLocalTime("j F Y", strtotime($date)) . ' г.';

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
        } elseif ($inStock <= 0 AND $inStock < (0 - $quantity) AND $possibleDate AND ($inStock > (0 - ($quantity + $possibleQuantity)))) {
            $shipping_date = CloudStore::$app->tool->utils->getLocalTime("j F Y", strtotime($possibleDate)) . ' г.';

            // Get difference (without DateTime Object)
            $date_1 = \DateTime::createFromFormat('j-m-Y', date("j-m-Y"));
            $date_2 = \DateTime::createFromFormat('Y-m-j', $possibleDate);
            $diff = $date_2->diff($date_1);

            if ($diff AND $diff->format("%d") === "1") {
                $shipping_date = "завтра";
            }

            $shipping_avail = true;
        } elseif (+$inStock === 0 AND +$inStock === (0 - $quantity) AND +$possibleQuantity !== 0 AND $possibleDate) {
            // Get difference
            $date_1 = \DateTime::createFromFormat('j-m-Y', date("j-m-Y"));
            $date_2 = \DateTime::createFromFormat('Y-m-j', $possibleDate);
            $shipping_date = CloudStore::$app->tool->utils->getLocalTime("j F Y", $date_2->getTimestamp()) . ' г.';
            $diff = $date_2->diff($date_1);

            if ($diff AND $diff->format("%d") === "1") {
                $shipping_date = "завтра";
            }

            $shipping_avail = true;
        } else {
            $shipping_date = 'Not available';
            $shipping_avail = false;
        }

        return [
            'shipping_date' => $shipping_date,
            'shipping_avail' => $shipping_avail,
            'count' => $inStock
        ];
    }

    /**
     * @param int $inStock
     * @param int $quantity
     * @param string $expectation
     * @param string $date
     * @param int $id
     * @return int
     */
    private function getAmount(int $inStock, int $quantity, string $expectation, string $date, int $id): int
    {
        if ($expectation === '1') {
            if (strtotime($date) - time() <= 0) {
                $inStock = (intval($inStock) + intval($quantity));
                $this->updateAmount($inStock, $id);
                return $inStock;
            } else {
                return $inStock;
            }
        } else {
            return $inStock;
        }
    }

    /**
     * @param int $inStock
     * @param int $id
     * @return bool
     */
    private function updateAmount(int $inStock, int $id): bool
    {
        return false;
        return CloudStore::$app->store->update("products", ["quantity_available" => $inStock, "delivery_possible" => 0], ["id" => $id]);
    }

    public function getPagination()
    {
        $main = "/" . CloudStore::$app->router->getRoute()[1] . "/" . CloudStore::$app->router->getAction() . '?';
        CloudStore::$app->tool->utils->getPagination($main);
    }
}
