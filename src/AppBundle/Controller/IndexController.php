<?php


namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Response;

class IndexController
{
    public function indexAction()
    {
        $content = file_get_contents(__DIR__.'/../../../web/trends/index.html');

        $content = str_replace('<link href="bootstrap/', '<link href="/trends/bootstrap/', $content);
        $content = str_replace('<link href="css/', '<link href="/trends/css/', $content);
        $content = str_replace('<script src="js/', '<script src="/trends/js/', $content);
        $content = str_replace('getJSON(\'data/', 'getJSON(\'/trends/data/', $content);

        return new Response($content);
    }
}
