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
    const RESPONSE_SECONDS_DELAY = 5;

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

    protected function tearDown()
    {
        $this->driver->close();
    }
}
