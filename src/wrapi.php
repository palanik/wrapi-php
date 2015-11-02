<?php
namespace wrapi;

class wrapi {
    public function __construct($baseURL, array $endpoints = array(), array $opts = array()) {
        $this->baseURL = $baseURL;
        $this->endpoints = $endpoints;
        $this->opts = $opts;
    }

    // arguments order - param1, param2, ..., querystring?, body?, callback 
    public function __call($name, $args) {
        if (!array_key_exists($name, $this->endpoints)) {
            throw new \RuntimeException('No such registered method. : '. $name);
            return;
        }

        $apiEndpoint = $this->endpoints[$name];

        // Last argument is callable
        $cb = array_pop($args);
        if (!is_callable($cb)) {
            array_push($args, $cb);
            unset($cb);
        }

        // Content body
        if (in_array($apiEndpoint['method'], ['PATCH', 'POST', 'PUT'])) {
            $body = array_pop($args);
        }

        $querystring = array_pop($args);
        // Query Strings are associative arrays
        if (!is_array($querystring) || array_keys($querystring) === range(0, count($querystring) - 1)) {
            array_push($args, $querystring);
            unset($querystring);
        }

        // rest in args are params
        $url = preg_replace_callback("/(:[a-zA-Z_][a-zA-Z0-9_]*)/", 
            function($m) use (&$args) {
                return array_shift($args);
            }, 
            $this->baseURL . $apiEndpoint['path']
        );

        $client = new \GuzzleHttp\Client();
        $opts = $array = array_merge(array(), $this->opts);
        
        // Add Query strings
        if (isset($querystring)) {
            if(!array_key_exists('query', $opts)) {
                $opts['query'] = array();
            }
            $opts['query'] = array_merge($opts['query'], $querystring);
        }

        // Add User-Agent
        if(!array_key_exists('header', $opts)) {
            $opts['header'] = array();
        }
        $opts['header'] = array_merge(array('User-Agent' => 'wrapi-client'), $opts['header']);

        // Add body
        if (isset($body)) {
            if (is_array($body) && array_keys($body) === range(0, count($body) - 1)) {
                $opts['json'] = $body;
            }
            else {
                $opts['body'] = $body;
            }
        }

        // Request & Response
        try {
            $response = $client->request($apiEndpoint['method'], 
                $url,
                $opts
            );

            $respBody = (string ) $response->getBody();

            // Parse json if content-type is 'appication/json'
            if ($response->hasHeader('Content-Type') && strpos($response->getHeader('Content-Type')[0], 'application/json') === 0) {
                $respBody = json_decode($respBody, TRUE);
            }

            // Has callback?
            if (isset($cb)) {
                $cb(null, $respBody, $response);
                return;
            }
            else {
                return $respBody;
            }
        }
        catch (Exception $e) {
            if (isset($cb)) {
                $cb($e);
            }
            else {
                throw $e;
            }
        }
    }
}

?>