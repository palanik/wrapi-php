<?php

class wrapiNestedRegisterTest extends \PHPUnit_Framework_TestCase {
    use \InterNations\Component\HttpMock\PHPUnit\HttpMockTrait;

    protected static $client;

    public static function setUpBeforeClass() {
        static::setUpHttpMockBeforeClass('8082', 'localhost');

        $client = new wrapi\wrapi('http://localhost:8082/v1/');
        $client("books", array("method" => "GET", "path" => "books"));
        $client("books.list", array("method" => "GET", "path" => "books"));
        $client("books.item", array("method" => "GET", "path" => "books/:id"));
        $client("books.item.get", array("method" => "GET", "path" => "books/:id"));
        $client("books.item.create", array("method" => "POST", "path" => "books"));
        $client("books.item.update", array("method" => "PUT", "path" => "books/:id"));
        $client("books.item.remove", array("method" => "DELETE", "path" => "books/:id"));

        self::$client = $client;
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

    public function testBooksRequest() {
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

        $response = self::$client->books();
        $this->assertNotNull($response);
        $this->assertEquals([
                array("id" => 1, "name" => "The Martian"),
                array("id" => 2, "name" => "The Odyssey")
                ],
                json_decode($response, true));

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

    public function testItemGetRequest() {
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
