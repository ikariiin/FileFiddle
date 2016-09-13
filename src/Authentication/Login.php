<?php declare(strict_types = 1);
/**
 * Created by PhpStorm.
 * User: lelouch
 * Date: 11/9/16
 * Time: 2:00 PM
 */

namespace FileFiddle\Authentication;
use Aerys\ParsedBody;
use Aerys\Request;
use Aerys\Response;
use Aerys\Session;
use FileFiddle\Application\Keys\StorageKeys;
use FileFiddle\Exceptions\FieldsNotFilled;
use FileFiddle\Application\Keys\CookieKeys;
use FileFiddle\Exceptions\UserNameDoesNotExist;
use FileFiddle\Exceptions\UserNamePassWordMisMatch;

class Login {
    private $request;
    private $response;

    public function __construct(Request $request, Response $response) {
        $this->request = $request;
        $this->response = $response;
    }

    /**
     * @param ParsedBody $body
     * @return Response
     * @throws FieldsNotFilled
     * @throws UserNameDoesNotExist
     * @throws UserNamePassWordMisMatch
     */
    public function login(ParsedBody $body) {
        $username = $body->get("username");
        $password = $body->get("password");

        if ($username === null || $password === null) {
            throw new FieldsNotFilled("The required fields were not submitted.");
        }

        $usernamePasswordKV = json_decode(file_get_contents(StorageKeys::USERNAME_PASSWORD_FILE), true);
        $hash = $usernamePasswordKV[$username];

        if($hash === null) {
            throw new UserNameDoesNotExist("The Username Provided Does Not Exist.");
        }

        if(!password_verify($password, $hash)) {
            throw new UserNamePassWordMisMatch("The username and the password combination do not match.");
        }

        $this->response->setCookie(CookieKeys::USERNAME, $username, ["path" => "/"]);
        $this->response->setCookie(CookieKeys::IS_LOGGED_IN, "true", ["path" => "/"]);

        return $this->response;
    }
}