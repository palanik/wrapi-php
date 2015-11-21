<?php

class wrapiQueryTest extends \PHPUnit_Framework_TestCase {
    use \InterNations\Component\HttpMock\PHPUnit\HttpMockTrait;

    protected static $client;

    public static function setUpBeforeClass() {
        static::setUpHttpMockBeforeClass('8082', 'localhost');
        $endPoints = json_decode(' 
          {
            "books" : {
              "method" : "GET",
              "path": "books"
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

    public function testByAuthor() {
        $this->http->mock
            ->when()
                ->methodIs('GET')
                ->pathIs('/v1/books?author=Homer')
            ->then()
                ->body(json_encode([
                        array("id" => 1, "name" => "Odyssey", "author" => "Homer"),
                        array("id" => 2, "name" => "Iliad", "author" => "Homer")
                    ])
                )
                ->statusCode(200)
            ->end();
        $this->http->setUp();

        $response = self::$client->books(array("author" => 'Homer'));
        $this->assertNotNull($response);
        $this->assertEquals([
                array("id" => 1, "name" => "Odyssey", "author" => "Homer"),
                array("id" => 2, "name" => "Iliad", "author" => "Homer")
                ],
                json_decode($response, true));
    }

    public function testByAuthorTitle() {
        $this->http->mock
            ->when()
                ->methodIs('GET')
                ->pathIs('/v1/books?author=Homer&title=Iliad')
            ->then()
                ->body(json_encode([
                        array("id" => 2, "name" => "Iliad", "author" => "Homer")
                    ])
                )
                ->statusCode(200)
            ->end();
        $this->http->setUp();

        $response = self::$client->books(array("author" => 'Homer', "title" => 'Iliad'));
        $this->assertNotNull($response);
        $this->assertEquals([
                array("id" => 2, "name" => "Iliad", "author" => "Homer")
                ],
                json_decode($response, true));
    }
}