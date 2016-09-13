<?php declare(strict_types = 1);
/**
 * Created by PhpStorm.
 * User: lelouch
 * Date: 11/9/16
 * Time: 1:30 PM
 */

spl_autoload_register(function(string $className) {
    $className = str_replace("FileFiddle", "", $className);
    $className = str_replace("\\", "/", $className);

    require_once __DIR__ . "$className.php";
});

$router = Aerys\router()
    ->route("POST", "/log/in", function (\Aerys\Request $request, \Aerys\Response $response) {
        (new FileFiddle\Authentication\Login($request, $response))
            ->login((yield Aerys\parseBody($request)));

        $response->addHeader("Location", "/");
        $response->setStatus(302);
        $response->end();
        return;
    })->route("GET", "/", function (\Aerys\Request $request, \Aerys\Response $response) {
        (new \FileFiddle\Authentication\OnlyIfLoggedIn($request, $response))
            ->if();

        $header = \FileFiddle\Application\TemplateLoader::loadHeaders("Main Page");
        $footer = \FileFiddle\Application\TemplateLoader::loadFooters();
        $page = file_get_contents(\FileFiddle\Application\Keys\Keys::INDEX_PAGE);

        $response->end($header . $page . $footer);
    });

$router->route("POST", "/getFolders", function (\Aerys\Request $request, \Aerys\Response $response) {
    (new \FileFiddle\Authentication\OnlyIfLoggedIn($request, $response))
        ->if();

    $response->setHeader("Content-Type", "application/json");
    try {
        $response->end((new \FileFiddle\Application\Apis\GetFolders((yield \Aerys\parseBody($request)), $request->getAllParams()))
            ->getRawData());
        return;
    } catch (\Exception $ex) {
        $response->end(json_encode([
            "status" => "failure",
            "message" => $ex->getMessage()
        ]));
        return;
    }
})->route("GET", "/getDefaultDir", function (\Aerys\Request $request, \Aerys\Response $response) {
    $response->setHeader("Content-Type", "application/json");

    $response->end((new \FileFiddle\Application\Apis\GetDefaultDir((yield \Aerys\parseBody($request)), $request->getAllParams()))
        ->getRawData());
})->route("POST", "/getFileDetails", function (\Aerys\Request $request, \Aerys\Response $response) {
    (new \FileFiddle\Authentication\OnlyIfLoggedIn($request, $response))
        ->if();

    $response->setHeader("Content-Type", "application/json");

    try {
        $response->end((new \FileFiddle\Application\Apis\GetFileDetails((yield \Aerys\parseBody($request)), $request->getAllParams()))
            ->getRawData());
        return;
    } catch (\Exception $ex) {
        $response->end(json_encode([
            "status" => "failure",
            "message" => $ex->getMessage()
        ]));
        return;
    }
})->route("POST", "/saveFile", function (\Aerys\Request $request, \Aerys\Response $response) {
    (new \FileFiddle\Authentication\OnlyIfLoggedIn($request, $response))
        ->if();

    $response->setHeader("Content-Type", "application/json");

    try {
        $response->end((new \FileFiddle\Application\Apis\SaveFile((yield \Aerys\parseBody($request)), $request->getAllParams()))
            ->getRawData());
        return;
    } catch (\Exception $ex) {
        $response->end(json_encode([
            "status" => false,
            "message" => $ex->getMessage()
        ]));
        return;
    }
});

(new Aerys\Host)
    ->expose("*", 1337)
    ->use(Aerys\root(__DIR__ . "/../public"))
    ->use($router);