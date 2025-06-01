<?php

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../Database/GenreDB.php';
require_once __DIR__ . '/../Models/Genre.php';

class GenreController extends BaseController {

    protected string $modelClass = 'Genre';
    protected string $repositoryClass = 'GenreDB';

    public function __construct() {
        parent::__construct($this->modelClass, $this->repositoryClass);
    }

    public function handle_method(array $request, string $method) {
        try {
			// Handle PUT
            if ($method === 'POST' && (isset($_GET['_method']) && strtolower($_GET['_method']) === 'put')) {
                http_response_code(405);
                return json_encode(['error' => 'Update not supported']);
            }

            switch ($method) {
                case 'GET':
                    return $this->getAll();
                case 'PUT':
                    http_response_code(405);
                    return json_encode(['error' => 'Direct PUT not allowed. Use POST with ?_method=put']);
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