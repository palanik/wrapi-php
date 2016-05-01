<?php
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Exception\RequestException;

class httpErrorsTest extends \PHPUnit_Framework_TestCase {
    use \InterNations\Component\HttpMock\PHPUnit\HttpMockTrait;

    protected static $client;

    public static function setUpBeforeClass() {
        static::setUpHttpMockBeforeClass('8082', 'localhost');
        $endPoints = json_decode(' 
            {
                "list": {
                    "method": "GET",
                    "path": "books"
                },
                "item": {
                    "method": "GET",
                    "path": "books/:id"
                }            
            }', true);

        self::$client = new wrapi\wrapi('http://localhost:8082/v1/', 
            $endPoints);
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

    public function testListRequest() {
        $this->http->mock
            ->when()
                ->methodIs('GET')
                ->pathIs('/v1/books')
            ->then()
                ->body(json_encode([
                        array("id" => 1, "name" => "Odyssey", "author" => "Homer"),
                        array("id" => 2, "name" => "Iliad", "author" => "Homer")
                    ])
                )
                ->statusCode(200)
            ->end();
        $this->http->setUp();

        $response = self::$client->list();
        $this->assertNotNull($response);
        $this->assertEquals([
                array("id" => 1, "name" => "Odyssey", "author" => "Homer"),
                array("id" => 2, "name" => "Iliad", "author" => "Homer")
                ],
                json_decode($response, true));
    }

    public function testValidItem() {
        $this->http->mock
            ->when()
                ->methodIs('GET')
                ->pathIs('/v1/books/2')
            ->then()
                ->body(json_encode(
                        array("id" => 2, "name" => "Odyssey", "author" => "Homer")
                        )
                )
                ->statusCode(200)
            ->end();
        $this->http->setUp();

        $response = self::$client->item(2);
        $this->assertNotNull($response);
        $this->assertEquals(array("id" => 2, "name" => "Odyssey", "author" => "Homer"),
                json_decode($response, true));
    }

    public function testInvalidItem() {
        $this->http->mock
            ->when()
                ->methodIs('GET')
                ->pathIs('/v1/books/2')
            ->then()
                ->body(json_encode(
                        array("id" => 2, "name" => "Odyssey", "author" => "Homer")
                        )
                )
                ->statusCode(200)
            ->end();
        $this->http->setUp();

        $response = null;
        try {
            $response = self::$client->item(3);
        }
        catch(RequestException $re) {
            $this->assertEquals($re->getResponse()->getStatusCode(), '404');
        }
        $this->assertNull($response);
   }
}