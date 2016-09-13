<?php declare(strict_types = 1);
/**
 * Created by PhpStorm.
 * User: lelouch
 * Date: 12/9/16
 * Time: 4:47 PM
 */

namespace FileFiddle\Application\Apis;
use Aerys\ParsedBody;
use FileFiddle\Application\FilesViewer\Files;
use FileFiddle\Exceptions\FieldsNotFilled;

class GetFileDetails implements Api {
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
        if($fileName === null) {
            throw new FieldsNotFilled("The fields required to be filled, are not filled.");
        }

        $file = new Files($fileName);
        return $file->getJsonReadyData();
    }
}