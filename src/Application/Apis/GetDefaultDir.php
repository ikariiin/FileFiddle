<?php declare(strict_types = 1);
/**
 * Created by PhpStorm.
 * User: lelouch
 * Date: 11/9/16
 * Time: 10:25 PM
 */

namespace FileFiddle\Application\Apis;
use Aerys\ParsedBody;
use FileFiddle\Application\Keys\StorageKeys;

class GetDefaultDir implements Api {
    private $body;
    private $query;
    private $conf;

    public function __construct(ParsedBody $body, array $query) {
        $this->query = $query;
        $this->body = $body;

        $this->conf = json_decode(file_get_contents(StorageKeys::CONF_FILE), true);
    }

    /**
     * @return string
     */
    public function getRawData() {
        return json_encode($this->conf, JSON_PRETTY_PRINT);
    }

    /**
     * @return string|array|object
     */
    public function getStructuredData() {
        return $this->conf["defaultDir"];
    }
}