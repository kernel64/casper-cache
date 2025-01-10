
# Casper Cache - A PHP Cache Server

A simple in-memory cache server written in PHP that supports `SET`, `GET`, `DEL`, and `ALL` commands via TCP connections. This project allows clients to connect to the server and store key-value pairs in memory, with optional TTL (Time-To-Live) for each entry.

## Features
- Supports `SET`, `GET`, `DEL`, and `ALL` commands.
- Cache values can be stored with optional TTL (default 30 minutes).
- Simple key-value storage in memory with automatic cleanup when TTL expires.
- Supports different data types for values (e.g., strings, integers, arrays, etc.).

## Requirements
- PHP 8.1 or higher.
- A TCP client capable of sending JSON-based requests.

## Installation

1. **Clone this repository:**
   ```bash
   git clone https://github.com/kernel64/casper-cache.git
   cd casper-cache
   ```

2. **Install the dependencies:**
   This project does not require any additional dependencies. (for the moment)

3. **Start the cache server:**

   Run the `server` script:
   ```bash
   php run.php
   ```

   The server will start listening on `127.0.0.1:100824` by default. Adjust the address and port as needed.

---

## Example of use

1. ** Using CacheClient.php:**

   A client that connects to the Casper Cache server and sends requests.

   ```php
   <?php
    $client = new \Casper\CacheClient();
    
    // Set a value
    $client->sendCommand('SET', 'key1', 'value1', 3600);
    
    // Get a value
    $value = $client->get('key1');
    echo $value; // Output: value1
    
    // Get a value with a callback if it doesn't exist
    $newValue = $client->get('key2', function($key) {
    return 'generated_value';
    });
    echo $newValue; // Output: generated_value
    
    // Delete a value
    $response = $client->delete('key1');
    print_r($response); // Response from server

   
2. **More exemples in the test file :**
   ```bash
   php test.php
   ```

The client will connect to the server, send a `SET` request, and display the server response.

---

## supported Commands

### SET

- **Command**: `SET`
- **Description**: Store a value in the cache with a specified TTL (optional).
- **Request Format** (JSON):
  ```json
  {
      "command": "SET",
      "key": "your_key",
      "value": "your_value",
      "ttl": 3600
  }
  ```

### GET

- **Command**: `GET`
- **Description**: Retrieve the value of a key from the cache.
- **Request Format** (JSON):
  ```json
  {
      "command": "GET",
      "key": "your_key"
  }
  ```

### DEL

- **Command**: `DEL`
- **Description**: Delete a key from the cache.
- **Request Format** (JSON):
  ```json
  {
      "command": "DEL",
      "key": "your_key"
  }
  ```

### ALL

- **Command**: `ALL`
- **Description**: Retrieve all keys and their values from the cache.
- **Request Format** (JSON):
  ```json
  {
      "command": "ALL"
  }
  ```

### TODO

- Add logging functionality.
- Support TLS connections.


## License

This project is licensed under the [MIT License](LICENSE).
