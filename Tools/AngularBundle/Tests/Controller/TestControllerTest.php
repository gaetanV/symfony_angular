<?php
namespace Tools\AngularBundle\Tests\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TestControllerTest extends WebTestCase
{
     private $client = null;
     
     public function setUp(){$this->client = static::createClient(); }
    
    public function testGetUser(){
         $this->client->request('GET', '/fr/angular/test');

    
           $this->assertEquals(200,  $this->client->getResponse()->getStatusCode(), "Unexpected HTTP status code for GET /fr/angular/test/");
           
      }
      
       public function testPostUser(){
            $response=$this->client->request( 'POST','/fr/angular/test', array(
         
                "form"=>array(
                    "username"=>'test@free.Fr',
                     'password' =>array(
                         "first"=>'d4d',
                         "second"=>'d4d'
                     )
                )
                )
              );
            $response = $this->client->getResponse();
            

            $this->assertJsonResponse($response);
            $response=   $response->getContent();
            
            $response = json_decode($response, true);
       
            $this->assertInternalType('array', $response);
            $this->assertNotSame(null, $response);
            $this->assertArrayHasKey('error', $response);
            $this->assertFalse($response['error']);

      }
      
        public function testPostErrorUser(){
            $response=$this->client->request( 'POST','/fr/angular/test', array(
         
                "form"=>array(
                    "username"=>'test@free.Fr',
                     'password' =>array(
                         "first"=>'dd',
                         "second"=>'d'
                     )
                )
                )
              );
            $response = $this->client->getResponse();
            

            $this->assertJsonResponse($response);
            $response=   $response->getContent();
            
            $response = json_decode($response, true);
       
            $this->assertInternalType('array', $response);
            $this->assertNotSame(null, $response);
            $this->assertArrayHasKey('error', $response);
            $this->assertTrue($response['error']);

      }
      
      
    protected function assertJsonResponse($response, $statusCode = 200) {
        $this->assertEquals(
                $statusCode, $response->getStatusCode(), "Not json"
        );
        $this->assertTrue(
                $response->headers->contains('Content-Type', 'application/json'), $response->headers
        );
    }
      
}
