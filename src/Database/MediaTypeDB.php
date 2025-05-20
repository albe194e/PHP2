<?php

require_once __DIR__ . '/BaseRepository.php';
require_once __DIR__ . '/../Models/MediaType.php';

class MediaTypeDB extends BaseRepository {
	protected string $table = 'MediaType';
    protected string $modelClass = MediaType::class;
}
