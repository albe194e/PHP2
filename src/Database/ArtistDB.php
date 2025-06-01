<?php

require_once __DIR__ . '/../Models/Artist.php';
require_once __DIR__ . '/../Models/Album.php';
require_once __DIR__ . '/BaseRepository.php';

class ArtistDB extends BaseRepository {

	protected string $table = 'Artist';
	protected string $modelClass = Artist::class;

	public function getAll(?string $search = null): array {
		try {
			$sql = "SELECT * FROM {$this->table}";

			if ($search) {
				$sql .= " WHERE Name LIKE :search";
			}

			$stmt = $this->connect()->prepare($sql);

			if ($search) {
				$stmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
			}

			$stmt->execute();
			$result = $stmt->fetchAll(PDO::FETCH_CLASS, $this->modelClass);

			return [
				'ok'   => true, 
				'data' => $result
			];
		} catch (PDOException $e) {
			Logger::log($e->getMessage(), 'ERROR');
            return [
                'ok'      => false, 
                'message' => 'Database error'
            ];
		}
	}

	public function getAlbumsByArtistId(int $artistId): array {
		$sql = "SELECT * FROM Album WHERE ArtistId = :artistId";
		$stmt = $this->connect()->prepare($sql);
		$stmt->bindValue(':artistId', $artistId, PDO::PARAM_INT);
		$stmt->execute();
		$result = $stmt->fetchAll(PDO::FETCH_CLASS, Album::class);

		return [
				'ok'   => true, 
				'data' => $result
			];
	}

	public function delete(int $id): array {
		try {
			// Check if the artist has any albums
			$sql = "SELECT COUNT(*) FROM Album WHERE ArtistId = :artistId";
			$stmt = $this->connect()->prepare($sql);
			$stmt->execute(['artistId' => $id]);
			$albumCount = $stmt->fetchColumn();

			if ($albumCount > 0) {
				return [
					'ok'      => false,
					'message' => 'Cannot delete artist: artist has one or more albums'
				];
			}

			// Proceed with delete if no albums
			$sql = "DELETE FROM {$this->table} WHERE {$this->table}Id = :id";
			$statement = $this->connect()->prepare($sql);
			$statement->execute(['id' => $id]);

			if ($statement->rowCount() === 0) {
				return [
					'ok'      => false, 
					'message' => 'No record found to delete'
				];
			}
			
			return [
				'ok'      => true, 
				'message' => 'Record deleted successfully'
			];
		} catch (PDOException $e) {
			Logger::log($e->getMessage(), 'ERROR');
            return [
                'ok'      => false, 
                'message' => 'Database error'
            ];
		}
	}
}
