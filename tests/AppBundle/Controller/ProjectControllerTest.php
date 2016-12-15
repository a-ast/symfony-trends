<?php

namespace Tests\AppBundle\Controller;

use DateInterval;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class ProjectControllerTest extends WebTestCase
{
    /**
     * @var Client
     */
    private $client = null;

    public function setUp()
    {
        $this->client = static::createClient();
    }

    public function testIndexRedirectsToLogin()
    {

        $authHeaders = ['PHP_AUTH_USER' => 'bb', 'PHP_AUTH_PW' => '111'];

        $crawler = $this->client->request('GET', '/admin/projects/', [], [], []);
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());


//        $this->assertEquals(1, $crawler->filter('h1')->count());
//        $this->assertEquals('Symfony Trends beta', $crawler->filter('h1')->text());
//
//        $chartCount = $crawler->filter('div.chart')->count();
//        $h2Count = $crawler->filter('h2')->count();
//
//        $this->assertGreaterThan(0, $chartCount);
//        $this->assertEquals($chartCount, $h2Count);
    }

    public function testIndexClickCreate()
    {
        $this->logIn('bb', ['ROLE_CHECKER']);

        $crawler = $this->client->request('GET', '/admin/projects/');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $link = $crawler->selectLink('Create a new project')->link();
        $this->client->click($link);

        $this->assertEquals(403, $this->client->getResponse()->getStatusCode());
    }

    public function testNew()
    {
        $this->logIn('aa', ['ROLE_ADMIN', 'ROLE_CHECKER']);

        $crawler = $this->client->request('GET', '/admin/projects/new');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $button = $crawler->selectButton('Create');

        $form = $button->form([
            'project[name]' => 'Project X',
            'project[label]' => 'project_x',
            'project[githubPath]' => 'project/x',
            'project[color]' => '#000000',
        ]);

        $this->client->followRedirects();
        $newCrawler = $this->client->submit($form);


        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }


    private function logIn($userName, $roles)
    {
        /** @var SessionInterface $session */
        $session = $this->client->getContainer()->get('session');

        // the firewall context (defaults to the firewall name)
        $firewall = 'main';

        $token = new UsernamePasswordToken($userName, null, $firewall, $roles);
        $session->set('_security_'.$firewall, serialize($token));
        $session->save();

        $expires = new DateTime();
        $expires->add(new DateInterval('PT2H'));
        $cookie = new Cookie($session->getName(), $session->getId(), $expires->format('U'));
        $this->client->getCookieJar()->set($cookie);
    }
}
