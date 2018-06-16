<?php

use Slim\Http\Request;
use Slim\Http\Response;

// Routes

$app->get('/[{name}]', function (Request $request, Response $response, array $args) {
    // Sample log message
    $this->logger->info("Slim-Skeleton '/' route");

    // Render index view
    return $this->renderer->render($response, 'index.phtml', $args);
});

$app = new \Slim\App();

// Listar Contatos
$app->get('/contatos', function ($request, $response, $args) {
    $sql = "SELECT * FROM contatos";
    try{
        // Criando a instância da classe de conexão
        $db = new db();
        // Conectando e executando
        $db = $db->connect();
        $stmt = $db->query($sql);
        $contatos = $stmt->fetchAll(PDO::FETCH_OBJ);
        $db = null;
        echo json_encode($contatos);
    } catch(PDOException $e){
        echo '{"error": {"text": '.$e->getMessage().'}';
    }
});

// Pegar Contato por ID
$app->get('/contatos/{id}', function ($request, $response, $args) {
    $id = $request->getAttribute('id');
    $sql = "SELECT * FROM contatos WHERE id = $id";
    try{
        // Criando a instância da classe de conexão
        $db = new db();
        // Conectando e executando
        $db = $db->connect();
        $stmt = $db->query($sql);
        $contato = $stmt->fetch(PDO::FETCH_OBJ);
        $db = null;
        echo json_encode($contato);
    } catch(PDOException $e){
        echo '{"error": {"text": '.$e->getMessage().'}';
    }
});

// Adicionar Contato
$app->post('/contatos/add', function ($request, $response, $args) {
    //Pegando os parâmetros
    $nome = $request->getParam('nome');
    $email = $request->getParam('email');
    $telefone = $request->getParam('telefone');
    $foto = $request->getParam('foto');
    //Query SQL
    $sql = "INSERT INTO contatos (nome,email,telefone,foto) VALUES
    (:nome,:email,:telefone,:foto)";
    try{
        // Criando a instância da classe de conexão
        $db = new db();
        // Conectando
        $db = $db->connect();
        $stmt = $db->prepare($sql);
        //Bindando os parâmetros e executandos
        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':telefone', $telefone);
        $stmt->bindParam(':foto', $foto);
        $stmt->execute();
        echo '{"notice": {"text": "Contato adicionado!"}';
    } catch(PDOException $e){
        echo '{"error": {"text": '.$e->getMessage().'}';
    }

});

// Atualizar Contato
$app->put('/contatos/atualizar/{id}', function ($request, $response, $args) {
    //Pegando os parâmetros
    $id = $request->getAttribute('id');
    $nome = $request->getParam('nome');
    $email = $request->getParam('email');
    $telefone = $request->getParam('telefone');
    $foto = $request->getParam('foto');
    //Query SQL
    $sql = "UPDATE contatos SET
                nome 	    = :nome,
				email 	    = :email,
                telefone    = :telefone,
                foto        = :foto
			WHERE id = $id";
    try{
        // Criando a instância da classe de conexão
        $db = new db();
        // Conectando
        $db = $db->connect();
        $stmt = $db->prepare($sql);
        //Bindando os parâmetros e executandos
        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':telefone', $telefone);
        $stmt->bindParam(':foto', $foto);
        $stmt->execute();
        echo '{"notice": {"text": "Contato atualizado!"}';
    } catch(PDOException $e){
        echo '{"error": {"text": '.$e->getMessage().'}';
    }

});

// Apagar Contato
$app->delete('/contatos/excluir/{id}', function ($request, $response, $args) {
    //Pegando os parâmetros
    $id = $request->getAttribute('id');
    
    //Query SQL
    $sql = "DELETE FROM contatos WHERE id = $id";
    try{
        // Criando a instância da classe de conexão
        $db = new db();
        // Conectando e executando
        $db = $db->connect();
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $db = null;

        echo '{"notice": {"text": "Contato excluido!"}';
    } catch(PDOException $e){
        echo '{"error": {"text": '.$e->getMessage().'}';
    }

});

$app->options('/{routes:.+}', function ($request, $response, $args) {
    return $response;
});

$app->add(function ($req, $res, $next) {
    $response = $next($req, $res);
    return $response
            ->withHeader('Access-Control-Allow-Origin', 'http://contatosweb')
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS');
});

$app->map(['GET', 'POST', 'PUT', 'DELETE', 'PATCH'], '/{routes:.+}', function($req, $res) {
    $handler = $this->notFoundHandler; // handle using the default Slim page not found handler
    return $handler($req, $res);
});