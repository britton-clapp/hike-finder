<?php
require 'vendor/autoload.php';
 
function getDB()
{
    $dbhost = "localhost";
    $dbuser = "root";
    $dbpass = "root";
    $dbname = "hikes";
 
    $mysql_conn_string = "mysql:host=$dbhost;dbname=$dbname";
    $dbConnection = new PDO($mysql_conn_string, $dbuser, $dbpass); 
    $dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $dbConnection;
}
 
$app = new \Slim\Slim(array(
    'templates.path' => 'views',
));

$app->view = new \Slim\Views\Twig();
$app->view->setTemplatesDirectory("views");

$view = $app->view();
$view->parserOptions = ['debug' => true];
$view->parserExtensions = [new \Slim\Views\TwigExtension()];

// @todo: Implement twig templates: http://www.slimframework.com/docs/features/templates.html
$app->get('/', function() use($app) {
    try 
    {
        $db = getDB();
        $sth = $db->prepare("SELECT * FROM hikes.hikes
order by rand() limit 1;");
 
        $sth->execute();
 
        $location = $sth->fetch(PDO::FETCH_OBJ);

        $req = $app->request();
        $json = $req->get('json');

        $text_options = array(
            'How about %location%?',
            '%location% is nice.',
            'Let\'s go to %location%!',
            'Have you been to %location% lately?',
        );
        shuffle($text_options);
        $verbiage = reset($text_options);
 
        if($location) {
            $verbiage = str_replace('%location%', $location->name, $verbiage);
            $location->verbiage = $verbiage;
            if ($json) {
                $app->response->setStatus(200);
                $app->response()->headers->set('Content-Type', 'application/json');
                echo json_encode($location);
                $db = null;
            }
            else {
                // @todo: Render with bootstrap and twig views
                #slim::view()->setData(array('location' => $location));
                $app->render('home.html', array('location' => $location));
            }
        } else {
            throw new PDOException('No records found.');
        }
 
    } catch(PDOException $e) {
        $app->response()->setStatus(404);
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}); 
 
$app->get('/where', function ($id) {
    $app = \Slim\Slim::getInstance();
 
    try 
    {
        $db = getDB();
        $sth = $db->prepare("SELECT * FROM hikes.hikes
order by rand() limit 1;");
 
        $sth->execute();
 
        $student = $sth->fetch(PDO::FETCH_OBJ);
 
        if($student) {
            $app->response->setStatus(200);
            $app->response()->headers->set('Content-Type', 'application/json');
            echo json_encode($student);
            $db = null;
        } else {
            throw new PDOException('No records found.');
        }
 
    } catch(PDOException $e) {
        $app->response()->setStatus(404);
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
});


$app->run();