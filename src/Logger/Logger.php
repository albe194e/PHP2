<?php

class Logger
{
    private static string $logFile = __DIR__ . '/../../logs/api.log';

    public static function setLogFile(string $logFile): void
    {
        self::$logFile = $logFile;
    }

    public static function log(string $message, string $level = 'INFO'): void
    {
        $date = date('Y-m-d H:i:s');
        $entry = "[$date][$level] $message" . PHP_EOL;
        file_put_contents(self::$logFile, $entry, FILE_APPEND | LOCK_EX);
    }

    public static function logRequest(): void
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'UNKNOWN';
        $uri = $_SERVER['REQUEST_URI'] ?? 'UNKNOWN';
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN';
        $body = file_get_contents('php://input');
        $body = $body ? json_encode(json_decode($body), JSON_UNESCAPED_SLASHES) : '';
        $message = "Request: $method $uri from $ip" . ($body ? " | Body: $body" : '');
        self::log($message, 'REQUEST');
    }
}