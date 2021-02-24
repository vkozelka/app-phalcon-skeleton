<?php
namespace App\Core\Mvc;

use App\Core\App;
use Phalcon\Http\Response;
use Phalcon\Http\ResponseInterface;
use Phalcon\Version;

class Controller extends \Phalcon\Mvc\Controller {

    const FLASH_SUCCESS = "success";
    const FLASH_INFO = "info";
    const FLASH_ERROR = "danger";

    protected function apiResponse($success = true, $data = []): Response {
        $data["system"] = [
            "environment" => CMS_ENV,
            "phalcon" => Version::get(),
            "profiler" => App::get()->outputProfiler(true),
            "includedFiles" => get_included_files(),
            "memoryUsage" => [
                "peak" => memory_get_peak_usage(),
                "usage" => memory_get_usage()
            ]
        ];
        $data["success"] = $success;
        $code = 200;
        if (isset($data["code"])) {
            $code = $data["code"];
        }

        $this->response->setJsonContent($data)->setStatusCode($code)->setHeader("content-type", "application/json");
        return $this->response;
    }

    protected function exitApplication() : void {
        $this->response->sendHeaders();
        $this->response->send();
        exit;
    }

    protected function getRouteParam($key, $default = null) : ?string
    {
        $routeParams = $this->dispatcher->getParams();
        return isset($routeParams[$key]) ? $routeParams[$key] : $default;
    }

    protected function redirect(string $routeName, array $routeParams = []): ResponseInterface {
        $params = array_merge($routeParams, ['for' => $routeName]);
        return $this->response->redirect($this->url->get($params));
    }

    protected function flash(string $message, string $type = "success"): void {
        $this->flash->message($type, $message);
    }

}