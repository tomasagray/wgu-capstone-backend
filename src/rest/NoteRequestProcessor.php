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

require_once "data/access/NoteDao.php";
require_once "rest/Response.php";
require_once "rest/RouteProcessor.php";
require_once "rest/BaseRequestProcessor.php";

class NoteRequestProcessor extends BaseRequestProcessor
{
    private $method;
    private $routes;
    private $note_dao;
    private $response;

    public function __construct($request_method, RouteProcessor $routes)
    {
        $this->method = $request_method;
        $this->routes = $routes;
        $this->note_dao = new NoteDao();
        $this->response = new Response();
    }

    public function getResponse() {
        return $this->response;
    }

    public function processRoutes()
    {
        switch($this->method) {
            case self::GET_REQUEST:
                $this->handleGetRequest();
                break;
            case self::POST_REQUEST:
                $this->handlePostRequest();
                break;
            case self::PATCH_REQUEST:
                $this->handlePatchRequest();
                break;

            default:
                Log::e("Unknown request method");
                $this->response->setStatusCode(Response::HTTP_400);
        }
    }

    // GET
    // ----------------------------------------------------------
    private function handleGetRequest()
    {
        if($this->routes->hasSubRoute())
        {
            // Get specific note
            $note_id = $this->routes->getRoute(1);
            Log::i("Getting note: " . $note_id);
            $this->getNote($note_id);
        } else {
            Log::i("Getting all notes");
            $this->getAllNotes();
        }
    }

    private function getNote($note_id)
    {
        $note = $this->note_dao->load($note_id);

        if($note != null) {
            Log::i("Successfully retrieved data for note: " . $note_id);
            $this->response->setStatusCode(Response::HTTP_200);
            $this->response->setBody( $note );
        } else {
            Log::e("Failed to load data for note: " . $note_id);
            $this->response->setStatusCode(Response::HTTP_404);
        }
    }

    private function getAllNotes()
    {
        $notes = $this->note_dao->loadAll();

        if($notes != null) {
            Log::i("Successfully loaded all note data");
            $this->response->setStatusCode(Response::HTTP_200);
            $this->response->setBody( $notes );
        } else {
            Log::e("Failed to load all note data");
            $this->response->setStatusCode(Response::HTTP_404);
        }
    }

    // POST (new)
    // ----------------------------------------------------------
    private function handlePostRequest()
    {
        Log::i("POSTing new Note");
        $note_data = json_decode(file_get_contents("php://input"), true);
        Log::i("Note data POSTed: " . json_encode( $note_data ));

        // Add note to DB
        $note = new Note();
        $note->setNoteId($note_data['note_id']);
        $note->setText($note_data['text']);

        if( $this->note_dao->save($note) ) {
            Log::i("Note successfully saved to DB");
            $this->response->setStatusCode(Response::HTTP_200);
            $this->response->setBody( $note);
        } else {
            Log::e("Error saving note to DB");
            $this->response->setStatusCode(Response::HTTP_400);
        }
    }

    // PATCH (update)
    // ----------------------------------------------------------
    private function handlePatchRequest()
    {
        Log::i("Handling patch request");

        // If no sub-routes, quit
        if(!($this->routes->hasSubRoute())) {
            Log::e("Attempting to PATCH to note base URL");
            $this->response->setStatusCode(Response::HTTP_400);
            return;
        }

        // Parse Note data
        // TODO: Make this work with objects!
        $note_id = $this->routes->getRoute(1);
        $note_data = json_decode( file_get_contents("php://input"), true);
        Log::i("Raw note data:" .implode("||",  $note_data));

        // TODO: Update date defaults to Now()
        $note = new Note();
        $note->setNoteId($note_data['note_id']);
        $note->setText($note_data['text']);

        // Update note
        if($this->note_dao->load($note->getNoteId()) != null) {
            $rows = $this->note_dao->update($note);
            if($rows == 1) {
                $this->response->setStatusCode(Response::HTTP_200);
                Log::i("Successfully updated note: " . $note->getNoteId());
            } else {
                Log::e("Error updating note, wrong row count");
                $this->response->setStatusCode(Response::HTTP_500);
            }
        }
    }
}
