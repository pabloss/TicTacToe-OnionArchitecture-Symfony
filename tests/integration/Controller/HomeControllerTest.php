<?php
declare(strict_types=1);

namespace App\Tests\integration\Controller;

use App\AppCore\DomainModel\Game\Player\Symbol;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class HomeControllerTest extends WebTestCase
{
    const RESPONSE_SECONDS_DELAY = 5;

    /** @var RemoteWebDriver  */
    private $driver;

    protected function setUp()
    {
        $host = 'http://selenium-tests:4444/wd/hub';
        $this->driver = RemoteWebDriver::create($host, DesiredCapabilities::chrome());
        $this->driver->manage()->timeouts()->setScriptTimeout(6);
        $this->driver->getCommandExecutor()->setRequestTimeout(10*1000);

        $client = static::createClient();
        $entityManager = $client->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    /**
     * @test
     */
    public function displayResetButton()
    {
        $this->driver
            ->get('http://webserver/game/')
            ->wait(self::RESPONSE_SECONDS_DELAY)
            ->until(
                WebDriverExpectedCondition::presenceOfElementLocated(
                    WebDriverBy::cssSelector("button#reset")
                )
            )
        ;

        self::assertTrue(
            $this->driver->findElement(
                WebDriverBy::cssSelector("button#reset")
            )
            ->isDisplayed()
        );

        self::assertEquals(
            "Reset",
            $this->driver->findElement(
                WebDriverBy::cssSelector("button#reset")
            )
            ->getText()
        );

        $this->driver->findElement(
            WebDriverBy::cssSelector("button#reset")
        )
        ->click()
        ;
        $result =
            \json_decode(
                $this->driver
                    ->get('http://webserver/api/game')
                    ->findElement(
                        WebDriverBy::tagName('pre'))
                    ->getText(),
                true
            );
        self::assertFalse(\is_null($result));
        self::assertTrue(\is_array($result));
        self::assertSame(0, \count($result));


        $css_selector = "a#tile_0_1";
        $this->driver
            ->get('http://webserver/game/')
            ->wait(self::RESPONSE_SECONDS_DELAY)
            ->until(
                WebDriverExpectedCondition::presenceOfElementLocated(
                    WebDriverBy::cssSelector($css_selector)
                )
            )
        ;
        $this->driver->findElement(
            WebDriverBy::cssSelector($css_selector)
        )
            ->click()
        ;

        $this->driver
            ->get('http://webserver/game/')
            ->wait(self::RESPONSE_SECONDS_DELAY)
            ->until(
                WebDriverExpectedCondition::elementTextContains(WebDriverBy::cssSelector($css_selector), 'X')
            )
        ;

        self::assertEquals(
            'X',
            $this->driver->findElement(
                WebDriverBy::cssSelector($css_selector)
            )
            ->getText()
        );
    }

    /**
     * @test
     */
    public function completeHappyPathGameplay()
    {
        $this->driver
            ->get('http://webserver/game/')
            ->wait(self::RESPONSE_SECONDS_DELAY)
            ->until(
                WebDriverExpectedCondition::presenceOfElementLocated(
                    WebDriverBy::cssSelector("button#reset")
                )
            )
        ;

        self::assertTrue(
            $this->driver->findElement(
                WebDriverBy::cssSelector("button#reset")
            )
                ->isDisplayed()
        );

        self::assertEquals(
            "Reset",
            $this->driver->findElement(
                WebDriverBy::cssSelector("button#reset")
            )
                ->getText()
        );

        $this->driver->findElement(
            WebDriverBy::cssSelector("button#reset")
        )
            ->click()
        ;
        $result =
            \json_decode(
                $this->driver
                    ->get('http://webserver/api/game')
                    ->findElement(
                        WebDriverBy::tagName('pre'))
                    ->getText(),
                true
            );
        self::assertFalse(\is_null($result));
        self::assertTrue(\is_array($result));
        self::assertSame(0, \count($result));

        // we should see winner section
        $this->driver
            ->get('http://webserver/game/')
            ->wait(self::RESPONSE_SECONDS_DELAY)
            ->until(
                WebDriverExpectedCondition::presenceOfElementLocated(
                    WebDriverBy::cssSelector("div#winner")
                )
            )
        ;

        self::assertEquals(
            "winner",
            $this->driver
                ->get('http://webserver/game')->findElement(WebDriverBy::cssSelector("div#winner"))->getAttribute("id")
        );

        $turns = [
            [1, 1],
            [0, 0],
            [0, 1],
            [0, 2],
            [2, 1],
        ];

        foreach ($turns as $index => $turn){
                $this->driver->findElement(
                    WebDriverBy::cssSelector("a#tile_".\implode("_", $turn))
                )
                    ->click();
                $this->driver->wait(self::RESPONSE_SECONDS_DELAY)->until(
                    WebDriverExpectedCondition::elementTextContains(
                        WebDriverBy::cssSelector("a#tile_".\implode("_", $turn)),
                        [Symbol::PLAYER_X_SYMBOL, Symbol::PLAYER_0_SYMBOL][$index%2]
                    )
                );
            self::assertEquals(
                [Symbol::PLAYER_X_SYMBOL, Symbol::PLAYER_0_SYMBOL][$index%2],
                $this->driver->findElement(
                    WebDriverBy::cssSelector("a#tile_".\implode("_", $turn))
                )->getText(), "a#tile_".\implode("_", $turn));
        }

        $this->driver->wait(self::RESPONSE_SECONDS_DELAY)->until(
            WebDriverExpectedCondition::elementTextContains(
                WebDriverBy::cssSelector("div#winner"),
                Symbol::PLAYER_X_SYMBOL
            )
        );

        self::assertEquals(
            Symbol::PLAYER_X_SYMBOL,
            $this->driver->findElement(
                WebDriverBy::cssSelector("div#winner")
            )->getText(), Symbol::PLAYER_X_SYMBOL
        );
    }

    protected function tearDown()
    {
        $this->driver->close();
    }
}
