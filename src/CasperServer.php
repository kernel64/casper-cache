<?php

namespace Casper;

class CasperServer
{
    private array $cache = [];
    private int $defaultTTL = 1800;

    public function __construct(private string $host = '127.0.0.1', private int $port = 100824)
    {
    }

    public function start(): void
    {
        $server = stream_socket_server("tcp://{$this->host}:{$this->port}", $errno, $errstr);

        if (!$server) {
            throw new RuntimeException("Error starting server: $errstr ($errno)");
        }

        echo "Server running at {$this->host}:{$this->port}\n";

        while (true) {
            $client = @stream_socket_accept($server, 1);

            if ($client) {
                $request = trim(fgets($client));

                $response = $this->handleRequest($request);

                fwrite($client, json_encode($response) . PHP_EOL);
                fclose($client);
            }
            $this->cleanExpiredKeys();
        }
    }

    private function handleRequest(string $request): array
    {
        $data = json_decode($request, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return ['status' => 'error', 'message' => 'Invalid JSON'];
        }

        $command = strtoupper($data['command'] ?? '');
        $key = $data['key'] ?? null;
        $value = $data['value'] ?? null;
        $ttl = $data['ttl'] ?? null;

        return match ($command) {
            'SET' => $this->set($key, $value, $ttl),
            'GET' => $this->get($key),
            'DEL' => $this->delete($key),
            'ALL' => $this->getAll(),
            default => ['status' => 'error', 'message' => 'Invalid command'],
        };
    }

    private function set(?string $key, mixed $value, ?int $ttl = null): array
    {
        if ($key === null) {
            return ['status' => 'error', 'message' => 'Key is required'];
        }

        $currentTime = time();
        $expiration = $currentTime + ($ttl ?? $this->defaultTTL);

        $this->cache[$key] = [
            'value' => serialize($value),
            'expires_at' => $expiration
        ];

        return ['status' => 'success', 'message' => 'Key set successfully'];
    }


    private function get(?string $key): array
    {
        if ($key === null) {
            return ['status' => 'error', 'message' => 'Key is required'];
        }

        if (isset($this->cache[$key]) && $this->cache[$key]['expires_at'] >= time()) {
            return ['status' => 'success', 'data' => $this->cache[$key]['value']];
        }

        return ['status' => 'success', 'data' => null];
    }


    private function delete(?string $key): array
    {
        if ($key === null) {
            return ['status' => 'error', 'message' => 'Key is required'];
        }

        unset($this->cache[$key]);
        return ['status' => 'success', 'message' => 'OK'];
    }

    private function getAll(): array
    {
        $result = [];
        foreach ($this->cache as $key => $entry) {
            if ($entry['expires_at'] >= time()) {
                $result[$key] = $entry['value'];
            }
        }

        return ['status' => 'success', 'data' => $result];
    }

    private function cleanExpiredKeys(): void
    {
        $currentTime = time();
        $this->cache = array_filter(
            $this->cache,
            fn($entry) => $entry['expires_at'] >= $currentTime
        );
    }
}
