<?php


namespace Jet\App\Engine\Interfaces;

/**
 * Class JSONOutput
 * @package Jet\App\Engine\Interfaces
 */
class JSONOutput
{
    /**
     * @var int
     * @deprecated
     */
    public $status = HTTP_OK;
    /**
     * @var string
     */
    public $action = '';
    /**
     * @var array
     */
    public $data = [];
    /**
     * @var MessageBox
     */
    public $message;

    /**
     * JSONOutput constructor.
     */
    public function __construct()
    {
        $this->message = new MessageBox();
    }

    /**
     * @return string
     */
    public function returnJsonOutput(): string
    {
        return json_encode([
            'status' => $this->status,
            'action' => $this->action,
            'data' => $this->data,
            'message' => $this->message
        ]);
    }
}