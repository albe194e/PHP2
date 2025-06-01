<?php

header('Content-Type: application/json');

ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);

// Parse the request
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];
$segments = explode('/', trim($uri, '/'));

if (empty($segments[0])) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid endpoint']);
    exit;
}

switch (strtolower($segments[0])) {
	case 'album':
		require_once __DIR__ . '/src/Controllers/AlbumController.php';
    	$controller = new AlbumController();
		$result = $controller->handle_method($segments, $method);

		echo $result;
		break;

	case 'artist':
		require_once __DIR__ . '/src/Controllers/ArtistController.php';
		$controller = new ArtistController();
		$result = $controller->handle_method($segments, $method);

		echo $result;
		break;

	case 'track':
		require_once __DIR__ . '/src/Controllers/TrackController.php';
		$controller = new TrackController();
		$result = $controller->handle_method($segments, $method);

		echo $result;
		break;
	
	case 'mediatype':
		require_once __DIR__ . '/src/Controllers/MediaTypeController.php';
		$controller = new MediaTypeController();
		$result = $controller->handle_method($segments, $method);

		echo $result;
		break;

	case 'genre':
		require_once __DIR__ . '/src/Controllers/GenreController.php';
		$controller = new GenreController();
		$result = $controller->handle_method($segments, $method);

		echo $result;
		break;

	case 'playlist':
		require_once __DIR__ . '/src/Controllers/PlaylistController.php';
		$controller = new PlaylistController();
		$result = $controller->handle_method($segments, $method);

		echo $result;
		break;

	default:
		http_response_code(404);
    	echo json_encode(['error' => 'Not found']);

		break;
}
