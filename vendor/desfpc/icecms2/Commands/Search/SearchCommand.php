<?php
declare(strict_types=1);
/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * Elastic command class
 */

namespace iceCMS2\Commands\Search;

use Elastic\Elasticsearch\ClientBuilder;
use Exception;
use iceCMS2\Commands\CommandInterface;
use iceCMS2\Search\SearchFactory;
use iceCMS2\Settings\Settings;
use GuzzleHttp\Client as GuzzleClient;

final class SearchCommand implements CommandInterface
{
    /** @var string */
    public string $info = 'test - test';

    /**
     * @throws Exception
     */
    public static function run(Settings $settings, ?array $param = null): string
    {
        $client = ClientBuilder::create()
            ->setHttpClient(new GuzzleClient(['verify'=>false,
                    'http_errors'=>false])
            )
            ->setHosts(['elastic-icecms:9200'])
            ->setRetries(1)
            ->setBasicAuthentication('elastic', 'MyPw123t')
            ->build();

//        $client = ClientBuilder::create()
//            ->setHosts(['elastic-icecms:9200'])
//            ->setBasicAuthentication('elastic', 'MyPw123t')
//           // ->setCABundle('path/to/http_ca.crt')
//            ->build();
//->build();
//        $params = [
//            'index' => 'some_index',
//            'type' => 'the_type',
//            'id' => 13,
//            'client' => [
//                'ignore' => 404
//            ],
//            'parent' => 1346,
//        ];
//        $response = $client->get($params);

//        $client = ClientBuilder::create()
//            ->setHosts(['elastic-icecms:9200'])
//            ->setBasicAuthentication('elastic', 'MyPw123t')
////            ->setCABundle('path/to/http_ca.crt')
//            ->build();
        $response = $client->info();
//
//        $indexParams = [
//            'index' => 'users' // Указываем имя индекса
//        ];
//        var_dump(11);
//        $response = $client->indices()->create($indexParams);


        print_r($response->asArray());
//        $class = SearchFactory::instance($settings);
//        $class->search($settings, [123]);
//
        return "123";
    }
}