<?php

namespace CloudStore\App\Engine\Controllers;

use CloudStore\App\Engine\Core\Controller;
use CloudStore\App\MVC\Client\Models\ModelCart;
use CloudStore\CloudStore;

/**
 * Class ControllerCart
 * @package CloudStore\App\Engine\Controllers
 */
class ControllerCart extends Controller
{
    protected $title = "Корзина";
    /**
     * ControllerCart constructor.
     * @param string $name
     */
    public function __construct(string $name = "")
    {
        parent::__construct($name);
        $this->model = new ModelCart();
    }

    /**
     * @return string
     */
    public function actionBasic()
    {
        $postData = CloudStore::$app->system->request->getPOST('checkout');
        if ($postData) {
            $ip = CloudStore::$app->system->request->getUserIP();
            $result = $this->model->prepareCheckout($ip);
            // redirect to checkout
            if ($result) {
                $token = CloudStore::$app->system->token->generateCheckoutToken();
                $urlString = '/checkout/step1?token=' . $token;
                CloudStore::$app->router->redirect($urlString);
            } else {
                CloudStore::$app->router->errorPage500();
            }
        }

        $products = $this->model->getCart();
        $sum = $this->model->getSum($products);

        return $this->view->render($this->view->getTemplateName(), [
            'cart' => $products,
            'sum' => $sum
        ]);
    }

    /**
     * @return string
     */
    public function actionAJAXAdd()
    {
        $postData = CloudStore::$app->system->request->getPOST();
        if (!$postData) {
            return $this->view->returnJsonOutput(
                false,
                ['why' => 'no data']
            );
        }

        // @todo something
        // there is kinda logic
        // all posts MUST include csrf token, we did it in Request class
        // so we just send request to /controller/AJAXMethod/
        // that's all, actually there's no need to type AJAX in action name, but i just think it's just better

        return '';
    }
}
