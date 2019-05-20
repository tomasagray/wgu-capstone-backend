<?php
namespace Capstone;


class Response
{
    // HTTP response headers
    const HTTP_200 = "HTTP/1.1 200 OK";
    const HTTP_404 = "HTTP/1.1 404 Not Found";


    private $status_code;
    private $body;

    public function setStatusCode($code) {
        $this->status_code = $code;
    }
    public function setBody($body) {
        $this->body = $body;
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