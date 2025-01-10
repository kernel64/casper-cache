<?php

/***
 * Run test.php after running run.php in cli
 *
 * Test 1 : string
 * Value for 'testKey': string Hello world!
 * ***********************************************
 * Test 2 : int
 * Value for 'testKey': integer 1234
 * ***********************************************
 * Test 3 : float
 * Value for 'testKey': double 3.69
 * ***********************************************
 * Test 4 : bool
 * Value for 'testKey': boolean 1
 * ***********************************************
 * Test 5 : Array
 * Value for 'testKey': array
 * Array
 * (
 * [0] => toto
 * [1] => 122
 * [2] => 5.4
 * [3] =>
 * [hello] => world
 * )
 *
 * ***********************************************
 * Test 6 : expired key
 * Value for 'testKey': NULL
 *
 */

use Casper\CacheClient;

require_once "vendor/autoload.php";

$client = new CacheClient();


echo "Test 1 : string\n";
$value = $client->get('testkey', function () {
    return 'Hello world!';
}, 5);
echo "Value for 'testKey': ".gettype($value)." $value \n";
echo "************************************************\n";
$client->delete('testkey');
echo "Test 2 : int\n";
$value = $client->get('testkey', function () {
    return 1234;
}, 5);
echo "Value for 'testKey': ".gettype($value)." $value \n";
echo "************************************************\n";
$client->delete('testkey');
echo "Test 3 : float\n";
$value = $client->get('testkey', function () {
    return 3.69;
}, 5);
echo "Value for 'testKey': ".gettype($value)." $value \n";
echo "************************************************\n";
$client->delete('testkey');
echo "Test 4 : bool\n";
$value = $client->get('testkey', function () {
    return true;
}, 5);
echo "Value for 'testKey': ".gettype($value)." $value \n";
echo "************************************************\n";
$client->delete('testkey');
echo "Test 5 : Array\n";
$value = $client->get('testkey', function () {
    return ['toto', 122, 5.4, false, 'hello' => 'world'];
}, 5);
echo "Value for 'testKey': ".gettype($value)."\n". print_r($value, true). " \n";
echo "************************************************\n";
sleep(6);
echo "Test 6 : expired key \n";
$value = $client->get('testkey');
echo "Value for 'testKey': ".gettype($value)."\n". print_r($value, true). " \n";


