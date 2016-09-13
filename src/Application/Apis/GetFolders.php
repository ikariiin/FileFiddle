<?php declare(strict_types = 1);
/**
 * Created by PhpStorm.
 * User: lelouch
 * Date: 11/9/16
 * Time: 8:30 PM
 */

namespace FileFiddle\Application\Apis;
use Aerys\ParsedBody;
use Aerys\Request;
use Aerys\Response;
use FileFiddle\Application\FolderViewer\Folders;
use FileFiddle\Exceptions\FieldsNotFilled;

class GetFolders implements Api {
    private $body;
    private $query;

    public function __construct(ParsedBody $body, array $query) {
        $this->body = $body;
        $this->query = $query;
    }

    public function getRawData() {
        return json_encode($this->getStructuredData(), JSON_PRETTY_PRINT);
    }

    public function getStructuredData() {
        $dir = $this->body->get("dir");
        if($dir === null) {
            throw new FieldsNotFilled("The field 'dir' was a required field, but not filled.");
        }

        $folders = new Folders($dir);

        return $folders->getJsonReadyList();
    }
}