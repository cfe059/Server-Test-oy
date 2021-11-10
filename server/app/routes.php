<?php
declare(strict_types=1);

use App\Application\Actions\User\ListUsersAction;
use App\Application\Actions\User\ViewUserAction;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

return function (App $app) {
    $app->options('/{routes:.*}', function (Request $request, Response $response) {
        // CORS Pre-Flight OPTIONS Request Handler
        return $response;
    });

    // 投稿一覧の取得API
    $app->get('/api/page', function (Request $request, Response $response) {
        // データベース操作
        $link = mysqli_connect('localhost', 'root', '', 'bulletin-board');
        $result = mysqli_query($link, 'SELECT * FROM messages');
        $messages = mysqli_fetch_all($result);
        mysqli_close($link);

        // 文字列（json形式）にして返す
        $response->getBody()->write(json_encode($messages, JSON_UNESCAPED_UNICODE));
        return $response;
    });

    // 新規投稿API
    $app->post('/api/page', function (Request $request, Response $response) {
        $params = $request->getQueryParams();
        $title = $params['title'];
        $message = $params['message'];

        // データベース操作
        $link = mysqli_connect('localhost', 'root', '', 'bulletin-board');
        $stmt = mysqli_prepare($link, "INSERT INTO messages (title, message) VALUES (?, ?)");
        mysqli_stmt_bind_param($stmt, "ss", $title, $message);
        $result = mysqli_stmt_execute($stmt);
        
        mysqli_close($link);

        $response->getBody()->write(json_encode($result, JSON_UNESCAPED_UNICODE));
        return $response;
    });

    // 更新API
    $app->put('/api/page', function (Request $request, Response $response) {
        $params = $request->getQueryParams();
        $id = $params['id'];
        $title = $params['title'];
        $message = $params['message'];

        // データベース操作
        $link = mysqli_connect('localhost', 'root', '', 'bulletin-board');
        $stmt = mysqli_prepare($link, "UPDATE messages SET title = ?, message = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "ssi", $title, $message, $id);
        $result = mysqli_stmt_execute($stmt);
        
        mysqli_close($link);

        $response->getBody()->write(json_encode($result, JSON_UNESCAPED_UNICODE));
        return $response;
    });

    // 削除API
    $app->delete('/api/page', function (Request $request, Response $response) {
        $params = $request->getQueryParams();
        $id = $params['id'];

        // データベース操作
        $link = mysqli_connect('localhost', 'root', '', 'bulletin-board');
        $stmt = mysqli_prepare($link, "DELETE FROM messages WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "i", $id);
        $result = mysqli_stmt_execute($stmt);
        
        mysqli_close($link);

        $response->getBody()->write(json_encode($result, JSON_UNESCAPED_UNICODE));
        return $response;
    });

    $app->group('/users', function (Group $group) {
        $group->get('', ListUsersAction::class);
        $group->get('/{id}', ViewUserAction::class);
    });
};
