<?php declare(strict_types = 1);
/**
 * Created by PhpStorm.
 * User: lelouch
 * Date: 11/9/16
 * Time: 10:34 PM
 */

namespace FileFiddle\Authentication;
use Aerys\Request;
use FileFiddle\Application\Keys\CookieKeys;

class OnlyIfLoggedIn {
    private $request;

    public function __construct(Request $request) {
        $this->request = $request;
    }

    public function if() {
        $isLoggedIn = $this->request->getCookie(CookieKeys::IS_LOGGED_IN);
        if($isLoggedIn === null || (bool) $isLoggedIn === false) {
            $this->request->addHeader("Location", "/login.html");
            $this->request->setStatus(302);
            $this->request->end();
            return;
        }
    }
}