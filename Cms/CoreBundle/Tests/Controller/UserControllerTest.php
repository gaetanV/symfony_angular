<?php

namespace CmsCoreBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class UserControllerTest extends WebTestCase
{
     private $client = null;
     
  public function setUp()
    {
        $this->client = static::createClient();
     //   $this->app = new \AppKernel('test', false);
    //   $this->app->boot();
    }
    
    
      private function logIn()
    {
        $session = $this->client->getContainer()->get('session');

        $firewall = 'secured_area';
        $token = new UsernamePasswordToken('admin', null, $firewall, array('ROLE_ADMIN'));
     
        $session->set('_security_'.$firewall, serialize($token));
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $this->client->getCookieJar()->set($cookie);
    }
    
    
      public function testGetUser(){
           $this->client->request('GET', '/fr/admin/user/1/');
             $response = $this->client->getResponse();
          $this->assertJsonResponse($response);
      }
      
      
      public function testGetUsers(){
            $this->client->request('GET', '/fr/admin/user/');
              $response = $this->client->getResponse();
            $this->assertJsonResponse($response);
      }
      
      
    public function testAddUser(){
          $this->logIn();
          /*
            $request = new Request('/fr/admin/user/create', 'POST', array(
            'username' => 'igor@example.com',
            'password' => 'Hello',
        ));
           
            
            
            
 $response = $this->app->handle($request);
            */
          
          
            $response=$this->client->request( 'POST','/fr/admin/user/create', array(
               // "form[username]"=>""
                "form"=>array(
                    "username"=>'test@free.Fr',
                     'password' =>array(
                         "first"=>'dd',
                         "second"=>'dd'
                     )
                )
                )
              );

           var_dump($response);
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
