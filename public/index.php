<?php

use App\Url;
use App\UrlsRepository;
use DI\Container;
use Slim\Factory\AppFactory;
use Slim\Middleware\MethodOverrideMiddleware;

require __DIR__.'/../vendor/autoload.php';

session_start();

$dotenv = Dotenv\Dotenv::createUnsafeImmutable(dirname(__DIR__));
$dotenv->safeLoad();

$container = new Container();
$container->set('renderer', function () {
    return new Slim\Views\PhpRenderer(__DIR__.'/../templates');
});

$container->set('flash', function () {
    return new Slim\Flash\Messages();
});

$container->set(\PDO::class, function () {
    $databaseUrl = parse_url(getenv('DATABASE_URL') ?: '');

    $dsn = sprintf(
            "pgsql:host=%s;port=%d;dbname=%s;user=%s;password=%s",
            $databaseUrl['host'],
            $databaseUrl['port'],
            ltrim($databaseUrl['path'], '/'),
            $databaseUrl['user'],
            $databaseUrl['pass']
    );

    $conn = new PDO($dsn);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    return $conn;
});

$initFilePath = implode('/', [dirname(__DIR__), 'init.sql']);
$initSql = file_get_contents($initFilePath);
$container->get(\PDO::class)->exec($initSql);

$app = AppFactory::createFromContainer($container);
$app->add(MethodOverrideMiddleware::class);
$app->addErrorMiddleware(true, true, true);

$router = $app->getRouteCollector()->getRouteParser();

$repo = $container->get(UrlsRepository::class);

$app->get('/', function ($request, $response) {

    dump($response->getBody()->getMetadata());
    $params = [
            'url' => new Url(),
            'errors' => []
    ];
    return $this->get('renderer')->render($response, 'index.phtml', $params);
});

$app->get('/urls', function ($request, $response) use ($repo) {
    $flash = $this->get('flash')->getMessages();

    $params = [
            'flash' => $flash,
            'urls' => $repo->getEntities()
    ];
    return $this->get('renderer')->render(
            $response,
            'urls/index.phtml',
            $params
    );
});

$app->get('/urls/{id}', function ($request, $response, $args) use ($repo) {
    $flash = $this->get('flash')->getMessages();
    $id = (int)$args['id'];
    $url = $repo->find($id);

    if (!$url) {
        throw new Slim\Exception\HttpNotFoundException($request);
    }

    $params = [
            'flash' => $flash,
            'url' => $url
    ];

    return $this->get('renderer')->render(
            $response,
            'urls/show.phtml',
            $params
    );
})->setName('urls');

$app->post('/urls', function ($request, $response) use ($router, $repo) {
    $urlData = $request->getParsedBodyParam('url');
    $validator = new App\Validator();
    $errors = $validator->validate($urlData);

    if (count($errors) === 0) {
        [$url] = array_values(
                array_filter(
                        $repo->getEntities(),
                        fn(Url $url) => $url->getName() === $urlData['name']
                )
        );

        if ($url) {
            $this->get('flash')->addMessage(
                    'success',
                    'Страница уже существует'
            );
        } else {
            $url = Url::fromArray($urlData);
            $repo->create($url);
            $this->get('flash')->addMessage(
                    'success',
                    'Страница успешно добавлена'
            );
        }

        return $response->withRedirect(
                $router->urlFor('urls', ['id' => $url->getId()])
        );
    }

    $params = [
            'url' => Url::fromArray($urlData),
            'errors' => $errors
    ];

    return $this->get('renderer')->render(
            $response->withStatus(422),
            'index.phtml',
            $params
    );
});

$app->post('/urls/{url_id}/checks ', function ($request, $response, $args) use ($router, $repo) {
    $id = (int)$args['url_id'];
    $urlData = $request->getParsedBodyParam('url');
    $validator = new App\Validator();
    $errors = $validator->validate($urlData);

    if (count($errors) === 0) {
        [$url] = array_values(
                array_filter(
                        $repo->getEntities(),
                        fn(Url $url) => $url->getName() === $urlData['name']
                )
        );

        if ($url) {
            $this->get('flash')->addMessage(
                    'success',
                    'Страница уже существует'
            );
        } else {
            $url = Url::fromArray($urlData);
            $repo->create($url);
            $this->get('flash')->addMessage(
                    'success',
                    'Страница успешно добавлена'
            );
        }

        return $response->withRedirect(
                $router->urlFor('urls', ['id' => $url->getId()])
        );
    }

    $params = [
            'url' => Url::fromArray($urlData),
            'errors' => $errors
    ];

    return $this->get('renderer')->render(
            $response->withStatus(422),
            'index.phtml',
            $params
    );
});

$app->run();
