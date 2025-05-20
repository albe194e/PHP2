<?php

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../Database/TrackDB.php';
require_once __DIR__ . '/../Models/Track.php';

class TrackController extends BaseController {

    protected string $modelClass = 'Track';
    protected string $repositoryClass = 'TrackDB';

    public function __construct() {
        parent::__construct($this->modelClass, $this->repositoryClass);
    }

	public function getAll(?string $search = null) {
		$repository = new $this->repositoryClass();
		Logger::log("Fetching all records from " . $this->repositoryClass);
		return json_encode($repository->getAll($search));
	}

	public function getAllByComposer($composer) {
		$repository = new $this->repositoryClass();
		Logger::log("Fetching all records from " . $this->repositoryClass);
		return json_encode($repository->getAllByComposer($composer));
	}

    public function handle_method(array $request, string $method) {
        switch ($method) {
            case 'GET':

				if ($_GET['composer'] ?? null) {
					$composer = $_GET['composer'];
					return $this->getAllByComposer($composer);
				}
                if ($this->isId($request[1] ?? null)) {
                    return $this->get($request[1]);
                } else {
					$search = $_GET['s'] ?? null;
                    return $this->getAll($search);
                }
            case 'POST':
                return $this->create();
            case 'PUT':
                if ($this->isId($request[1] ?? null)) {
                    return $this->update($request[1]);
                } else {
                    throw new Exception("ID is required for update");
                }
            case 'DELETE':
                if ($this->isId($request[1] ?? null)) {
                    return $this->delete($request[1]);
                } else {
                    throw new Exception("ID is required for delete");
                }
            default:
                throw new Exception("Method not allowed");
        }
    }
}