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
     * @Route(path="/positions", methods={"GET"}, name="scraper_positions_lite")
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function getTeamsPositionTableLite(Request $request)
    {
        $client = new Client();
        $crawler = $client->request('GET', 'https://www.tigresdearaguabbc.com');

        $tabContentSelectors = [
            'regular_round' => '#pills-r',
            'playoffs' => '#pills-d',
            'semifinal' => '#pills-l',
            'final' => '#pills-w',
        ];

        $table = [];
        foreach ($tabContentSelectors as $key => $selector) {
            global $header, $body, $rowIndex;
            $header = [];
            $body = [];
            $rowIndex = 0;

            $crawler->filter("{$selector} table.table > thead.thead-dark tr th")->each(function ($node) {
                global $header;
                array_push($header, $node->text());
            });

            $crawler->filter("{$selector} table.table > tbody tr")->each(function ($node) {
                global $rowIndex;
                $node->filter('td')->each(function ($item) {
                    global $body, $rowIndex;
                    $body[$rowIndex][] = $item->text();
                });
                $rowIndex++;
                //array_push($body, $node->text());
            });

            $positionsTable = [
                'header' => $header,
                'body' => $body,
            ];

            $table[$key] = $positionsTable;
        }


        $response = [
            'status' => 200,
            'data' => $table
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
    }

    /**
     * @Route(path="/leaders/pitching", methods={"GET"}, name="scraper_leaders_pitching")
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function getPitchingLeaders(Request $request)
    {
    }
}