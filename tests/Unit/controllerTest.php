<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Http\Controllers\StreetViewController;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;

class controllerTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    // public function setUp()
    // {
    //     parent::setUp();
    // }
    public function testExample()
    {
        $this->assertTrue(true);
    }

    public function testAPIs()
    {
    	//checking "/streetview/get/street/{geo_id}"
    	$this->get('/api/streetview/get/street/242')
             ->assertJson([
                    "name"=> "Banani Rd No 11"
            ]);

        //checking "/streetview/get/street/all"
        $response = $this->json('GET','/api/streetview/get/street/all');

        $response
            ->assertStatus(200)
            ->assertJson([
            	"0" => [
                	"name"=> "Banani Rd No 11",
                ]
            ]);

        //checking "/geo/search"
        $this->post('/api/geo/search',[
        		'latitude' => 23.78747764,
        		'longitude' => 90.40063136])
        	->assertJson([
        		"id" => 514,
        	])
        	->assertExactJson([
        		"id" => 514,
			    "road_id" => 376,
			    "latitude" => 23.78747764,
			    "longitude" => 90.40063136
        	])
        	->assertJsonStructure([
        		"id",
			    "road_id",
			    "latitude",
			    "longitude"
        	]);

        //checking "/streetview/update"
        $this->post('api/streetview/update',[
        		'id' => 717,
        		'imageLink' => file('https://images.pexels.com/photos/1000084/pexels-photo-1000084.jpeg?auto=compress&cs=tinysrgb&dpr=2&h=650&w=940')
        	])
        	->assertJson([
        		'Message' => 'Updated'
        	]);
    }
    public function testDB()
    {
    	$this->assertDatabaseHas('streetviews', ['id' => '10' ]);
    }

    // public function testController()
    // {
    // 	$file = "https://images.pexels.com/photos/1000084/pexels-photo-1000084.jpeg";
    // 	$geo_id = 123;
    // 	StreetViewController::saveFile($file,$geo_id);
    // }

    // public function testInsertData()
    // {
    // 	$file = UploadedFile::fake()->image('/var/www/html/streetviewSearch/storage/app/public/streetview/227/00001.jpg')
    // 	$data = [
    //                     'latitude' => [94.545645],
    //                     'longitude' => [23.5644],
    //                     'geometry_id' => [123],
    //                     'road_name' => ["atoz"],
    //                     'imageLink' => [image("/var/www/html/streetviewSearch/storage/app/public/streetview/227/00001.jpg")]
    // 					// 'latitude[0]' => 94.545645,
    //      //                'longitude[0]' => 23.5644,
    //      //                'geometry_id[0]' => 123,
    //      //                'road_name[0]' => "atoz",
    //      //                'imageLink[0]' => "https://images.pexels.com/photos/1000084/pexels-photo-1000084.jpeg?auto=compress&cs=tinysrgb&dpr=2&h=650&w=940"
    //                            ];

    //         $response = $this->json('POST', '/api/streetview',$data);
    //         $response->assertStatus(500);
    //         $response->assertJson(['Message' => 'Inserted']);
    // }

    // public function tearDown()
    // {
    //     parent::tearDown();
    // }
}
