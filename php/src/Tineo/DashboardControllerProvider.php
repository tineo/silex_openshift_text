<?php
namespace Tineo;

use Silex\Application;
use Silex\ControllerCollection;
use Silex\ControllerProviderInterface;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;


class DashboardControllerProvider implements ControllerProviderInterface {

    public function connect(Application $app)
    {




        $controllers = $app['controllers_factory'];


        /*$controllers->before(function() use ($app){

            if(!$app['security']->isGranted('ROLE_USER')){
                $subRequest = Request::create('/login', 'GET');
                return $app->handle($subRequest, HttpKernelInterface::SUB_REQUEST);
            }

        });*/


        /*$app->post('/dashboard_login_check', function() use ($app) {
            return $app['twig']->render('index.twig', array(1           ));
        })->bind('dashboard_login_check');
        */


        $controllers->get('/',function (Application $app){



            return $app['twig']->render('index.twig', array('token' => $app['security']->getToken()));
        });

        return $controllers;


    }
}