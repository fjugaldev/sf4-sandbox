<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class DefaultController
 * @package App\Controller
 */
class DefaultController extends Controller {

    /**
     * @Route(path="/{_locale}/hello/{place}", name="default")
     * @param Request $request
     * @param $place
     * @return Response
     */
    public function indexAction(Request $request, $place)
    {
        return $this->render('default.html.twig', ['place' => $place]);
    }
}