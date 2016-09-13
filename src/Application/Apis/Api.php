<?php declare(strict_types = 1);
/**
 * Created by PhpStorm.
 * User: lelouch
 * Date: 11/9/16
 * Time: 8:36 PM
 */

namespace FileFiddle\Application\Apis;
use Aerys\ParsedBody;
use Aerys\Request;
use Aerys\Response;

interface Api {
    public function __construct(ParsedBody $body, array $query);

    /**
     * @return string
     */
    public function getRawData();

    /**
     * @return string|array|object
     */
    public function getStructuredData();
}