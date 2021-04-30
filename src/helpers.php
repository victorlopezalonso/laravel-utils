<?php

use Illuminate\Http\Request;

/**
 * Transforms an array or string to underscore convention
 *
 * @param array|string $input array or string to convert
 * @param bool $keys convert only keys
 * @return array|string
 *
 */
function camel_to_underscore($input, $keys = false)
{
    if (!is_array($input)) {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $input));
    }

    $output = [];

    foreach ($input as $key => $value) {
        if ($keys) {
            $output[camel_to_underscore($key)] = is_array($value) ? camel_to_underscore($value, $keys) : $value;
        } else {
            $output[$key] = is_array($value) ? camel_to_underscore($value, $keys) : camel_to_underscore($value);
        }
    }

    return $output;
}

/**
 * Return the request keys using underscore convention
 *
 * @param  array|string $key
 * @param  mixed $default
 * @return Request|string|array
 */
function params($key = null, $default = null)
{
    $request = request($key, $default);

    return is_array($request) ? camel_to_underscore($request, true) : $request;
}

/**
 * Shuffle an associative array
 * @param $list
 * @param bool $maintainKey
 * @return array
 */
function shuffle_assoc($list, $maintainKey = false)
{
    if (!is_array($list)) {
        return $list;
    }

    $keys = array_keys($list);
    shuffle($keys);

    $random = [];
    foreach ($keys as $key) {
        if ($maintainKey) {
            $random[$key] = $list[$key];
        } else {
            $random[] = $list[$key];
        }
    }

    return $random;
}

/**
 * Prints a json with the data parameter and die
 * @param $data
 */
function json_die($data)
{
    echo json_encode($data, JSON_PRETTY_PRINT);
    die();
}

/**
 * Return if the string parameter is binary
 * @param $string
 * @return bool
 */
function is_binary($string)
{
    return preg_match('~[^\x20-\x7E\t\r\n]~', $string) > 0;
}

/**
 * Encrypt the password using sha256 with the app hash
 * @param $password
 * @return string
 */
function encryptWithAppSecret($password)
{
    return hash_hmac('sha256', $password, env('APP_HASH'));
}

function getRandomString($length)
{
    $data = '1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZabcefghijklmnopqrstuvwxyz';

    return substr(str_shuffle($data), 0, $length);
}
