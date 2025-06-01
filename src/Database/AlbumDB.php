<?php

require_once __DIR__ . '/BaseRepository.php';
require_once __DIR__ . '/../Models/Album.php';

class AlbumDB extends BaseRepository {

	protected string $table = 'Album';
    protected string $modelClass = Album::class;

	public function getAll(?string $search = null): array
	{
		try {
			$sql = "SELECT 
						Album.AlbumId, 
						Album.Title, 
						Album.ArtistId, 
						Artist.ArtistId AS Artist_ArtistId, 
						Artist.Name AS Artist_Name
					FROM Album
					JOIN Artist ON Album.ArtistId = Artist.ArtistId";
			if ($search) {
				$sql .= " WHERE Album.Title LIKE :search";
			}
			$statement = $this->connect()->prepare($sql);
			if ($search) {
				$statement->bindValue(':search', "%$search%", PDO::PARAM_STR);
			}
			$statement->execute();
			$albums = $statement->fetchAll(PDO::FETCH_ASSOC);

			foreach ($albums as &$album) {
				$album['Artist'] = [
					'ArtistId' => $album['Artist_ArtistId'],
					'Name'     => $album['Artist_Name']
				];
				unset($album['Artist_ArtistId'], $album['Artist_Name'], $album['ArtistId']);
			}

			return [
				'ok'   => true,
				'data' => $albums
			];
		} catch (PDOException $e) {
			Logger::log($e->getMessage(), 'ERROR'); // Log the real error
			return [
				'ok'      => false,
				'message' => 'Database error'
			];
		}
	}

	public function get(int $id): array
	{
		if (!is_numeric($id)) {
			return [
				'ok'      => false, 
				'message' => 'ID must be a number'
			];
		}

		if ($id < 1) {
			return [
				'ok'      => false, 
				'message' => 'ID must be greater than 0'
			];
		}

		try {
			$sql = "SELECT 
						Album.AlbumId, 
						Album.Title, 
						Album.ArtistId, 
						Artist.ArtistId AS Artist_ArtistId, 
						Artist.Name AS Artist_Name
					FROM Album
					JOIN Artist ON Album.ArtistId = Artist.ArtistId
					WHERE Album.AlbumId = :id";
			$statement = $this->connect()->prepare($sql);
			$statement->execute(['id' => $id]);
			$album = $statement->fetch(PDO::FETCH_ASSOC);

			if ($album) {
				$album['Artist'] = [
					'ArtistId' => $album['Artist_ArtistId'],
					'Name'     => $album['Artist_Name']
				];
				unset($album['Artist_ArtistId'], $album['Artist_Name'], $album['ArtistId']);

				return [
					'ok'   => true, 
					'data' => $album
				];
			} else {
				return [
					'ok'      => false, 
					'message' => 'No record found'
				];
			}
			
		} catch (PDOException $e) {
			Logger::log($e->getMessage(), 'ERROR'); // Log the real error
			return [
				'ok'      => false,
				'message' => 'Database error'
			];
		}
	}

	public function getTracksByAlbumId($id): array {
		if (!is_numeric($id)) {
			return [
				'ok'      => false, 
				'message' => 'ID must be a number'
			];
		}

		if ($id < 1) {
			return [
				'ok'      => false, 
				'message' => 'ID must be greater than 0'
			];
		}

		try {
			$sql = "SELECT 
						Track.*,
						MediaType.MediaTypeId AS MediaType_MediaTypeId,
						MediaType.Name AS MediaType_Name,
						Genre.GenreId AS Genre_GenreId,
						Genre.Name AS Genre_Name
					FROM Track
					LEFT JOIN MediaType ON Track.MediaTypeId = MediaType.MediaTypeId
					LEFT JOIN Genre ON Track.GenreId = Genre.GenreId
					WHERE Track.AlbumId = :albumId";
			$stmt = $this->connect()->prepare($sql);
			$stmt->bindValue(':albumId', $id, PDO::PARAM_INT);
			$stmt->execute();
			$tracks = $stmt->fetchAll(PDO::FETCH_ASSOC);

			foreach ($tracks as &$track) {
				$track['MediaType'] = [
					'MediaTypeId' => $track['MediaType_MediaTypeId'],
					'Name'        => $track['MediaType_Name']
				];
				$track['Genre'] = [
					'GenreId' => $track['Genre_GenreId'],
					'Name'    => $track['Genre_Name']
				];
				unset(
					$track['MediaType_MediaTypeId'],
					$track['MediaType_Name'],
					$track['Genre_GenreId'],
					$track['Genre_Name'],
					$track['MediaTypeId'],
					$track['GenreId']
				);
			}

			return [
				'ok'   => true, 
				'data' => $tracks
			];
			
		} catch (PDOException $e) {
			Logger::log($e->getMessage(), 'ERROR'); // Log the real error
			return [
				'ok'      => false, 
				'message' => 'Database error'
			];
		}
	}

	public function create(array $data): array {
		if (empty($data)) {
			return [
				'ok'      => false, 
				'message' => 'No data provided'
			];
		}
		if (!isset($data['Title']) || !isset($data['ArtistId'])) {
			return [
				'ok'      => false, 
				'message' => 'Title and ArtistId are required'
			];
		}
		try {

			// Check if the artist exists
			$sql = "SELECT COUNT(*) FROM Artist WHERE ArtistId = :artistId";
			$stmt = $this->connect()->prepare($sql);
			$stmt->bindValue(':artistId', $data['ArtistId'], PDO::PARAM_INT);
			$stmt->execute();
			$artistExists = $stmt->fetchColumn();
			if (!$artistExists) {
				return [
					'ok'      => false, 
					'message' => 'Artist does not exist'
				];
			}

			$columns = implode(", ", array_keys($data));
			$placeholders = ":" . implode(", :", array_keys($data));

			$sql = "INSERT INTO {$this->table} ($columns) VALUES ($placeholders)";
			$statement = $this->connect()->prepare($sql);
			$statement->execute($data);

			return [
				'ok'      => true, 
				'message' => 'Record created successfully'
			];
		} catch (PDOException $e) {
			Logger::log($e->getMessage(), 'ERROR'); // Log the real error
			return [
				'ok'      => false, 
				'message' => 'Database error'
			];
		}
	}

	public function delete(int $id): array {
		if (!is_numeric($id)) {
			return [
				'ok'      => false, 
				'message' => 'ID must be a number'
			];
		}

		if ($id < 1) {
			return [
				'ok'      => false, 
				'message' => 'ID must be greater than 0'
			];
		}

		try {
			$sql = "SELECT COUNT(*) FROM Track WHERE AlbumId = :albumId";
			$stmt = $this->connect()->prepare($sql);
			$stmt->bindValue(':albumId', $id, PDO::PARAM_INT);
			$stmt->execute();
			$trackCount = $stmt->fetchColumn();

			if ($trackCount > 0) {
				return [
					'ok'      => false,
					'message' => 'Cannot delete album: album has one or more tracks'
				];
			}

			return parent::delete($id);
			
		} catch (PDOException $e) {
			Logger::log($e->getMessage(), 'ERROR'); // Log the real error
			return [
				'ok'      => false, 
				'message' => 'Database error'
			];
		}	
	}
}
