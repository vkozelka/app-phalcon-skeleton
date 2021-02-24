<?php

namespace App\Core;

use App\System\Router\RouteNotFoundException;
use App\System\Router\RouteWithoutPathException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

final class Router extends \Phalcon\Mvc\Router
{

    protected string $section;

    private array $routesConfig;

    public function __construct()
    {
        $this->routesConfig = $this->getRouterConfig();
        $this->prepareRouter();
    }

    public function match(): void
    {
        App::get()->profiler->start("App::Routing");
        $cleanPath = str_replace(App::get()->url->getBasePath(),"",App::get()->request->getServer("REQUEST_URI"));
        $this->handle($cleanPath);

        if (isset($this->params["section"])) {
            $this->setSection($this->params["section"]);
            unset($this->params["section"]);
        }

        App::get()->profiler->stop("App::Routing");
    }

    private function getRouterConfig(): array
    {
        return App::get()->config->getConfigValues("routes");
    }

    private function prepareRouter(): void
    {
        App::get()->profiler->start("App::Routing::Init");
        foreach ($this->routesConfig as $routeName => $routeDefinition) {
            if ($routeName === 'notFound') {
                $this->notFound($routeDefinition['paths']);
            } else {
                $route = $routeDefinition["route"] ?: null;
                if (null === $route) {
                    throw new RouteWithoutPathException();
                }
                $paths = isset($routeDefinition["paths"]) ? $routeDefinition["paths"] : [];

                $this->add($route, $paths);
            }
        }
        App::get()->profiler->stop("App::Routing::Init");
    }

    public function getSection(): string
    {
        return $this->section;
    }

    public function setSection(string $section): void
    {
        $this->section = $section;
    }

}