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
        switch ($method) {
            case 'GET':
                    return $this->getAll();
            default:
                throw new Exception("Method not allowed");
        }
    }
}