<?php

class Track {
    public int $TrackId;
    public string $Name;
    public ?int $AlbumId;
    public int $MediaTypeId;
    public ?int $GenreId;
    public ?string $Composer;
    public int $Milliseconds;
    public ?int $Bytes;
    public float $UnitPrice;
}