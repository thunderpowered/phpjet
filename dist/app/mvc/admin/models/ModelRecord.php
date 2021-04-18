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
     * @var string
     */
    private $databasePath = APP . 'database/';
    /**
     * @var string[]
     */
    private $remove = [
        '.', '..'
    ];

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
                $records = $this->parseTables($tableKey);
                return new ModelResponse(!!$records, '', ['records' => $records]);
            default:
                return new ModelResponse(false, 'unsupported mode');
        }
    }

    /**
     * @param string $filterBy
     * @param bool $includeStructure
     * @param bool $includeData
     * @return array
     */
    public function parseTables(string $filterBy = '', bool $includeStructure = false, bool $includeData = false): array
    {
        // temp solution, i have an idea, but have no time
        $tables = scandir($this->databasePath);
        $tables = array_diff($tables, array('.', '..'));
        array_walk($tables, function (&$element) {
            $element = substr($element, 0, strpos($element, '.'));
        });

        // filtering
        if ($filterBy) {
            $tables = array_filter($tables, function ($element) use ($filterBy) {
                return $element === $filterBy;
            });
        }

        if ($includeStructure) {
            // do something good
        }

        if ($includeData) {
            // do something even more good
        }

        return array_values($tables);
    }
}