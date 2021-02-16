<?php


namespace Jet\App\Engine\Interfaces;

/**
 * Class JSONOutput
 * @package Jet\App\Engine\Interfaces
 */
class JSONOutput
{
    /**
     * @var bool
     */
    public $status = false;
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
    public $messageBox;

    /**
     * JSONOutput constructor.
     */
    public function __construct()
    {
        $this->messageBox = new MessageBox();
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
            'message_box' => $this->messageBox
        ]);
    }
}