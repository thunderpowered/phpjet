<?php


namespace Jet\App\MVC\Admin\Models;

use Jet\App\Engine\Interfaces\ModelResponse;
use Jet\PHPJet;

/**
 * Class ModelRecord
 * @package Jet\App\MVC\Admin\Models
 */
class ModelRecord
{
    /**
     * @param string $id
     * @param string $mode
     * @return ModelResponse
     */
    public function getRecord(string $id, string $mode = 'only_title'): ModelResponse
    {
        $tableKey = $id === '*' ? '' : $id;
        switch ($mode) {
            case 'only_title':
                $records = PHPJet::$app->tool->configurator->_parseTables($tableKey, false, false, true);
                return new ModelResponse(!!$records, '', ['records' => $records]);
            default:
                return new ModelResponse(false, 'unsupported mode');
        }
    }
}