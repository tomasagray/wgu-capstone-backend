<?php
namespace Capstone;

require_once "data/access/AddressDao.php";
require_once "rest/Response.php";
require_once "rest/RouteProcessor.php";


// TODO: Delete this class?
class AddressRequestProcessor
{
    private $method;
    private $routes;
    private $address_dao;
    private $response;

    public function __construct($request_method, RouteProcessor $routes)
    {
        $this->method = $request_method;
        $this->routes = $routes;
        $this->address_dao = new AddressDao();
        $this->response = new Response();
    }

    public function getResponse() {
        return $this->response;
    }

    public function processRoutes()
    {
        if($this->routes->hasSubRoute()) {
            // Get specific address
            $address_id = $this->routes->getRoute(1);
            Log::i("Getting address: " . $address_id);
            $this->getAddress($address_id);

        } else {
            Log::i("Getting all address data");
            $this->getAllAddresses();
        }
    }

    private function getAddress($address_id)
    {
        $address = $this->address_dao->load($address_id);

        if($address != null) {
            Log::i("Successfully loaded data for address: ". $address_id);
            $this->response->setStatusCode(Response::HTTP_200);
            $this->response->setBody( $address );
        } else {
            Log::e("Error loading data for address: ". $address_id);
            $this->response->setStatusCode(Response::HTTP_404);
        }
    }

    private function getAllAddresses()
    {
        $addresses = $this->address_dao->loadAll();

        if($addresses != null) {
            Log::i("Successfully loaded all address data");
            $this->response->setStatusCode(Response::HTTP_200);
            $this->response->setBody( $addresses );
        } else {
            Log::e("Error loading all address data from DB");
            $this->response->setStatusCode(Response::HTTP_404);
        }
    }
}