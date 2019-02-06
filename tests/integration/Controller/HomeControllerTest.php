<?php
declare(strict_types=1);

namespace App\Tests\integration\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class HomeControllerTest extends WebTestCase
{
    /**
     * @test
     */
    public function startGame()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/game');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals(
            9,
            $crawler->filter('div.tile')->count()
        );

        for ($i = 1; $i <= 9; $i++) {
            $this->assertEquals(
                1,
                $crawler->filter("div#tile_$i")->count()
            );
        }

        $link = $crawler->filter("div#tile_1 a")->eq(0)->link();
        $client->click($link);

        self::assertEquals('GET', $link->getMethod());
        self::assertEquals('http://localhost/game/get-tile/0,0', $link->getUri());
        self::assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());

        $link = $crawler->filter("div#tile_2 a")->eq(0)->link();
        $client->click($link);
        self::assertEquals('http://localhost/game/get-tile/0,1', $link->getUri());
        self::assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());

        $link = $crawler->filter("div#tile_1 a")->eq(0)->link();
        $client->click($link);
        self::assertEquals(Response::HTTP_CONFLICT, $client->getResponse()->getStatusCode());
    }
}
