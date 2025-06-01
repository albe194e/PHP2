<?php

require_once __DIR__ . '/BaseRepository.php';
require_once __DIR__ . '/../Models/Track.php';

class TrackDB extends BaseRepository {
	
	protected string $table = 'Track';
    protected string $modelClass = Track::class;

	public function getAll(?string $search = null): array
	{
		try {
			$sql = "SELECT 
						Track.*,
						MediaType.MediaTypeId AS MediaType_MediaTypeId,
						MediaType.Name AS MediaType_Name,
						Genre.GenreId AS Genre_GenreId,
						Genre.Name AS Genre_Name
					FROM Track
					LEFT JOIN MediaType ON Track.MediaTypeId = MediaType.MediaTypeId
					LEFT JOIN Genre ON Track.GenreId = Genre.GenreId";
			if ($search) {
				$sql .= " WHERE Track.Name LIKE :search";
			}
			$stmt = $this->connect()->prepare($sql);
			if ($search) {
				$stmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
			}
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
			Logger::log($e->getMessage(), 'ERROR');
            return [
                'ok'      => false, 
                'message' => 'Database error'
            ];
		}
	}

	public function getAllByComposer($composer) {

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
					WHERE Track.Composer LIKE :composer";
			$stmt = $this->connect()->prepare($sql);
			$stmt->bindValue(':composer', "%$composer%", PDO::PARAM_STR);
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
			Logger::log($e->getMessage(), 'ERROR');
            return [
                'ok'      => false, 
                'message' => 'Database error'
            ];
		}
		
	}

	public function delete(int $id): array
	{
		try {
			// Check if the track is in any playlist
			$sql = "SELECT COUNT(*) FROM PlaylistTrack WHERE TrackId = :trackId";
			$stmt = $this->connect()->prepare($sql);
			$stmt->bindValue(':trackId', $id, PDO::PARAM_INT);
			$stmt->execute();
			$playlistCount = $stmt->fetchColumn();

			if ($playlistCount > 0) {
				return [
					'ok'      => false,
					'message' => 'Cannot delete track. It is in a playlist.'
				];
			}

			return parent::delete($id);
		} catch (PDOException $e) {
			Logger::log($e->getMessage(), 'ERROR');
            return [
                'ok'      => false, 
                'message' => 'Database error'
            ];
		}
	}
}