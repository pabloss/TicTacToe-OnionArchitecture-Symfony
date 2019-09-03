<?php
declare(strict_types=1);

namespace App\Tests\integration\Controller;

use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class HomeControllerTest extends WebTestCase
{
    /** @var RemoteWebDriver  */
    private $driver;

    protected function setUp()
    {
        $host = 'http://selenium-tests:4444/wd/hub';
        $this->driver = RemoteWebDriver::create($host, DesiredCapabilities::chrome());
    }

    /**
     * @test
     */
    public function displayBoardBasedOnDatabaseContent()
    {
        $result =
            \json_decode(
                $this->driver
                    ->get('http://webserver/api/game')
                    ->findElement(
                        WebDriverBy::tagName('pre'))
                    ->getText(),
                true
            );

        $this->driver
            ->get('http://webserver/game/')
            ->wait(5)
            ->until(
                WebDriverExpectedCondition::elementTextContains(WebDriverBy::tagName('a'), 'X')
            )
        ;

        foreach ($result as $boardIndex => $playeSymbol){
            self::assertEquals(
                    $playeSymbol,
                    $this->driver
                    ->findElement(WebDriverBy::cssSelector("a#tile_".(($boardIndex - ($boardIndex % 3)) / 3) . '_' . ($boardIndex % 3)))
                    ->getText()
            );
        }
    }

    protected function tearDown()
    {
        $this->driver->close();
    }
}
