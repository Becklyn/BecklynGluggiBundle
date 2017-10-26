<?php

namespace Tests\Becklyn\GluggiBundle\Assets;

use Becklyn\GluggiBundle\Assets\LayoutAssets;
use Becklyn\GluggiBundle\Configuration\GluggiConfig;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Asset\Packages;


class LayoutAssetsTest extends TestCase
{
    public function buildInstance (array $cssFiles = [], array $jsFiles = [])
    {
        $packageAssets = self::getMockBuilder(Packages::class)
            ->disableOriginalConstructor()
            ->getMock();

        $packageAssets
            ->method("getUrl")
            ->willReturnCallback(
                function ($param)
                {
                    return ":intern:/{$param}";
                }
            );

        $config = self::getMockBuilder(GluggiConfig::class)
            ->disableOriginalConstructor()
            ->getMock();

        $config
            ->method("getCssFiles")
            ->willReturn($cssFiles);

        $config
            ->method("getJavaScriptFiles")
            ->willReturn($jsFiles);

        return new LayoutAssets($config, $packageAssets);
    }



    public function dataProviderJavaScript ()
    {
        return [
            ["a.js", ":intern:/bundles/layout/js/a.js"],
            ["/a/a.js", ":intern:/a/a.js"],
            ["http://a/a.js", "http://a/a.js"],
            ["https://a/a.js", "https://a/a.js"],
            ["a/b.js", ":intern:/bundles/layout/js/a/b.js"],
            ["http:/a/a.js", ":intern:/bundles/layout/js/http:/a/a.js"],
        ];
    }



    public function dataProviderCSS ()
    {
        return [
            ["a.css", ":intern:/bundles/layout/css/a.css"],
            ["/a/a.css", ":intern:/a/a.css"],
            ["http://a/a.css", "http://a/a.css"],
            ["https://a/a.css", "https://a/a.css"],
            ["a/b.css", ":intern:/bundles/layout/css/a/b.css"],
            ["http:/a/a.css", ":intern:/bundles/layout/css/http:/a/a.css"],
        ];
    }



    /**
     * @dataProvider dataProviderJavaScript
     */
    public function testJavaScripts ($path, $expected)
    {
        $assets = $this->buildInstance([], [$path]);

        self::assertSame($assets->getJavaScriptUrls()[0], $expected);
    }



    /**
     * @dataProvider dataProviderCSS
     */
    public function testCSS ($path, $expected)
    {
        $assets = $this->buildInstance([$path], []);

        self::assertSame($assets->getCssUrls()[0], $expected);
    }
}
