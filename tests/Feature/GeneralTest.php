<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;

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
        $response->assertStatus(200);
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
        $response->assertStatus(200);

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
    public function testExample()
    {
        $response = $this->get('/api/query?resource=article');
        $response->assertStatus(200);
    }


}
