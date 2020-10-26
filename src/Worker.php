<?php

/**
 * Worker.
 */

declare(strict_types=1);

namespace SEOCLI;

use SEOCLI\Traits\Singleton;
use SEOstats\SEOstats;

/**
 * Worker.
 */
class Worker
{
    use Singleton;

    /**
     * URIs.
     *
     * @var array
     */
    protected static $uris = [];

    /**
     * Depth.
     *
     * @var int
     */
    protected static $depth = 1;

    /**
     * Add URI.
     */
    public function add(Uri $uri): void
    {
        self::$uris[] = $uri;
    }

    /**
     * Set depth.
     */
    public function setDepth(int $depth): void
    {
        self::$depth = $depth;
    }

    /**
     * Prefetch one.
     *
     * @return bool|string
     */
    public function prefetchOne()
    {
        foreach (self::$uris as $key => $uri) {
            /** @var Uri $uri */
            if (null === $uri->getInfo()) {
                $request = new Request($uri);

                $info = (array) $request->getMeta();
                $content = $request->getContent();
                $info['crawlDepth'] = $uri->getDepth();
                $info['documentSizeInMb'] = (new Format())->megaBytes(mb_strlen($content));

                $headers = $request->getHeader();
                $info['contentType'] = isset($headers['Content-Type']) ? implode('', $headers['Content-Type']) : '';
                // $infos['content'] = $request->getContent();
                // $infos['header'] = $request->getHeader();

                // meta Description, metaDescription Length
                // meta Keywords, metakeywords Length

                // wordCount
                // textRatio

                $parser = new Parser();
                $parserResult = $parser->parseAll($uri, $request->getContent());

                $info['title'] = $parserResult['title']['text'];
                $info['titleLength'] = $parserResult['title']['length'];
                $info['links'] = \count($parserResult['links']);

                $info['wordCount'] = $parserResult['text']['wordCount'];
                $info['textRatio'] = $parserResult['text']['textRatio'];

                //define('SEOSTATSPATH', '..\\..\\..\\..\\..\\vendor\\seostats\\seostats\\SEOstats\\');
                //$seoStats = new SEOstats((string)$uri);
                //var_dump($seoStats->Sistrix()::getVisibilityIndex());
                //var_dump($seoStats->Alexa()::getWeeklyRank());
                //die();

                $robotsTxt = new RobotsTxt();
                $info['robotsTxt'] = $robotsTxt->status($uri);

                $uri->setInfo($info);

                if ($uri->getDepth() < self::$depth) {
                    $worker = self::getInstance();
                    foreach ($this->cleanupLinksForWorker($uri, $parserResult['links']) as $link) {
                        $worker->add(new Uri($link, $uri->getDepth() + 1));
                    }
                }

                return (string) $uri.' ('.$uri->getDepth().')';
            }
        }

        return false;
    }

    /**
     * Get open.
     *
     * @return array
     */
    public function getOpen()
    {
        return array_filter(self::$uris, function (Uri $uri) {
            return null === $uri->getInfo();
        });
    }

    /**
     * Get fetched.
     *
     * @return array
     */
    public function getFetched()
    {
        return array_filter(self::$uris, function (Uri $uri) {
            return null !== $uri->getInfo();
        });
    }

    /**
     * Get.
     *
     * @return array
     */
    public function get()
    {
        return self::$uris;
    }

    /**
     * Cleanup Links for worker.
     */
    protected function cleanupLinksForWorker(Uri $uri, array $links): array
    {
        $result = [];
        $alreadyQueued = array_map(function ($uri) {
            return (string) $uri;
        }, self::$uris);

        foreach ($uri->normalizeLinks($links) as $link) {
            if (\in_array($link, $alreadyQueued, true)) {
                continue;
            }
            $result[] = $link;
        }

        return array_filter($result, function ($item) {
            $extension = pathinfo($item, PATHINFO_EXTENSION);

            return !\in_array($extension, ['jpg', 'jpeg', 'bmp', 'gif', 'pdf', 'mp4', 'mp3', 'mov'], true);
        });
    }
}
