<?php

class wrapiNestedArrayTest extends \PHPUnit_Framework_TestCase {
    use \InterNations\Component\HttpMock\PHPUnit\HttpMockTrait;

    protected static $client;

    public static function setUpBeforeClass() {
        static::setUpHttpMockBeforeClass('8082', 'localhost');
        $endPoints = json_decode(' 
          {
            "books.list" : {
              "method" : "GET",
              "path": "books"
            },
            "books.item.get": {
              "method" : "GET",
              "path": "books/:id"
            },
            "books.item.create": {
              "method" : "POST",
              "path": "books"
            },
            "books.item.update": {
              "method" : "PUT",
              "path": "books/:id"
            },
            "books.item.remove": {
              "method" : "DELETE",
              "path": "books/:id"
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

    public function testListRequest() {
        $this->http->mock
            ->when()
                ->methodIs('GET')
                ->pathIs('/v1/books')
            ->then()
                ->body(json_encode([             // list
                    array("id" => 1, "name" => "The Martian"),
                    array("id" => 2, "name" => "The Odyssey")
                    ])
                )
                ->statusCode(200)
            ->end();
        $this->http->setUp();

        $response = self::$client->books->list();
        $this->assertNotNull($response);
        $this->assertEquals([
                array("id" => 1, "name" => "The Martian"),
                array("id" => 2, "name" => "The Odyssey")
                ],
                json_decode($response, true));
    }


    public function testItemRequest() {
        $this->http->mock
            ->when()
                ->methodIs('GET')
                ->pathIs('/v1/books/2')
            ->then()
                ->body(json_encode(
                    array("id" => 2, "name" => "The Odyssey")
                    )
                )
                ->statusCode(200)
            ->end();
        $this->http->setUp();

        $response = self::$client->books->item->get(2);
        $this->assertNotNull($response);
        $this->assertEquals(array("id" => 2, "name" => "The Odyssey"),
                json_decode($response, true));
    }


    public function testCreateRequest() {
        $this->http->mock
            ->when()
                ->methodIs('POST')
                ->pathIs('/v1/books')
            ->then()
                ->body(json_encode(
                    array("id" => 3)
                    )
                )
                ->statusCode(200)
            ->end();
        $this->http->setUp();

        $response = self::$client->books->item->create('{name: "The Metamorphosis"}');
        $this->assertNotNull($response);
        $this->assertEquals(array("id" => 3),
                json_decode($response, true));
   }

    public function testUpdateRequest() {
        $this->http->mock
            ->when()
                ->methodIs('PUT')
                ->pathIs('/v1/books/3')
            ->then()
                ->body(json_encode(
                    array("id" => 3, "name" => "The Time Machine")
                    )
                )
                ->statusCode(200)
            ->end();
        $this->http->setUp();

        $response = self::$client->books->item->update(3, '{name: "The Time Machine"}');
        $this->assertNotNull($response);
        $this->assertEquals(array("id" => 3, "name" => "The Time Machine"),
                json_decode($response, true));
   }

    public function testRemoveRequest() {
        $this->http->mock
            ->when()
                ->methodIs('DELETE')
                ->pathIs('/v1/books/3')
            ->then()
                ->body(json_encode(
                    array("id" => 3, "name" => "The Time Machine")
                    )
                )
                ->statusCode(200)
            ->end();
        $this->http->setUp();

        $response = self::$client->books->item->remove(3);
        $this->assertNotNull($response);
        $this->assertEquals(array("id" => 3, "name" => "The Time Machine"),
                json_decode($response, true));
   }
}