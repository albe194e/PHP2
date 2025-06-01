<?php

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../Database/ArtistDB.php';
require_once __DIR__ . '/../Models/Artist.php';


class ArtistController extends BaseController {


    protected string $modelClass = 'Artist';
    protected string $repositoryClass = 'ArtistDB';

    public function __construct() {
        parent::__construct($this->modelClass, $this->repositoryClass);
    }

    public function getAlbums($id) {
        try {
            $repository = new $this->repositoryClass();
            $result = $repository->getAlbumsByArtistId($id);

            if ($result['ok']) {
                http_response_code(200);
                return json_encode($result);
            } else {
                http_response_code(404);
                return json_encode(['error' => $result['message'] ?? 'Not found']);
            }
        } catch (Exception $e) {
            http_response_code(500);
            Logger::log($e->getMessage(), 'ERROR');
            return json_encode(['error' => 'Internal Server Error']);
        }
    }

    public function getAll(?string $search = null) {
        try {
            $repository = new $this->repositoryClass();
            Logger::log("Fetching all records from " . $this->repositoryClass);
            $result = $repository->getAll($search);

            if ($result['ok']) {
                http_response_code(200);
                return json_encode($result);
            } else {
                http_response_code(404);
                return json_encode(['error' => $result['message'] ?? 'Not found']);
            }
        } catch (Exception $e) {
            http_response_code(500);
            Logger::log($e->getMessage(), 'ERROR');
            return json_encode(['error' => 'Internal Server Error']);
        }
    }

    public function handle_method(array $request, string $method) {
        try {
			// Handle PUT
            if ($method === 'POST' && (isset($_GET['_method']) && strtolower($_GET['_method']) === 'put')) {
                if ($this->isId($request[1] ?? null)) {
                    return $this->update($request[1]);
                } else {
                    http_response_code(400);
                    return json_encode(['error' => 'ID is required for update']);
                }
            }

            switch ($method) {
                case 'GET':
                    if ($this->isId($request[1] ?? null) && ($request[2] ?? null) == 'albums') {
                        return $this->getAlbums($request[1]);
                    } elseif ($this->isId($request[1] ?? null)) {
                        return $this->get($request[1]);
                    } else {
                        $search = $_GET['s'] ?? null;
                        return $this->getAll($search);
                    }
                case 'POST':
                    return $this->create();
                case 'PUT':
                    http_response_code(405);
                    return json_encode(['error' => 'Direct PUT not allowed. Use POST with ?_method=put']);
                case 'DELETE':
                    if ($this->isId($request[1] ?? null)) {
                        return $this->delete($request[1]);
                    } else {
                        http_response_code(400);
                        return json_encode(['error' => 'ID is required for delete']);
                    }
                default:
                    http_response_code(405);
                    return json_encode(['error' => 'Method not allowed']);
            }
        } catch (Exception $e) {
            http_response_code(500);
            Logger::log($e->getMessage(), 'ERROR');
            return json_encode(['error' => 'Internal Server Error']);
        }

    }
}
