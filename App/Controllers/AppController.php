<?php

namespace App\Controllers;

//Recursos do miniframework
use MF\Controller\Action;
use MF\Model\Container; 


class AppController extends Action {

	public function timeline() {


		//Evitando o By pass sem passar primeiro pela validação do usuário.
		$this->validaAutenticacao();

		//Recuperação dos tweets
		$tweet = Container::getModel('Tweet');


		
		$tweet->__set('id_usuario', $_SESSION['id']);


		$tweets = $tweet->getAll();
			

		$teste = $this->view->tweets = $tweets;

		$usuario = Container::getModel('Usuario');
		$usuario->__set('id', $_SESSION['id']);

		$this->view->info_usuario = $usuario->getInfoUsuario();
		$this->view->total_tweets = $usuario->getTotalTweets();
		$this->view->total_seguindo = $usuario->getTotalSeguindo();
		$this->view->total_seguidores = $usuario->getTotalSeguidores();

		$this->render('timeline');

		

		
	}

	//Função para desconectar o usuário
	public function sair() {
		session_start();

		session_destroy();

		header('Location: /');
	}

	public function tweet() {

		//Evitando o By pass sem passar primeiro pela validação do usuário.
		
		$this->validaAutenticacao();



		$tweet = Container::getModel('Tweet');



		$tweet->__set('tweet', $_POST['tweet']);
		$tweet->__set('id_usuario', $_SESSION['id']);

		$tweet->salvar();

		header('Location: /timeline');

				
	}

	public function validaAutenticacao() {

		session_start();

		if(!isset($_SESSION['id']) || $_SESSION['id'] == '' || !isset($_SESSION['nome']) || $_SESSION['nome'] == ''){
			header('Location: /?login=erro');
		} 
	}

	public function quem_seguir() {
		$this->validaAutenticacao();
		
		$pesquisarPor = isset($_GET['pesquisarPor']) ? $_GET['pesquisarPor'] : '';
	
		$usuarios = array();

		if($pesquisarPor != ''){
			$usuario = Container::getModel('Usuario');
			$usuario->__set('nome', $pesquisarPor);
			$usuario->__set('id', $_SESSION['id']);
			$usuarios = $usuario->getAll();
		}

		$this->view->usuarios = $usuarios;

		$usuarioo = Container::getModel('Usuario');
		$usuarioo->__set('id', $_SESSION['id']);

		$this->view->info_usuario = $usuarioo->getInfoUsuario();
		$this->view->total_tweets = $usuarioo->getTotalTweets();
		$this->view->total_seguindo = $usuarioo->getTotalSeguindo();
		$this->view->total_seguidores = $usuarioo->getTotalSeguidores();


		$this->render('quemSeguir');

	}

	public function acao() {
		$this->validaAutenticacao();

		//acao
		$acao = isset($_GET['acao']) ? $_GET['acao'] : '';
		$id_usuario_seguindo = isset($_GET['id_usuario']) ? $_GET['id_usuario'] : '';

		$usuario = Container::getModel('Usuario');
		$usuario->__set('id', $_SESSION['id']);

		if($acao == 'seguir'){
			$usuario->seguirUsuario($id_usuario_seguindo);

		} else if($acao == 'deixar_de_seguir'){
			$usuario->deixarSeguirUsuario($id_usuario_seguindo);
		}

		header('Location: /quem_seguir');

	}

	public function remover(){
		$this->validaAutenticacao();

		//recuperar valores
		$id_usuario = isset($_GET['id_usuario']) ? $_GET['id_usuario'] : '';
		$id_tweet = isset($_GET['id_tweet']) ? $_GET['id_tweet'] : '';

		$tweet = Container::getModel('Tweet');
		$tweet->__set('id', $id_tweet);
		$tweet->__set('id_usuario', $id_usuario);

		$tweet->removerTweet();

		header('Location: /timeline');

	}
}

?>