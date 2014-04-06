<?php
$app = require __DIR__.'/app.php';

#Seguridad al Backend
$app->register(new Silex\Provider\SessionServiceProvider());
$app->register(new Silex\Provider\SecurityServiceProvider());
$app['security.firewalls'] = array(
    'dashboard' => array(
        'pattern' => '^/dashboard',
        //'http' => true,
        'form' => array('login_path' => '/login', 'check_path' => '/dashboard/login_check'),
        'logout' => array('logout_path' => '/dashboard/logout'),
        'users' => $app->share(function () use ($app) {
                return new Tineo\UserProvider($app);
          }),
        //'users' => array(
        //   'admin' => array('ROLE_ADMIN', '5FZ2Z8QIkA7UTZ4BYkoC+GsReLf569mSKDsfods6LYQ8t+a8EW9oaircfMpmaLbPBh4FOBiiFyLfuZmTSUwzZg=='),
        //),
    ),
);


require __DIR__.'/../config/dev.php';


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;

$app['monolog.logfile'] = __DIR__.'/../logs/backend.log';
$app['twig.path']  = array(__DIR__.'/../app/dashboard');

$app->boot();
#Rewrite basepath
//$app['asset_path'] = $app->share(function () use ($app) {
//    return $app['asset_path'].'/backend.php';
//});

$app->get('/', function(Request $request) use ($app) {
    return $app->redirect($request->getBasePath().'/backend.php/dashboard');
});

$app->get('/login', function(Request $request) use ($app) {

    if (null !== $app['security']->getToken()) {
        $subRequest = Request::create('/dashboard', 'GET');
        return $app->handle($subRequest, HttpKernelInterface::SUB_REQUEST);
    }
    return $app['twig']->render('login.twig', array(
        'error'         => $app['security.last_error']($request),
        'last_username' => $app['session']->get('_security.last_username'),
    ));
});

$app->get('/install', function() use ($app) {

    $user = new Tineo\Entity\User;
    $user->setUsername('ubuntu');
    //$user->setPassword('5FZ2Z8QIkA7UTZ4BYkoC+GsReLf569mSKDsfods6LYQ8t+a8EW9oaircfMpmaLbPBh4FOBiiFyLfuZmTSUwzZg==');
    $encoder = $app['security.encoder_factory']->getEncoder($user);
    $password = $encoder->encodePassword('kokoro', $user->getSalt());
    $user->setPassword($password);
    $user->setRoles(array('ROLE_ADMIN','ROlE_USER'));
    //$app['orm.em']->persist($user);


    $metadata = array(
        "images" => array(
            array("title" => "Kiko-san. Oh yeah!",
                "url" => "http://24.media.tumblr.com/tumblr_m53gky8x271qckx5qo1_1280.jpg"
            ),
        ),
        "tags" => array("tineo","cat","php")
    );

    $hello_world = new Tineo\Entity\Entry();
    $hello_world->setFormat('standard')
    ->setMetadata($metadata)
    ->setTitle("Hola mundoef")
    ->setContent("Hola mundo, mi primer post")
    ->setAuthorId(1)
    ->setCategoryId(1)
    ->setSlug("hello_world".time())
    ->setDatePublished(new \DateTime("now"))
    ->setDateCreated(new \DateTime("now"));

    $app['orm.em']->persist($hello_world);

    $metadata2 = array(
        "images" => array(
            array("title" => "Call me!",
                "url" => "http://31.media.tumblr.com/d5a3ef6e7a3f96435f1eff7798cfd21c/tumblr_myebtqt8cd1rcsr0fo1_500.jpg"
            ),
            array("title" => "Kiko-san. Oh yeah!",
                "url" => "http://www.soompi.com/wp-content/uploads/8/5/6z/325484/325484.jpg"
            ),
        ),
        "tags" => array("tineo","cat","php")
    );

    $second = new Tineo\Entity\Entry();
    $second->setFormat('image')
    ->setMetadata($metadata2)
    ->setTitle("Hola mundo")
    ->setContent(htmlspecialchars("<b>alert(1);</b>"))
    ->setAuthorId(1)
    ->setCategoryId(1)
    ->setSlug("hello_worldads".time())
    ->setDatePublished(new \DateTime("now"))
    ->setDateCreated(new \DateTime("now"));

    $app['orm.em']->persist($second);

    $app['orm.em']->flush();

    return $app['twig']->render('install.twig', array('token' => 1));
});

$app->mount('/dashboard', new Tineo\DashboardControllerProvider());

return $app;