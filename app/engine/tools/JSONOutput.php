<?php


namespace CloudStore\App\Engine\Tools;

/**
 * Class JSONOutput
 * @package CloudStore\App\Engine\Tools
 * Best usage: create new class
 */
class JSONOutput
{
    /**
     * @var array
     */
    private $messageBoxStyles = [
        'Warning',
        'Success'
    ];
    /**
     * @var array
     */
    private $JSONOutput = [
        // some default values
        'status' => false,
        'messageBox' => [
            'style' => 'Warning',
            'text' => 'Output is not specified'
        ],
        'data' => []
    ];

    public function setStatusFalse()
    {
        $this->JSONOutput['status'] = false;
        $this->setMessageBoxStyle(0);
    }

    public function setStatusTrue()
    {
        $this->JSONOutput['status'] = true;
        $this->setMessageBoxStyle(1);
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
        return json_encode($this->JSONOutput);
    }
}