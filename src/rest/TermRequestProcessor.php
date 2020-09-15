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

require_once "data/access/TermDao.php";
require_once "rest/Response.php";
require_once "rest/LoginRequestProcessor.php";

class TermRequestProcessor
{
    private $method;
    private $routes;
    private $term_dao;
    private $response;
    private $login;

    public function __construct($request_method, RouteProcessor $routes)
    {
        $this->method = $request_method;
        $this->routes = $routes;
        $this->term_dao = new TermDao();
        $this->response = new Response();
        $this->login = new LoginRequestProcessor();
    }

    public function processRoutes()
    {
        if($this->login->isAuthorized()) {
            Log::s("User is authorized");
            Log::s("Headers: " . json_encode(LoginRequestProcessor::getAuthHeaders()));
        } else {
            Log::s("User not authorized");
            $this->response->setStatusCode(Response::HTTP_403);
            return;
        }

        if( $this->routes->hasSubRoute() ) {
            // Get a specific term
            $term_id = $this->routes->getRoute(1);
            Log::i("Fetching data for term: " . $term_id);
            $this->getTerm($term_id);
        } else {
            // Get all terms
            Log::i("Fetching all terms");
            $this->getAllTerms();
        }
    }

    public function getResponse() {
        return $this->response;
    }

    private function getTerm($term_id)
    {
        $term = $this->term_dao->load($term_id);

        if($term != null) {
//            Log::i("Successfully retrieved term: " . $term_id);
            $this->response->setStatusCode(Response::HTTP_200);
            $this->response->setBody( $term );
        } else {
            Log::e("Failed to retrieve data for term: " . $term_id);
            $this->response->setStatusCode(Response::HTTP_404);
        }
    }

    private function getAllTerms()
    {
        $headers = LoginRequestProcessor::getAuthHeaders();
        $terms = $this->term_dao->loadStudentTerms($headers['user_id']);

        if($terms != null) {
            Log::i("Successfully retrieved all term data");
            $this->response->setStatusCode(Response::HTTP_200);
            $this->response->setBody( $terms );
        } else {
            Log::e("Error retrieving data for all terms");
            $this->response->setStatusCode(Response::HTTP_404);
        }
    }
}
