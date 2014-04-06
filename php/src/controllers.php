<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

$app->get('/', function () use ($app) {
    $query = $app["orm.em"]->createQuery("SELECT t FROM Tineo\Entity\Entry t ORDER BY t.datePublished DESC");
    $entries = $query->getArrayResult();
    return $app['twig']->render('index.twig', array('entries' => $entries ));
})
->bind('homepage')
;



$app->get('/blog', function () use ($app) {
    return $app['twig']->render('blog.twig', array('page_title' => '123'));
})
    ->bind('blog')
;

$app->get('/contact', function () use ($app) {
    return $app['twig']->render('contact.twig', array('page_title' => '123'));
})
    ->bind('contact')
;

$app->get('/{slug}', function ($slug) use ($app) {
    $query = $app["orm.em"]->createQuery("SELECT t FROM Tineo\Entity\Entry t WHERE t.slug = :slug");
    $query->setParameter(':slug', $slug);
    $entry = $query->getOneOrNullResult();

    if(count($entry) == 0)
        return new Response($app['twig']->render('404.twig', array('code' => 404)), 404);


    return $app['twig']->render('post.twig', array('entry' => $entry));
})
    ->bind('post')
;

$app->error(function (\Exception $e, $code) use ($app) {
    if ($app['debug']) {
        return;
    }

    $page = 404 == $code ? '404.twig' : '500.twig';

    return new Response($app['twig']->render($page, array('code' => $code)), $code);

});
