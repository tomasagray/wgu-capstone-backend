<?php
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