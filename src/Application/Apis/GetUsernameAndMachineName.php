<?php declare(strict_types = 1);
/**
 * Created by PhpStorm.
 * User: lelouch
 * Date: 13/9/16
 * Time: 11:27 PM
 */

namespace FileFiddle\Application\Apis;
use Aerys\ParsedBody;

class GetUsernameAndMachineName implements Api {
    public function __construct(ParsedBody $body, array $query) {
    }

    /**
     * @return string
     */
    public function getRawData() {
        return json_encode($this->getStructuredData(), JSON_PRETTY_PRINT);
    }

    /**
     * @return string|array|object
     */
    public function getStructuredData() {
        $machineName = gethostname();
        $user = exec("whoami");

        return [
            "machineName" => $machineName,
            "username" => $user
        ];
    }
}