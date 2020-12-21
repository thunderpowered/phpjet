<?php

namespace CloudStore\App\MVC\Client\Widgets;

use CloudStore\App\Engine\ActiveRecord\Tables\Cart;
use CloudStore\App\Engine\Core\Widget;
use CloudStore\CloudStore;

/**
 * Class WidgetCart
 * @package CloudStore\App\MVC\Client\Widgets
 */
class WidgetCart extends Widget
{
    /**
     * @var int
     */
    private $numberOfProducts;
    /**
     * @var string
     */
    private $fullCartValue;
    /**
     * @var string
     */
    private $userCartCookieToken;
    /**
     * @var string
     */
    private $userCartCookieTokenName = 'cartToken';

    /**
     * WidgetCart constructor.
     */
    public function __construct(Widget $widget)
    {
        parent::__construct($widget);

        // by cookie
        $this->userCartCookieToken = CloudStore::$app->system->request->getCookie($this->userCartCookieTokenName);
        if (!$this->userCartCookieToken) {
            // generate new
            $this->userCartCookieToken = hash("sha256", uniqid('', true));
            CloudStore::$app->system->request->setCookie($this->userCartCookieTokenName, $this->userCartCookieToken);
        }

        $cartProducts = Cart::get(["token" => $this->userCartCookieToken]);
        $this->calculateCartProducts($cartProducts);
    }

    /**
     * @return int
     */
    public function getNumberOfProducts(): int
    {
        return $this->numberOfProducts;
    }

    /**
     * @return string|void
     */
    public function getFullCartValue(bool $formatString = true)
    {
        if ($formatString) {
            return CloudStore::$app->tool->utils->asPrice($this->fullCartValue);
        }
        return $this->fullCartValue;
    }

    /**
     * @param Cart[] $cartProducts
     */
    private function calculateCartProducts(array $cartProducts): void
    {
        $this->numberOfProducts = 0;
        $this->fullCartValue = 0;

        // probably i can just do in with simple SQL-query...
        foreach ($cartProducts as $product) {
            $this->numberOfProducts += $product->amount;
            $this->fullCartValue += $product->price * $product->amount;
        }
    }
}
