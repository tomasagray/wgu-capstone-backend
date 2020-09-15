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


class Response
{
    // HTTP response headers
    const HTTP_200 = "HTTP/1.1 200 OK";
    const HTTP_400 = "HTTP/1.1 400 Bad Request";
    const HTTP_403 = "HTTP/1.1 403 Forbidden";
    const HTTP_404 = "HTTP/1.1 404 Not Found";
    const HTTP_409 = "HTTP/1.1 409 Conflict";
    const HTTP_413 = "HTTP/1.1 413 Request Entity Too Large";
    const HTTP_500 = "HTTP/1.1 500 Internal Server Error";


    private $status_code;
    private $body;

    public function __construct() {
        // Default value
        $this->status_code = self::HTTP_400;
    }

    public function setStatusCode($code) {
        $this->status_code = $code;
    }

    /**
     * Encodes teh response body as a JSON string.
     *
     * @param $body * body
     */
    public function setBody($body) {
        $this->body = json_encode($body);
    }

    public function getStatusCode() {
        return $this->status_code;
    }
    public function getBody() {
        return $this->body;
    }

    public function hasBody() {
        return
            ($this->body != null) ? true : false;
    }
}
