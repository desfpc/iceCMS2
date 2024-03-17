<?php

declare(strict_types=1);

/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * Elastic class
 */

namespace iceCMS2\Search;

use CurlHandle;
use iceCMS2\Settings\Settings;

final class Elastic implements SearchInterface
{
    /**
     * $params['index'] = 'books/_search'
     * $params['method'] = 'GET'
     *
     * @param Settings $settings
     * @param array $params
     *
     * @return string
     */
    public function search(Settings $settings, array $params): string
    {
        $ch = $this->curlStart($settings, $params['method'], $params['index']);
        return $this->curlEnd($ch);
    }

    /**
     * $params['index'] = 'books/_search'
     * $params['method'] = 'GET'
     * $params['data'] = ["name" => "brave"];
     *
     * @param Settings $settings
     * @param array $params
     *
     * @return string
     */
    public function searchByQuery(Settings $settings, array $params): string
    {
        $data = [
            'query' => [
                'match'=> $params['data']
            ]
        ];

        $ch = $this->curlStart($settings, $params['method'], $params['index']);

        curl_setopt($ch, CURLOPT_POSTFIELDS,  json_encode($data));
        return $this->curlEnd($ch);
    }

    /**
     * https://www.elastic.co/guide/en/elasticsearch/reference/current/sql-rest-format.html
     *
     * $params['index'] = '_sql'
     * $params['method'] = 'POST'
     * $params['sql'] = 'SELECT * FROM books';
     * $params['params'] = '&format=json';
     *
     * @param Settings $settings
     * @param array $params
     *
     * @return string
     */
    public function searchSql(Settings $settings, array $params): string
    {
        $data['query'] =  $params['sql'] ;

        $ch = $this->curlStart($settings, $params['method'], $params['index'], $params['params']);
        curl_setopt($ch, CURLOPT_POSTFIELDS,  json_encode($data));
        return $this->curlEnd($ch);
    }

    /**
     * $params['index'] = 'books/_doc';
     * $params['method'] = 'POST';
     * $params['data'] = ['name' => 'Snow Crash1','author' => 'Neal1 Stephenson','release_date' => '2024-06-01','page_count' => 71];
     *
     * @param Settings $settings
     * @param array $params
     *
     * @return string
     */
    public function create(Settings $settings, array $params): string
    {
        $dataString = json_encode($params['data']);

        $ch = $this->curlStart($settings, $params['method'], $params['index']);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
        return $this->curlEnd($ch);
    }

    /**
     * $params['index'] = '_bulk';
     * $params['method'] = 'POST';
     * $params['data'] = ['index' => ['_index' => 'books']],
     * ['name' => 'Revelation Space', 'author' => 'Alastair Reynolds', 'release_date' => '2000-03-15', 'page_count' => 585],
     * ['index' => ['_index' => 'books']],
     * ['name' => '1984', 'author' => 'George Orwell', 'release_date' => '1985-06-01', 'page_count' => 328],
     * ['index' => ['_index' => 'books']],
     *
     * @param Settings $settings
     * @param array $params
     *
     * @return string
     */
    public function createMultiple(Settings $settings, array $params): string
    {
        $dataString = '';
        foreach ($params['data'] as $item) {
            $dataString .= json_encode($item) . "\n";
        }

        $ch = $this->curlStart($settings, $params['method'], $params['index']);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
        return $this->curlEnd($ch);
    }

    /**
     * https://www.elastic.co/guide/en/elasticsearch/reference/8.12/docs-update.html
     *
     * $params['index'] = 'books/_update/MpT1Jo4Bu_PvgoUh6NvN';
     * $params['method'] = 'POST';
     * $params['data'] = ['script' => ['source' => 'ctx._source.name = params.name','lang' => 'painless','params' => ['name' => 'test',]]];
     *
     * _id = MpT1Jo4Bu_PvgoUh6NvN
     * field update = name
     *
     * @param Settings $settings
     * @param array $params
     *
     * @return string
     */
    public function update(Settings $settings, array $params): string
    {
        $ch = $this->curlStart($settings, $params['method'], $params['index']);
        curl_setopt($ch, CURLOPT_POSTFIELDS,  json_encode($params['data']));
        return $this->curlEnd($ch);
    }

    /**
     * delete index
     *
     * $params['index'] = 'books';
     * $params['method'] = 'DELETE';
     *
     * @param Settings $settings
     * @param array $params
     *
     * @return string
     */
    public function delete(Settings $settings, array $params): string
    {
        $ch = $this->curlStart($settings, $params['method'], $params['index']);
        return $this->curlEnd($ch);
    }

    /**
     * https://www.elastic.co/guide/en/elasticsearch/reference/current/indices-clearcache.html
     * _cache/clear OR my-index-000001,my-index-000002/_cache
     *
     * $params['index'] = '_cache/clear';
     * $params['method'] = 'POST';
     *
     * @param Settings $settings
     * @param array $params
     *
     * @return string
     */
    public function clearCache(Settings $settings, array $params): string
    {
        $ch = $this->curlStart($settings, $params['method'], $params['index']);
        return $this->curlEnd($ch);
    }

    /**
     * @param Settings $settings
     * @param string $method
     * @param string $index
     * @param string|null $params
     *
     * @return CurlHandle|false
     */
    public function curlStart(Settings $settings, string $method, string $index, ?string $params = null): CurlHandle|false
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'http://elastic-icecms:9200/'.$index.'?pretty'.$params);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_USERPWD, $settings->search->login.':'.$settings->search->password);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        return $ch;
    }

    /**
     * @param $ch
     *
     * @return bool|string
     */
    public function curlEnd($ch): bool|string
    {
        $response = curl_exec($ch);

        if($response === false){
            return 'Ошибка CURL: ' . curl_error($ch);
        }
        curl_close($ch);

        if($response){
            return $response;
        }

        return 'что-то пошло ни так';
    }
}