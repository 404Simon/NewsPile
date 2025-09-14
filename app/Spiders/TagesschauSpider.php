<?php

namespace App\Spiders;

use Generator;
use RoachPHP\Downloader\Middleware\RequestDeduplicationMiddleware;
use RoachPHP\Downloader\Middleware\UserAgentMiddleware;
use RoachPHP\Extensions\LoggerExtension;
use RoachPHP\Extensions\StatsCollectorExtension;
use RoachPHP\Http\Response;
use RoachPHP\Spider\BasicSpider;
use RoachPHP\Spider\ParseResult;

class TagesschauSpider extends BasicSpider
{
    public array $startUrls = [
        'https://www.tagesschau.de/inland/innenpolitik/wuest-bericht-aus-berlin-kommunalwahl-100.html',
    ];

    public array $downloaderMiddleware = [
        RequestDeduplicationMiddleware::class,
        [
            UserAgentMiddleware::class,
            [
                'userAgent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.3',
            ],
        ],
    ];

    public array $spiderMiddleware = [
        //
    ];

    public array $itemProcessors = [
        //
    ];

    public array $extensions = [
        LoggerExtension::class,
        StatsCollectorExtension::class,
    ];

    public int $concurrency = 2;

    public int $requestDelay = 1;

    /**
     * @return Generator<ParseResult>
     */
    public function parse(Response $response): Generator
    {
        $title = $response->filter('h1')->text();
        // $text = $response->filter('p')->text();

        $paragraphs = $response->filter('article p[class*="textabsatz"]')->each(function ($node) {
            return $node->text();
        });

        // Join paragraphs with line breaks
        $text = implode("\n", $paragraphs);

        yield $this->item([
            'title' => $title,
            'text' => $text,
        ]);
    }
}
