<?php

require_once __DIR__ . '/vendor/autoload.php';

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

$app = new Application();

$app['debug'] = true;

// REQUEST_METHOD
/*
  if ('/numeros-serie' == $_SERVER['PATH_INFO']) {
  echo 'estou mostrando os numeros de serie';
  } elseif ('GET' == $_SERVER['REQUEST_METHOD'] && '/numeros-serie/' == substr($_SERVER['PATH_INFO'], 0, strpos($_SERVER['PATH_INFO'], '/', 2) + 1)) {
  echo 'estou mostrando um numero de serie';
  } elseif ('PUT' == $_SERVER['REQUEST_METHOD'] && '/numeros-serie/' == substr($_SERVER['PATH_INFO'], 0, strpos($_SERVER['PATH_INFO'], '/', 2) + 1)) {
  echo 'estou atualizando um numero de serie';
  } elseif ('DELETE' == $_SERVER['REQUEST_METHOD'] && '/numeros-serie/' == substr($_SERVER['PATH_INFO'], 0, strpos($_SERVER['PATH_INFO'], '/', 2) + 1)) {
  echo 'estou apagando um numero de serie';
  } else {
  echo 'pagina nao encontrada';
  }
 */

//RETORNO NORMAL
/*
  $app->get('/cadastro-produto', function(Application $app, Request $request) {
  return 'Cadastra um numero-serie';
  });

  $app->get('/numeros-serie', function() {
  return 'Retorna todos numeros-serie';
  });

  $app->get('/numero-serie/{id}', function($id) {
  return 'Retorna um numero-serie' . ' - ' . $id;
  });

  $app->put('atualiza-numero-serie/{id}', function ($id) {
  return "Atulizacao do produto com id" . ' : ' . $id;
  });

  $app->delete('deleta-numero-serie/{id}', function ($id) {
  return 'Numero-serie' . ' : ' . $id . ' deletado';
  });

 */

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
    $produtos = print_r($app['db']->query('SELECT * FROM assistencia_tecnica.produto')->fetchAll(), true);
    return '<pre>' . $produtos . '</pre>';
});

$app->get('/produto/{id}', function ($id) use($app) {
    $produto = print_r($app['db']->query("SELECT * FROM assistencia_tecnica.produto where id = {$id}")->fetchAll(), true);
    return '<pre>' . $produto . '</pre>';
});

$app->post('/cadastro-produto', function (Request $request) use($app) {

    $dados = json_decode($request->getContent(), true);

    $cadastro = print_r($app['db']->query(
                    "INSERT INTO assistencia_tecnica.produto SET
            descricao = '" . addslashes($dados['descricao']) . "',
            imei = '" . addslashes($dados['imei']) . "',
            lote = '" . addslashes($dados['lote']) . "',
            nota_fiscal_id = '" . addslashes($dados['nfe']) . "'")->execute(), true);
    return 'Produto atualizado com sucesso';
});

$app->put('/atualizar-produto/{id}', function (Request $request, $id) use($app) {
    $dados = json_decode($request->getContent(), true);

    $atualizar = print_r($app['db']->query(
                    "UPDATE assistencia_tecnica.produto SET 
            descricao = '" . addslashes($dados['descricao']) . "',
            imei = '" . addslashes($dados['imei']) . "',
            lote = '" . addslashes($dados['lote']) . "',
            nota_fiscal_id = '" . addslashes($dados['nfe']) . "'
            where id = {$id} ")->execute(), true);
    return 'Produto atualizado com sucesso';
});

$app->delete('/deletar-produto/{id}', function ($id) use($app) {
    $delete = print_r($app['db']->query("DELETE FROM assistencia_tecnica.produto where id = {$id}")->execute(), true);
    return 'Deletado com sucesso';
});

//PRODUTO PELO SERIAL
$app->get('/produto-serial/{serial}', function($serial) use($app) {
    $produto_serial = print_r($app['db']->query("SELECT * FROM assistencia_tecnica.produto where imei = {$serial}")->fetchAll(), true);
    return '<pre>' . $produto_serial . '</pre>';
});

//NOTAS FISCAIS
$app->get('/notas-fiscais', function() use($app) {
    $notas = print_r($app['db']->query('SELECT * FROM assistencia_tecnica.nota_fiscal')->fetchAll(), true);
    return '<pre>' . $notas . '</pre>';
});

$app->get('/nota-fiscal/{id}', function ($id) use($app) {
    $nota = print_r($app['db']->query("SELECT * FROM assistencia_tecnica.nota_fiscal where id = {$id}")->fetchAll(), true);
    return '<pre>' . $nota . '</pre>';
});

$app->post('/cadastro-nfe', function (Request $request) use($app) {

    $dados = json_decode($request->getContent(), true);
    $data = DateTime::createFromFormat('d/m/Y', $dados['emissao']);

    $cadastro = print_r($app['db']->query(
                    "INSERT INTO assistencia_tecnica.nota_fiscal SET
            nota = '" . addslashes($dados['nota']) . "',
            SR = '" . addslashes($dados['sr']) . "',
            data_emissao = '" . $data->format('Y-m-d H:i:s') . "',
            pedido = '" . addslashes($dados['pedido']) . "',
            fornecedor = '" . addslashes($dados['fornecedor']) . "'")->execute(), true);
    echo $cadastro;
    return 'Nota fiscal cadastrada com sucesso';
});

$app->put('/atualizar-nfe/{id}', function (Request $request, $id) use($app) {
    $dados = json_decode($request->getContent(), true);

    $atualizar = print_r($app['db']->query(
                    "UPDATE assistencia_tecnica.nota_fiscal SET 
            nota = '" . addslashes($dados['nota']) . "',
            SR = '" . addslashes($dados['sr']) . "',
            data_emissao = '" . addslashes($dados['emissao']) . "',
            pedido = '" . addslashes($dados['pedido']) . "',
            fornecedor = '" . addslashes($dados['fornecedor']) . "'
            where id = {$id} ")->execute(), true);
    return 'Atualizado com sucesso';
});

$app->delete('/deletar-nfe/{id}', function ($id) use($app) {
    $deletar = print_r($app['db']->query("DELETE FROM assistencia_tecnica.nota_fiscal where id = {$id}")->execute(), true);
    return 'Deletado com sucesso';
});

$app->run();
