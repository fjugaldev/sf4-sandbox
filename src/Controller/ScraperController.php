<?php

namespace App\Controller;


use Goutte\Client;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ScraperController
 * @Route(path="/scraper", name="scraper")
 * @package App\Controller
 */
class ScraperController extends Controller
{
    /**
     * @Route(path="/positions/lite", methods={"GET"}, name="scraper_positions_lite")
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function getTeamsPositionTableLite(Request $request)
    {
        $client = new Client();
        $crawler = $client->request('GET', 'https://www.lvbp.com');

        $tabContentSelectors = [
            'regular_round' => '#R',
            'playoffs'      => '#D',
            'semifinal'     => '#L',
            'final'         => '#W',
        ];

        $table = [];
        foreach ($tabContentSelectors as $key => $selector) {
            global $header, $body, $rowIndex;
            $header = [];
            $body = [];
            $rowIndex = 0;

            $crawler->filter("{$selector} table.table > thead tr th")->each(function ($node) {
                global $header;
                array_push($header, $node->text());
            });

            $crawler->filter("{$selector} table.table > tbody tr")->each(function ($node) {
                global $rowIndex;
                $node->filter('td')->each(function ($item) {
                    global $body, $rowIndex;
                    $body[$rowIndex][] = trim(preg_replace('/\s+/', ' ', $item->text()));
                });
                $rowIndex++;
                //array_push($body, $node->text());
            });

            $positionsTable = [
                'header' => $header,
                'body'   => $body,
            ];

            $table[$key] = $positionsTable;
        }


        $response = [
            'status' => 200,
            'data'   => $table
        ];

        return new JsonResponse($response, $response['status']);
    }

    /**
     * @Route(path="/positions/full", methods={"GET"}, name="scraper_positions")
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function getTeamsPositionTable(Request $request)
    {
        $client = new Client();
        $crawler = $client->request('GET', 'https://www.lvbp.com/posiciones.php');

        $tabContentSelectors = [
            'regular_round'      => '#r',
            'particulars_series' => '#s',
            'playoffs'           => '#d',
            'semifinal'          => '#l',
            'final'              => '#w',
        ];

        $table = [];
        foreach ($tabContentSelectors as $key => $selector) {
            global $header, $body, $rowIndex;
            $header = [];
            $body = [];
            $rowIndex = 0;

            $crawler->filter("{$selector} table.table > thead tr th")->each(function ($node) {
                global $header;
                array_push($header, $node->text());
            });

            $crawler->filter("{$selector} table.table > tbody tr")->each(function ($node) {
                global $rowIndex;
                $node->filter('td')->each(function ($item) {
                    global $body, $rowIndex;
                    $body[$rowIndex][] = trim(preg_replace('/\s+/', ' ', $item->text()));
                });
                $rowIndex++;
                //array_push($body, $node->text());
            });

            $positionsTable = [
                'header' => $header,
                'body'   => $body,
            ];

            $table[$key] = $positionsTable;
        }


        $response = [
            'status' => 200,
            'data'   => $table
        ];

        return new JsonResponse($response, $response['status']);
    }

    /**
     * @Route(path="/leaders/batting", methods={"GET"}, name="scraper_leaders_batting")
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function getBattingLeaders(Request $request)
    {
        $client = new Client();
        $crawler = $client->request('GET', 'https://www.lvbp.com#HR');
        $link = $crawler->selectLink('HR')->link();
        dump($link->getUri());
        $client->click($link);
        dump($crawler->text());
        die();

        $tabContentSelectors = [
            'AVG' => '#AVG',
            'CA'  => '#CA',
            'HR'  => '#HR',
            'CI'  => '#CI',
            'BR'  => '#BR',
            'H'   => '#H',
        ];

        global $table;
        $table = [];
        foreach ($tabContentSelectors as $key => $selector) {
            global $key_global;
            $key_global = $key;

            dump($crawler->text());die();

            $crawler->filter("{$selector} div.home-leaders-row")->each(function ($node) {
                global $name;
                $name = trim(preg_replace('/\s+/', ' ', $node->text()));
                $node->filter('div.col-md-1')->each(function ($item) {
                    global $table, $key_global, $name;
                    $table[$key_global][] = [
                        'name'  => str_replace($item->text(), '', $name),
                        'value' => $item->text(),
                    ];
                });
            });
        }

        $response = [
            'status' => 200,
            'data'   => $table
        ];

        return new JsonResponse($response, $response['status']);
    }

    /**
     * @Route(path="/leaders/pitching", methods={"GET"}, name="scraper_leaders_pitching")
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function getPitchingLeaders(Request $request)
    {
        $client = new Client();
        $crawler = $client->request('GET', 'https://www.lvbp.com');

        $tabContentSelectors = [
            'EFE' => '#EFE',
            'JG' => '#JG',
            'JS' => '#JS',
            'IP' => '#IP',
            'K' => '#K',
            'HLD' => '#HLD',
        ];

        global $table;
        $table = [];
        foreach ($tabContentSelectors as $key => $selector) {
            global $key_global;
            $key_global = $key;

            $crawler->filter("{$selector} .list-group .list-group-item")->each(function ($node) {
                global $name;
                $name = $node->text();
                $node->filter('div')->each(function ($item) {
                    global $table, $key_global, $name;
                    $table[$key_global][] = [
                        'name'  => str_replace($item->text(), '', $name),
                        'value' => $item->text(),
                    ];
                });
            });
        }


        $response = [
            'status' => 200,
            'data' => $table
        ];

        return new JsonResponse($response, $response['status']);
    }
}
