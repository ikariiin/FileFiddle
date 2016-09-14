<?php declare(strict_types = 1);
/**
 * Created by PhpStorm.
 * User: lelouch
 * Date: 13/9/16
 * Time: 11:09 PM
 */

namespace FileFiddle\Application\Apis;
use Aerys\ParsedBody;
use FileFiddle\Application\Terminal\Main;
use FileFiddle\Exceptions\FieldsNotFilled;

class ExecuteCommand implements Api {
    private $body;
    private $query;

    public function __construct(ParsedBody $body, array $query) {
        $this->body = $body;
        $this->query = $query;
    }

    /**
     * @return string
     */
    public function getRawData(): string {
        return json_encode($this->getStructuredData(), JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT);
    }

    /**
     * @return array|object|string
     * @throws FieldsNotFilled
     * @return string
     */
    public function getStructuredData(): array {
        $command = $this->body->get("command");

        if($command === null) {
            throw new FieldsNotFilled("The required fields were not filled.");
        }

        return [
            "output" => (new Main($command))
                ->executeCommand()
        ];
    }
}