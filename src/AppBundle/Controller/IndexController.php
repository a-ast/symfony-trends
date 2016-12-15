<?php


namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * Index controller.
 *
 * @Route("/")
 */
class IndexController extends Controller
{
    /**
     * Lists all contributor entities.
     *
     * @Route("/", name="index")
     * @Method("GET")
     */
    public function indexAction()
    {
        return $this->render('default/index.html.twig', []);
    }

    /**
     * Show trends page
     *
     * @Route("/trends", name="trends_index")
     *
     * @Method("GET")
     */
    public function trendsAction()
    {
        $content = file_get_contents(__DIR__.'/../../../web/trends/index.html');

        $content = str_replace('<link href="bootstrap/', '<link href="/trends/bootstrap/', $content);
        $content = str_replace('<link href="css/', '<link href="/trends/css/', $content);
        $content = str_replace('<script src="js/', '<script src="/trends/js/', $content);
        $content = str_replace('getJSON(\'data/', 'getJSON(\'/trends/data/', $content);

        return new Response($content);
    }
}
