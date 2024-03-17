<?php

declare(strict_types=1);
/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * Search Test class
 */

namespace vendor\Search;

use iceCMS2\Search\SearchFactory;
use iceCMS2\Tests\Ice2CMSTestCase;
use Exception;

class SearchTest extends Ice2CMSTestCase
{
    /**
     * Test Logger class
     *
     * @return void
     * @throws Exception
     */
    public function testSearch(): void
    {
        // create
        $params['index'] = 'test_books/_doc';
        $params['method'] = 'POST';
        $params['data'] = [
            'name' => 'Snow Crash',
            'author' => 'Neal Stephenson',
            'release_date' => '2024-06-01',
            'page_count' => 71
        ];
        $response = SearchFactory::instance(self::$_testSettings)->create(self::$_testSettings, $params);
        $responseData = json_decode($response, true);
        self::assertArrayNotHasKey('status', $responseData);

        //createMultiple
        $params['index'] = '_bulk';
        $params['method'] = 'POST';
        $params['data'] = [
            ['index' => ['_index' => 'test_books']],
            ['name' => 'Revelation Space', 'author' => 'Alastair Reynolds', 'release_date' => '2000-03-15', 'page_count' => 585],
            ['index' => ['_index' => 'test_books']],
            ['name' => '1984', 'author' => 'George Orwell', 'release_date' => '1985-06-01', 'page_count' => 328],
            ['index' => ['_index' => 'test_books']],
            ['name' => 'Fahrenheit 451', 'author' => 'Ray Bradbury', 'release_date' => '1953-10-15', 'page_count' => 227],
            ['index' => ['_index' => 'test_books']],
            ['name' => 'Brave New World', 'author' => 'Aldous Huxley', 'release_date' => '1932-06-01', 'page_count' => 268],
            ['index' => ['_index' => 'test_books']],
            ['name' => 'The Handmaids Tale', 'author' => 'Margaret Atwood', 'release_date' => '1985-06-01', 'page_count' => 311]
        ];
        $response = SearchFactory::instance(self::$_testSettings)->createMultiple(self::$_testSettings, $params);
        $responseData = json_decode($response, true);
        self::assertArrayNotHasKey('status', $responseData);

        //update
        $params['index'] = "test_books/_update/{$responseData['items'][0]['index']['_id']}";
        $params['method'] = 'POST';
        $params['data'] = [
            'script'=> [
                'source'=> 'ctx._source.name = params.name',
                'lang'=> 'painless',
                'params'=> [
                    'name'=> 'test',
                ]
            ]
        ];
        $response = SearchFactory::instance(self::$_testSettings)->update(self::$_testSettings, $params);
        $responseData = json_decode($response, true);
        self::assertArrayNotHasKey('status', $responseData);

        //search
        $params['index'] = 'test_books/_search';
        $params['method'] = 'GET';
        $response = SearchFactory::instance(self::$_testSettings)->search(self::$_testSettings, $params);
        $responseData = json_decode($response, true);
        self::assertArrayNotHasKey('status', $responseData);

        //searchByQuery
        $params['index'] = 'test_books/_search';
        $params['method'] = 'GET';
        $params['data'] = ["name" => "brave"];
        $response = SearchFactory::instance(self::$_testSettings)->searchByQuery(self::$_testSettings, $params);
        $responseData = json_decode($response, true);
        self::assertArrayNotHasKey('status', $responseData);

        //searchSql
        $params['index'] = '_sql';
        $params['method'] = 'POST';
        $params['params'] = '&format=json';
        $params['sql'] = "SELECT * FROM test_books";
        $response = SearchFactory::instance(self::$_testSettings)->searchSql(self::$_testSettings, $params);
        $responseData = json_decode($response, true);
        self::assertArrayNotHasKey('status', $responseData);

        //delete
        $params['index'] = 'test_books';
        $params['method'] = 'DELETE';
        $response = SearchFactory::instance(self::$_testSettings)->delete(self::$_testSettings, $params);
        $responseData = json_decode($response, true);
        self::assertArrayNotHasKey('status', $responseData);

        //clearCache
        $params['index'] = '_cache/clear';
        $params['method'] = 'POST';
        $response = SearchFactory::instance(self::$_testSettings)->clearCache(self::$_testSettings, $params);
        $responseData = json_decode($response, true);
        self::assertArrayNotHasKey('status', $responseData);
    }
}