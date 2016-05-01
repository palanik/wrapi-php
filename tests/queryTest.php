<?php

class wrapiQueryTest extends \PHPUnit_Framework_TestCase {
    use \InterNations\Component\HttpMock\PHPUnit\HttpMockTrait;

    protected static $client;

    public static function setUpBeforeClass() {
        static::setUpHttpMockBeforeClass('8082', 'localhost');
        $endPoints = json_decode(' 
          {
            "books.author" : {
              "method" : "GET",
              "path": "books",
              "query": {
                "type": "author"
              }
            },
            "books.genre" : {
              "method" : "GET",
              "path": "books",
              "query": {
                "type": "genre"
              }
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
                ->pathIs('/v1/books?type=author&q=Homer')
            ->then()
                ->body(json_encode([
                        array("id" => 1, "name" => "Odyssey", "author" => "Homer"),
                        array("id" => 2, "name" => "Iliad", "author" => "Homer")
                    ])
                )
                ->statusCode(200)
            ->end();
        $this->http->setUp();

        $response = self::$client->books->author(array("q" => 'Homer'));
        $this->assertNotNull($response);
        $this->assertEquals([
                array("id" => 1, "name" => "Odyssey", "author" => "Homer"),
                array("id" => 2, "name" => "Iliad", "author" => "Homer")
                ],
                json_decode($response, true));
    }

    public function testByGenre() {
        $this->http->mock
            ->when()
                ->methodIs('GET')
                ->pathIs('/v1/books?type=genre&q=sci-fi')
            ->then()
                ->body(json_encode([
                        array("id" => 4, "name" => "The Time Machine")
                    ])
                )
                ->statusCode(200)
            ->end();
        $this->http->setUp();

        $response = self::$client->books->genre(array("q" => 'sci-fi'));
        $this->assertNotNull($response);
        $this->assertEquals([
                array("id" => 4, "name" => "The Time Machine")
                ],
                json_decode($response, true));
    }

    public function testByAuthorOverride() {
        $this->http->mock
            ->when()
                ->methodIs('GET')
                ->pathIs('/v1/books?type=genre&q=sci-fi')
            ->then()
                ->body(json_encode([
                        array("id" => 4, "name" => "The Time Machine")
                    ])
                )
                ->statusCode(200)
            ->end();
        $this->http->setUp();

        $response = self::$client->books->author(array("type" => 'genre', "q" => 'sci-fi'));
        $this->assertNotNull($response);
        $this->assertEquals([
                array("id" => 4, "name" => "The Time Machine")
                ],
                json_decode($response, true));
    }
}