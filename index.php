<?php

require_once __DIR__ . '/vendor/autoload.php';

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

$app = new Application();

$app['debug'] = true;

$app->register(new Silex\Provider\DoctrineServiceProvider(), array(
    'db.options' => array(
        'driver' => 'pdo_mysql',
        'host' => 'localhost',
        'dbname' => 'assistencia_tecnica',
        'user' => 'root',
        'password' => 'mateus2025',
        'charset' => 'utf8mb4',
    )
));

//PRODUTOS
$app->get('/produtos', function() use($app) {
    $produtos = $app['db']->query('SELECT * FROM assistencia_tecnica.produto')->fetchAll();
    if ($produtos) {
        return New Response($app->json($produtos), 200);
    } else {
        return New Response($app->json('Nenhum dado encontrado'), 404);
    }
});

$app->get('/produto/{id}', function ($id) use($app) {
    $produto = $app['db']->query("SELECT * FROM assistencia_tecnica.produto where id = {$id}")->fetchAll();
    if ($produto) {
        return new Response($app->json($produto), 200);
    } else {
        return New Response($app->json('Nao encontrado'), 404);
    }
});

$app->post('/cadastro-produto', function (Request $request) use($app) {

    $dados = json_decode($request->getContent(), true);
    $cadastro = $app['db']->query(
            "INSERT INTO assistencia_tecnica.produto SET
            descricao = '" . addslashes($dados['descricao']) . "',
            imei = '" . addslashes($dados['imei']) . "',
            lote = '" . addslashes($dados['lote']) . "',
            nota_fiscal_id = '" . addslashes($dados['nfe']) . "'");
    if (!empty($cadastro)) {
        return New Response($app->json('Produto cadastrado com sucesso'), 200);
    } else {
        return new Response($app->json('Algo deu errado'), 404);
    }
});

$app->put('/atualizar-produto/{id}', function (Request $request, $id) use($app) {
    $dados = json_decode($request->getContent(), true);

    $atualizar = $app['db']->query(
            "UPDATE assistencia_tecnica.produto SET 
            descricao = '" . addslashes($dados['descricao']) . "',
            imei = '" . addslashes($dados['imei']) . "',
            lote = '" . addslashes($dados['lote']) . "',
            nota_fiscal_id = '" . addslashes($dados['nfe']) . "'
            where id = {$id} ");
    if (!empty($atualizar)) {
        return new Response($app->json('Atualizado com sucesso'), 200);
    } else {
        return new Response($app->json('Algo deu errado'), 304);
    }
});

$app->delete('/deletar-produto/{id}', function ($id) use($app) {
    $delete = $app['db']->query("DELETE FROM assistencia_tecnica.produto where id = {$id}")->execute();
    if ($delete) {
        return new Response($app->json('Deletado com sucesso'), 200);
    } else {
        return new Response($app->json('Algo deu errado'), 404);
    }
});

//PRODUTO PELO SERIAL
$app->get('/produto-serial/{serial}', function($serial) use($app) {
    $produto_serial = $app['db']->query("SELECT * FROM assistencia_tecnica.produto where imei = {$serial}")->fetchAll();
    if ($produto_serial) {
        return new Response($app->json($produto_serial), 200);
    } else {
        return New Response($app->json('Nao encontrado'), 404);
    }
});

//NOTAS FISCAIS
$app->get('/notas-fiscais', function() use($app) {
    $notas = $app['db']->query('SELECT * FROM assistencia_tecnica.nota_fiscal')->fetchAll();
    if ($notas) {
        return new Response($app->json($notas), 200);
    } else {
        return New Response($app->json('Nao encontrado'), 404);
    }
});

$app->get('/nota-fiscal/{id}', function ($id) use($app) {
    $nota = $app['db']->query("SELECT * FROM assistencia_tecnica.nota_fiscal where id = {$id}")->fetchAll();
    if ($nota) {
        return new Response($app->json($nota), 200);
    } else {
        return new Response($app->json('Nao encontrado'), 404);
    }
});

$app->post('/cadastro-nfe', function (Request $request) use($app) {

    $dados = json_decode($request->getContent(), true);
    $data = DateTime::createFromFormat('d/m/Y', $dados['emissao']);

    $cadastro = $app['db']->query(
            "INSERT INTO assistencia_tecnica.nota_fiscal SET
  nota = '" . addslashes($dados['nota']) . "',
  SR = '" . addslashes($dados['sr']) . "',
  data_emissao = '" . $data->format('Y-m-d') . "',
  pedido = '" . addslashes($dados['pedido']) . "',
  fornecedor = '" . addslashes($dados['fornecedor']) . "'");
    if (!empty($cadastro)) {
        return new Response($app->json('cadastrado com sucesso'), 200);
    } else {
        return new Response($app->json('Algo deu errado'), 404);
    }
});

$app->put('/atualizar-nfe/{id}', function (Request $request, $id) use($app) {
    $dados = json_decode($request->getContent(), true);

    $atualizar = $app['db']->query(
            "UPDATE assistencia_tecnica.nota_fiscal SET
  nota = '" . addslashes($dados['nota']) . "',
  SR = '" . addslashes($dados['sr']) . "',
  data_emissao = '" . addslashes($dados['emissao']) . "',
  pedido = '" . addslashes($dados['pedido']) . "',
  fornecedor = '" . addslashes($dados['fornecedor']) . "'
  where id = {$id} ");
    if (!empty($atualizar)) {
        return new Response($app->json('Atualizado com sucesso'), 200);
    } else {
        return new Response($app->json('Algo deu errado'), 304);
    }
});

$app->run();
