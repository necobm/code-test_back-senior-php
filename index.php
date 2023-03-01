<?php
/**
 * Prueba de código para MarketGoo. ¡Lee el README.md!
 */
require __DIR__."/vendor/autoload.php";

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Schema;
use App\Repository\UserRepository;

$userRepository = new UserRepository();
$geolocationService = new \App\Services\GeolocationService();

$users = $userRepository->findAll();

// Definimos el schema del tipo de dato "Usuario" para GraphQL
$graphql_user_type = new ObjectType([
    "name" => "User",
    "fields" => [
        "id" => Type::int(),
        "name" => Type::string(),
        "ip" => Type::string(),
        "ip_region" => Type::string()
    ]
]);

// Instanciamos la aplicación Slim. Es tan sencilla que sólo vamos a usar aquí
// la ruta "/graphql" para este test. Todo lo demás es por defecto.
$app = new Slim\App();

$app->map(["GET", "POST"], "/graphql", function(Request $request, Response $response) {
    global $users, $graphql_user_type, $geolocationService, $userRepository;
    $debug = \GraphQL\Error\Debug::INCLUDE_DEBUG_MESSAGE | \GraphQL\Error\Debug::INCLUDE_TRACE;
    try {
        $graphQLServer = new \GraphQL\Server\StandardServer([
            "schema" => new Schema([
                "query" => new ObjectType([
                    "name" => "Query",
                    "fields" => [
                        "user" => [
                            "type" => $graphql_user_type,
                            "args" => [
                                "id" => Type::nonNull(Type::int())
                            ],
                            "resolve" => function ($rootValue, $args) use ($users, $geolocationService, $userRepository) {
                                $user = $users[intval($args["id"])] ?? null;
                                if (!is_null($user) && empty($user['ip_region'])){

                                    try {
                                        $location = $geolocationService->getLocationFromIp([$user['ip']]);
                                        if(!empty($location)){
                                            $user['ip_region'] = $location[$user['ip']];
                                        }
                                        $userRepository->update(intval($args["id"]), $user);
                                    }
                                    catch (Exception $exception){
                                        return $user;
                                    }

                                }
                                return $user;
                            }
                        ],
                        "users" => [
                            "type" => Type::listOf($graphql_user_type),
                            "resolve" => function() use ($users) {
                                return $users;
                            }
                        ]
                    ]
                ])
            ]),
            "debug" => $debug
        ]);

        return $graphQLServer->processPsrRequest($request, $response, $response->getBody());
    } catch (\Exception $e) {
        return $response->withStatus($e->getCode() ?? 500)->withJson([
            "errors" => [\GraphQL\Error\FormattedError::createFromException($e, $debug)]
        ]);
    }
});

$app->run();
