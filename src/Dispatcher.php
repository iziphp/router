<?php

/**
 * Dispatcher::match() and Dispatcher::compilePath() methods are heavily
 * inspired by AltoRouter
 *
 * @see https://altorouter.com
 */

declare(strict_types=1);

namespace PhpStandard\Router;

use PhpStandard\Router\Exceptions\RouteNotFoundException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ServerRequestInterface;

/** @package PhpStandard\Router */
class Dispatcher
{
    /** @var array<string> Array of the match types  */
    protected array $matchTypes = [
        'i'  => '[0-9]++', // Integer
        'a'  => '[0-9A-Za-z]++', // Alphanumeric
        'h'  => '[0-9A-Fa-f]++', // Hexadecimal
        's'  => '[0-9A-Za-z\-]++', // url slug
        '*'  => '.+?', //
        '**' => '.++',
        ''   => '[^/\.]++'
    ];

    /**
     * @param RouteCollector $collector
     * @param ContainerInterface $container
     * @return void
     */
    public function __construct(
        private RouteCollector $collector,
        private ContainerInterface $container
    ) {
    }

    /**
     * @param ServerRequestInterface $request
     * @return null|Route
     * @throws RouteNotFoundException
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     */
    public function dispatch(ServerRequestInterface $request): ?Route
    {
        $route = $this->matchRoute(
            $request
        );

        $route->resolve($this->container);
        return $route;
    }

    /**
     * @param ServerRequestInterface $request
     * @return Route
     * @throws RouteNotFoundException
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    private function matchRoute(
        ServerRequestInterface $request
    ): Route {
        $uri = $request->getUri();
        $url = $uri->getPath();
        $method = $request->getMethod();

        $params = [];
        $routes = $this->collector->getRoutes();

        // Strip query string (?a=b) from Request Url
        $strpos = strpos($url, '?');
        if ($strpos !== false) {
            $url = substr($url, 0, $strpos);
        }

        // Last character of the request url
        $lastChar = $url ? $url[strlen($url) - 1] : '';

        foreach ($routes as $route) {
            $methods = explode("|", $route->getMethod());
            $path = $route->getPath();

            // Method did not match, continue to next route.
            if (!in_array($method, $methods)) {
                continue;
            }

            if ($path === '*') {
                // * wildcard (matches all)
                return $route;
            }

            if (isset($path[0]) && $path[0] === '@') {
                // @ regex delimiter
                $pattern = '`' . substr($path, 1) . '`u';

                if (preg_match($pattern, $url, $params) === 1) {
                    return $this->addParams($route, $params);
                }
            }

            $position = strpos($path, '[');
            if ($position === false && strcmp($url, $path) === 0) {
                // No params in url, do string comparison
                return $this->addParams($route, $params);
            }

            // Compare longest non-param string with url before moving on to
            // regex. Check if last character before param is a slash,
            // because it could be optional if param is optional too
            if (
                strncmp($url, $path, $position) !== 0
                && ($lastChar === '/' || $path[$position - 1] !== '/')
            ) {
                continue;
            }

            $regex = $this->compilePath($path);
            if (preg_match($regex, $url, $params) === 1) {
                return $this->addParams($route, $params);
            }
        }

        throw new RouteNotFoundException($request);
    }

    /**
     * @param Route $route
     * @param array $param
     * @return Route
     */
    private function addParams(Route $route, array $param): Route
    {
        $params = [];
        foreach ($param as $key => $value) {
            if (!is_numeric($key)) {
                $params[] = new Param($key, $value);
            }
        }

        return $route->withParam(...$params);
    }

    /**
     * Compile the regex for a given route path (EXPENSIVE)
     * @param string $path
     * @return string
     */
    protected function compilePath(string $path): string
    {
        $pattern = '`(/|\.|)\[([^:\]]*+)(?::([^:\]]*+))?\](\?|)`';
        if (preg_match_all($pattern, $path, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                list($block, $pre, $type, $param, $optional) = $match;

                if (isset($this->matchTypes[$type])) {
                    $type = $this->matchTypes[$type];
                }

                if ($pre === '.') {
                    $pre = '\.';
                }

                $optional = $optional !== '' ? '?' : null;

                //Older versions of PCRE require the 'P' in (?P<named>)
                $pattern = '(?:'
                    . ($pre !== '' ? $pre : null)
                    . '('
                    . ($param !== '' ? "?P<$param>" : null)
                    . $type
                    . ')'
                    . $optional
                    . ')'
                    . $optional;

                $path = str_replace($block, $pattern, $path);
            }
        }

        return "`^$path$`u";
    }
}
