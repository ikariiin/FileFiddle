<?php declare(strict_types = 1);
/**
 * Created by PhpStorm.
 * User: lelouch
 * Date: 13/9/16
 * Time: 6:32 PM
 */

namespace FileFiddle\Application\Apis;
use Aerys\ParsedBody;
use FileFiddle\Exceptions\FieldsNotFilled;

class SaveFile implements Api {
    private $body;
    private $query;

    public function __construct(ParsedBody $body, array $query) {
        $this->body = $body;
        $this->query = $query;
    }

    /**
     * @return string
     */
    public function getRawData() {
        return json_encode($this->getStructuredData(), JSON_PRETTY_PRINT);
    }

    /**
     * @return array|object|string
     * @throws FieldsNotFilled
     */
    public function getStructuredData() {
        $fileName = $this->body->get("fileName");
        $content = $this->body->get("content");

        if($fileName === null || $content === null) {
            throw new FieldsNotFilled("All the required fields were not filled.");
        }

        return ["status" => ((bool) file_put_contents($fileName, $content))];
    }
}