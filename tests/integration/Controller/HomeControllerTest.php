<?php
declare(strict_types=1);

namespace App\Tests\integration\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

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
        for ($i = 1; $i <= 9; $i++) {
//            $link = $crawler->filter("div.tile a")->eq(1)->link();
//            $client->click($link);
            \var_dump($crawler->filter("div.tile")->attr('style'));
        }
    }
}
