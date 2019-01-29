<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Mockery;
use App\Event;

//Past below code in Command Line to get Debugging in CLI
//export XDEBUG_CONFIG="idekey=VSCODE" 

class GeneralTest extends TestCase
{
    use RefreshDatabase;


    /**
     * A basic test example.
     *
     * @return void
     */
    public function testSaveArticle()
    {
        $response = $this->withHeaders([
            'X-Header' => 'Value',
            ])->json('POST', '/api/query?resource=article',
                ['title' => 'New Article Post','body'=>'This is the body of the article']);
        $response = null;
        $response = $this->get('/api/query?resource=article');
        $data = $response->baseResponse->getContent();
        $data = json_decode($data, TRUE);
        $this->assertEquals(count($data),1);
    }

    public function testSaveVenue()
    {
        $response = $this->withHeaders([
            'X-Header' => 'Value',
            ])->json('POST', '/api/query?resource=venue',
                [
                    'name' => 'The Derby East',
                    'address'=>'6224 E Livingston Ave',
                    'city'=>'Reynoldsburg',
                    'state'=>'OH',
                    'zip'=>'43068',
                    'latitude'=>39.942440,
                    'longitude'=>-82.828950
                ]);
        $response = null;
        $response = $this->get('/api/query?resource=venue');
        $data = $response->baseResponse->getContent();
        $data = json_decode($data, TRUE);
        $this->assertEquals(count($data),1);
    }


    /**
     * A basic test example.
     *
     * @return void
     */
    public function testApiCallToArticle()
    {
        $response = $this->get('/api/query?resource=article');
        $response->assertStatus(200);
    }

    public function testHaversineGreatCircle() {
        $event = new Event();
        //Users Location
        $from_lat= 39.946042;
        $from_long = -82.811131;
        //Event Location
        $to_lat = 39.942372;
        $to_long = -82.828873;

        $distance = $event->distance($from_lat,$from_long,$to_lat,$to_long,$event::MILES);
        $distance = round($distance, 2);
        $this->assertEquals($distance,0.97);
    }

    public function testCheckInApiNotFarEnought() {
        //Users Location
        $from_lat= 39.946042;
        $from_long = -82.811131;
        //Event Location
        $to_lat = 39.942372;
        $to_long = -82.828873;
        $user_id = 1;
        $queryString = "&from_lat=$from_lat&from_long=$from_long&to_lat=$to_lat&to_long=$to_long";
        $queryString .= "&user_id=$user_id&event_id=1";
        $response = $this->get("/api/query?resource=event&action=check-in$queryString");
        $response = $response->decodeResponseJson();
        $this->assertEquals($response['data']['type'],'checkin');
    }

    public function testCheckInApiCloseEnough() {
        $user = factory(\App\User::class)->create();
        //Users Location
        $from_lat= 39.942504;
        $from_long = -82.827660;
        //Event Location
        $to_lat = 39.942454;
        $to_long = -82.827928;
        $user_id = 1;
        $queryString = "&from_lat=$from_lat&from_long=$from_long&to_lat=$to_lat&to_long=$to_long";
        $queryString .= "&user_id=$user_id&event_id=1";
        $response = $this->put("/api/query?resource=event&action=check-in$queryString");
        $u = \App\User::find(1);
        $this->assertEquals(1,$u->event_check_in_id);
    }

    public function testCreateComment() {
        Storage::fake('avatars');
        $file = UploadedFile::fake()->image('image.jpg');

        $response = $this->withHeaders([
            'X-Header' => 'Value',
            ])->json('POST', '/api/query?resource=comment',
                [
                    'user_id' => 1,
                    'message'=>'This place is poppin!',
                    'attachment_file' => $file,
                ]);
        $response = $this->get('/api/query?resource=comment');
        $data = $response->baseResponse->getContent();
        $data = json_decode($data, TRUE);
        $this->assertEquals(count($data),1);
    }

    public function testGetCommentsForEvents() {
        $response = $this->get("/api/query?resource=comment&action=get-event-comments&event_id=1");
        $this->assertEquals(TRUE,TRUE);
    }
}
