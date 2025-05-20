<?php

require_once __DIR__ . '/BaseRepository.php';
require_once __DIR__ . '/../Models/Genre.php';

class GenreDB extends BaseRepository {

	protected string $table = 'Genre';
	protected string $modelClass = Genre::class;


}
