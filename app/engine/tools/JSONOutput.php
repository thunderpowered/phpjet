<?php


namespace Jet\App\Engine\Tools;

/**
 * Class JSONOutput
 * @package Jet\App\Engine\Tools
 * Best usage: create new class
 */
class JSONOutput
{
    /**
     * @var array
     */
    private $messageBoxStyles = [
        'success',
        'warning',
        'error',
        'danger',
        'info'
    ];
    /**
     * @var array
     */
    private $JSONOutput = [
        // some default values
        'status' => false,
        'messageBox' => [
            'style' => 'info',
            'text' => ''
        ],
        'action' => '',
        'data' => []
    ];

    public function setStatusTrue()
    {
        $this->JSONOutput['status'] = true;
        $this->setMessageBoxStyle(0);
    }

    public function setStatusFalse()
    {
        $this->JSONOutput['status'] = false;
        $this->setMessageBoxStyle(2);
    }

    /**
     * @param string $message
     */
    public function setMessageBoxText(string $message)
    {
        $this->JSONOutput['messageBox']['text'] = $message;
    }

    /**
     * @param int $style
     */
    public function setMessageBoxStyle(int $style)
    {
        $style = $this->messageBoxStyles[$style];
        $this->JSONOutput['messageBox']['style'] = $style;
    }

    /**
     * @param array $data
     */
    public function setData(array $data)
    {
        $this->JSONOutput['data'] = $data;
    }

    public function setAction(string $action)
    {
        $this->JSONOutput['action'] = $action;
    }

    /**
     * @param string $JSONOutput
     */
    public function dangerouslySetFullJSONOutput(string $JSONOutput)
    {
        $this->JSONOutput = $JSONOutput;
    }

    /**
     * @return string
     */
    public function returnJSONOutput(): string
    {
        header('Content-Type: application/json');
        return json_encode($this->JSONOutput);
    }
}