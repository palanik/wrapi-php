<?php
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;

class wrapiDefaultTest extends \PHPUnit_Framework_TestCase {
	protected static $client;

    public static function setUpBeforeClass() {
		$mock = new MockHandler([
		    new Response(200, [], json_encode([
                array("id" => 1, "name" => "The Martian"),
                array("id" => 2, "name" => "The Odyssey")
                ])
            ),
            new Response(200, [], json_encode(
                array("id" => 2, "name" => "The Odyssey")
                )
            )

		]);

		$handler = HandlerStack::create($mock);

        $client = new wrapi\wrapi('http://api.a2zbooks.local/v1/', [], [], ['handler' => $handler]);
        $client("list", array("method" => "GET", "path" => "books"));
        $client("item", array("path" => "books/:id"));  // Use Default method

        self::$client = $client;
    }
    public static function tearDownAfterClass() {
    }

    public function testListRequest() {
        $response = self::$client->list();
        $this->assertNotNull($response);
        $this->assertEquals([
                array("id" => 1, "name" => "The Martian"),
                array("id" => 2, "name" => "The Odyssey")
                ],
                json_decode($response, true));
    }

    public function testItemRequest() {
        $response = self::$client->item(2);
        $this->assertNotNull($response);
        $this->assertEquals(array("id" => 2, "name" => "The Odyssey"),
                json_decode($response, true));
   }
}