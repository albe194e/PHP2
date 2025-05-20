<?php

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../Database/PlaylistDB.php';
require_once __DIR__ . '/../Models/Playlist.php';

class PlaylistController extends BaseController {

    protected string $modelClass = 'Playlist';
    protected string $repositoryClass = 'PlaylistDB';

    public function __construct() {
        parent::__construct($this->modelClass, $this->repositoryClass);
    }

	public function getAll(?string $search = null) {
		$repository = new $this->repositoryClass();
		Logger::log("Fetching all records from " . $this->repositoryClass);
		return json_encode($repository->getAll($search));
	}

	public function addTrack($id) {
		$repository = new $this->repositoryClass();

		$body = json_decode(file_get_contents('php://input'), true);
		if (isset($body['track_id'])) {
			$trackId = $body['track_id'];
			$result = $repository->addTrackToPlaylist($id, $trackId);
			return json_encode($result);
		} else {
			throw new Exception("Track ID is required");
		}
	}

	public function removeTrack($playlistId, $trackId) {
		$repository = new $this->repositoryClass();
		$result = $repository->removeTrackFromPlaylist($playlistId, $trackId);
		return json_encode($result);
	}

    public function handle_method(array $request, string $method) {
        switch ($method) {
            case 'GET':
                if ($this->isId($request[1] ?? null)) {
                    return $this->get($request[1]);
                } else {
					$search = $_GET['s'] ?? null;
                    return $this->getAll($search);
                }
            case 'POST':

				if ($this->isId($request[1]) && $request[2] == 'tracks') {
					return $this->addTrack($request[1]);
				}
                return $this->create();
            case 'PUT':
                if ($this->isId($request[1] ?? null)) {
                    return $this->update($request[1]);
                } else {
                    throw new Exception("ID is required for update");
                }
            case 'DELETE':

				if ($this->isId($request[1] ?? null) && $request[2] == 'tracks' && $this->isId($request[3] ?? null)) {
					$repository = new $this->repositoryClass();
					$trackId = $request[3];
					return $this->removeTrack($request[1], $trackId);
				}
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