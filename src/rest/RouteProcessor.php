<?php
/*
 * Copyright (c) 2020 Tomás Gray
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

namespace Capstone;


class RouteProcessor
{
    // Assoc. array of routes
    private $routes;

    public function __construct() {
        // Get an array of routes, ensuring no
        // empties
        $this->routes = self::clean_routes(self::parseRoutes());

        Log::i("Routes: " . implode(' => ', $this->routes));
    }


    public function getRequestType() {
        return $this->getRoute(0);
    }
    public function getEndPoint() {
        $index = count($this->routes) -1;
        return $this->routes[$index];
    }
    public function getRoute($i) {
        return (isset($this->routes[$i])) ? $this->routes[$i] : null;
    }
    public function hasSubRoute() {
        return isset($this->routes[1]);
    }

    public function getSubRouteCount() {
        // Get the number of routes, minus
        // the base route
        return count($this->routes) -1;
    }

    private static function parseRoutes()
    {
        $url = parse_url(
            $_SERVER['REQUEST_URI'],
            PHP_URL_PATH
        );

        $routes = explode('/', $url);

        // TODO: Fix this!!!!
       return self::subDirify($routes);
    }

    private static function clean_routes($routes)
    {
        // Ensure no empty routes
        foreach($routes as $key => $route) {
            if( trim($route) == '' )
                unset( $routes[$key] );
        }

        return array_values($routes);
    }


    // For subdir installation
    private static function subDirify($routes) {
        unset($routes[1], $routes[2]);
        return $routes;
    }
}
