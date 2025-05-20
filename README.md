# Chinook API

## Overview
The Chinook API is a RESTful API designed to interact with a digital media store represented by the Chinook database. This API allows users to manage artists, albums, tracks, genres, media types, and playlists.

## Project Structure
```
chinook-api
├── public
│   └── index.php                # Entry point for the API
├── src
│   ├── Controllers              # Contains controller classes for handling requests
│   │   ├── AlbumController.php
│   │   ├── ArtistController.php
│   │   ├── GenreController.php
│   │   ├── MediaTypeController.php
│   │   ├── PlaylistController.php
│   │   └── TrackController.php
│   ├── Models                   # Contains model classes for database interaction
│   │   ├── Album.php
│   │   ├── Artist.php
│   │   ├── Genre.php
│   │   ├── MediaType.php
│   │   ├── Playlist.php
│   │   └── Track.php
│   ├── Database                 # Contains the Database class for connection handling
│   │   └── Database.php
│   ├── Logger                   # Contains the Logger class for logging requests
│   │   └── Logger.php
│   ├── Router                   # Contains the Router class for request routing
│   │   └── Router.php
│   └── Utils                    # Contains utility classes
│       └── Response.php
├── logs                         # Directory for log files
│   └── api.log
├── config                       # Configuration files
│   └── config.php
├── composer.json                # Composer dependencies
└── README.md                    # Project documentation
```

## Requirements
- PHP 8 or higher
- A compatible relational database management system (e.g., MySQL, PostgreSQL)
- Apache web server

## Setup Instructions
1. Clone the repository:
   ```
   git clone <repository-url>
   cd chinook-api
   ```

2. Install dependencies using Composer:
   ```
   composer install
   ```

3. Configure the database connection in `config/config.php`.

4. Set up the database using the provided Chinook database scripts.

5. Deploy the application in the document root of an Apache web server.

## Usage
- The API can be accessed at the base URL of your server.
- Use the appropriate HTTP methods (GET, POST, PUT, DELETE) to interact with the endpoints as specified in the project requirements.

## Logging
All API requests and errors will be logged in the `logs/api.log` file for monitoring and debugging purposes.

## Security
Ensure to implement proper security measures to protect against SQL injection, XSS, and other common vulnerabilities.

## Contribution
Feel free to contribute to the project by submitting issues or pull requests.