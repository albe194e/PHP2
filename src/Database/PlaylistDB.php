<?php

require_once __DIR__ . '/BaseRepository.php';
require_once __DIR__ . '/../Models/Playlist.php';

class PlaylistDB extends BaseRepository {

	protected string $table = 'Playlist';
    protected string $modelClass = Playlist::class;

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
			// Get the playlist
			$sql = "SELECT * FROM {$this->table} WHERE {$this->table}Id = :id";
			$stmt = $this->connect()->prepare($sql);
			$stmt->execute(['id' => $id]);
			$playlist = $stmt->fetchObject($this->modelClass);

			if (!$playlist) {
				return [
					'ok'      => false,
					'message' => 'No record found'
				];
			}

			// Get tracks for this playlist
			$sqlTracks = "SELECT Track.* 
						FROM PlaylistTrack 
						JOIN Track ON PlaylistTrack.TrackId = Track.TrackId 
						WHERE PlaylistTrack.PlaylistId = :id";
			$stmtTracks = $this->connect()->prepare($sqlTracks);
			$stmtTracks->execute(['id' => $id]);
			$tracks = $stmtTracks->fetchAll(PDO::FETCH_ASSOC);

			// Attach tracks to playlist
			$playlist->Tracks = $tracks;

			return [
				'ok'   => true,
				'data' => $playlist
			];
		} catch (PDOException $e) {
			return [
				'ok'      => false,
				'message' => $e->getMessage()
			];
		}
	}

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
			return [
				'ok'      => false, 
				'message' => $e->getMessage()
			];
		}
	}

	public function addTrackToPlaylist(int $playlistId, int $trackId): array {
		try {
			$sql = "INSERT INTO PlaylistTrack (PlaylistId, TrackId) VALUES (:playlistId, :trackId)";
			$stmt = $this->connect()->prepare($sql);
			$stmt->execute(['playlistId' => $playlistId, 'trackId' => $trackId]);

			return [
				'ok'      => true,
				'message' => 'Track added to playlist'
			];
		} catch (PDOException $e) {
			return [
				'ok'      => false,
				'message' => $e->getMessage()
			];
		}
	}

	public function removeTrackFromPlaylist(int $playlistId, int $trackId): array {
		try {
			$sql = "DELETE FROM PlaylistTrack WHERE PlaylistId = :playlistId AND TrackId = :trackId";
			$stmt = $this->connect()->prepare($sql);
			$stmt->execute(['playlistId' => $playlistId, 'trackId' => $trackId]);

			return [
				'ok'      => true,
				'message' => 'Track removed from playlist'
			];
		} catch (PDOException $e) {
			return [
				'ok'      => false,
				'message' => $e->getMessage()
			];
		}
	}

	public function delete(int $id): array {
		try {
			// Check if the playlist has any tracks
			$sql = "SELECT COUNT(*) FROM PlaylistTrack WHERE PlaylistId = :playlistId";
			$stmt = $this->connect()->prepare($sql);
			$stmt->bindValue(':playlistId', $id, PDO::PARAM_INT);
			$stmt->execute();
			$trackCount = $stmt->fetchColumn();

			if ($trackCount > 0) {
				return [
					'ok'      => false,
					'message' => 'Cannot delete playlist: playlist has one or more tracks'
				];
			}

			// Proceed with delete if no tracks
			return parent::delete($id);
		} catch (PDOException $e) {
			return [
				'ok'      => false,
				'message' => $e->getMessage()
			];
		}
	}
}
