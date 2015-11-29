<?php

namespace wrapi;

require __DIR__ . '/nester.php';

class wrapi {
    public function __construct($baseURL, 
        array $endpoints = array(), 
        array $opts = array(), 
        array $guzzleOpts = array()) {

        $this->baseURL = $baseURL;
        $this->opts = $opts;
        $this->guzzleOpts = $guzzleOpts;
        $this->endpoints = new NestedDeco();

        foreach ($endpoints as $key => $value) {
            $this->register($key, $value);
        }
    }

    private function api($apiEndpoint) {
        return function() use ($apiEndpoint) {
            $args = func_get_args();
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
                \Sabre\Uri\resolve($this->baseURL, $apiEndpoint['path'])
            );

            $opts = array_merge(array(), $this->opts);
            
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
                if (is_array($body) && array_keys($body) !== range(0, count($body) - 1)) {
                    $opts['json'] = $body;
                }
                else {
                    $opts['body'] = $body;
                }
            }

            // Request & Response
            $client = new \GuzzleHttp\Client($this->guzzleOpts);
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
        };
    }

    public function register($action, $endpoint) {
        $pathArray = explode('.', $action);
        if ($pathArray[0] === "register") {
            throw new \RuntimeException('"register" is a reserved function name for wrapi. Please use an alias (eg. "Register", "_register").');
            return;
        }

        $last = array_pop($pathArray);
        $tail = array_reduce($pathArray, 
            function(&$acc, $a) {
                if (!$acc->$a) {
                    $acc->$a = new NestedDeco();
                }
                else if (is_callable($acc->$a)) {
                  throw new \RuntimeException('Property ' + $a + ' already registered as function path.');
                }
                return $acc->$a;
            },
            $this->endpoints
        );

        $tail->$last = $this->api($endpoint);

        return $this;
    }

  public function __get($name) {
    return $this->endpoints->$name;
  }
  
  public function __call($name, $args) {
    return call_user_func_array($this->endpoints->$name, $args);
  }
}

?>