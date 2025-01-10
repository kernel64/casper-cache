<?php

namespace Casper;

class CacheClient
{
    private $socket;

    public function __construct(private string $host = '127.0.0.1', private int $port = 100824)
    {
        $this->connect();
    }

    /**
     * Destructor to ensure the socket is properly closed
     */
    public function __destruct()
    {
        if ($this->socket) {
            fclose($this->socket);
        }
    }

    /**
     * @param string $key
     * @return array
     * @throws \Exception
     */
    public function delete(string $key): array
    {
        return $this->sendCommand('DEL', $key);
    }

    /**
     * @param string $key
     * @param callable|null $callback
     * @param int|null $ttl
     * @return mixed
     * @throws \Exception
     */
    public function get(string $key, callable $callback = null, ?int $ttl = null): mixed
    {
        $response = $this->sendCommand('GET', $key);

        if ($response['status'] === 'success' && $response['data'] !== null) {
            return unserialize($response['data']);
        }

        if ($callback !== null) {
            $newValue = $callback($key);
            $this->sendCommand('SET', $key, $newValue, $ttl);

            return $newValue;
        }

        return null;
    }

    /**
     * @return void
     * @throws \Exception
     */
    private function connect(): void
    {
        $this->socket = stream_socket_client("tcp://{$this->host}:{$this->port}", $errno, $errstr, 30);
        if (!$this->socket) {
            throw new \Exception("Could not connect to server: $errstr ($errno)");
        }
    }

    /**
     * @param string $command
     * @param string|null $key
     * @param mixed|null $value
     * @param int|null $ttl
     * @return array
     * @throws \Exception
     */
    private function sendCommand(string $command, ?string $key = null, mixed $value = null, ?int $ttl = null): array
    {
        $payload = json_encode(array_filter([
            'command' => $command,
            'key' => $key,
            'value' => $value,
            'ttl' => $ttl
        ]));

        if ($this->socket !== false) {
            $this->connect();
        }

        fwrite($this->socket, $payload . "\n");
        $response = fgets($this->socket);

        if (!$response) {
            throw new \Exception('No response from server');
        }

        $decodedResponse = json_decode($response, true);
        if ($decodedResponse === null) {
            throw new \Exception('Failed to decode server response');
        }

        return $decodedResponse;
    }
}
