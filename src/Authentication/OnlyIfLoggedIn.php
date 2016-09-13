<?php declare(strict_types = 1);
/**
 * Created by PhpStorm.
 * User: lelouch
 * Date: 11/9/16
 * Time: 10:34 PM
 */

namespace FileFiddle\Authentication;
use Aerys\Request;
use Aerys\Response;
use FileFiddle\Application\Keys\CookieKeys;

class OnlyIfLoggedIn {
    private $request;

    public function __construct(Request $request, Response $response) {
        $this->request = $request;
        $this->response = $response;
    }

    public function if() {
        $isLoggedIn = $this->request->getCookie(CookieKeys::IS_LOGGED_IN);
        if($isLoggedIn === null || (bool) $isLoggedIn === false) {
            $this->response->addHeader("Location", "/login.html");
            $this->response->setStatus(302);
            $this->response->end();
            return;
        }
    }
}