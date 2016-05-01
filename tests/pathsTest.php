<?php

class wrapiPathsTest extends \PHPUnit_Framework_TestCase {
    use \InterNations\Component\HttpMock\PHPUnit\HttpMockTrait;

    protected static $client;

    public static function setUpBeforeClass() {
        static::setUpHttpMockBeforeClass('8082', 'localhost');
        $endPoints = json_decode(' 
          {
            "simple": {
              "method": "GET",
              "path": "simple"
            },
            "fullPath": {
              "method": "GET",
              "path": "path/to/endpoint"
            },
            "relativePath": {
              "method": "GET",
              "path": "../v1/relative/path/to/endpoint"
            },
            "pathParam": {
              "method": "GET",
              "path": "path/:to/endpoint"
            },
            "empty": {
              "method": "GET",
              "path": ""
            }
          }', true);

        self::$client = new wrapi\wrapi('http://localhost:8082/v1/', 
            $endPoints, []);
    }

    public static function tearDownAfterClass() {
        static::tearDownHttpMockAfterClass();
    }

    public function setUp() {
        $this->setUpHttpMock();
    }

    public function tearDown() {
        $this->tearDownHttpMock();
    }

    public function testSimple() {
        $this->http->mock
            ->when()
                ->methodIs('GET')
                ->pathIs('/v1/simple')
            ->then()
                ->body("Simple")
                ->statusCode(200)
            ->end();
        $this->http->setUp();

        $response = self::$client->simple();
        $this->assertNotNull($response);
        $this->assertEquals("Simple", $response);
    }

    public function testfullPath() {
        $this->http->mock
            ->when()
                ->methodIs('GET')
                ->pathIs('/v1/path/to/endpoint')
            ->then()
                ->body("path/to/endpoint")
                ->statusCode(200)
            ->end();
        $this->http->setUp();

        $response = self::$client->fullPath();
        $this->assertNotNull($response);
        $this->assertEquals("path/to/endpoint", $response);
    }

    public function testrelativePath() {
        $this->http->mock
            ->when()
                ->methodIs('GET')
                ->pathIs('/v1/relative/path/to/endpoint')
            ->then()
                ->body("relative/path/to/endpoint")
                ->statusCode(200)
            ->end();
        $this->http->setUp();

        $response = self::$client->relativePath();
        $this->assertNotNull($response);
        $this->assertEquals("relative/path/to/endpoint", $response);
    }

    public function testemptyPath() {
        $this->http->mock
            ->when()
                ->methodIs('GET')
                ->pathIs('/v1/')
            ->then()
                ->body("empty")
                ->statusCode(200)
            ->end();
        $this->http->setUp();

        $response = self::$client->empty();
        $this->assertNotNull($response);
        $this->assertEquals("empty", $response);
    }

}