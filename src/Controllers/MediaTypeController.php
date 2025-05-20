<?php

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../Database/MediaTypeDB.php';
require_once __DIR__ . '/../Models/MediaType.php';

class MediaTypeController extends BaseController {

    protected string $modelClass = 'MediaType';
    protected string $repositoryClass = 'MediaTypeDB';

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