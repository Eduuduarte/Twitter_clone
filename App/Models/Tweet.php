<?php

namespace App\Models;

use MF\Model\Model;

class Tweet extends Model {

	private $id;
	private $id_usuario;
	private $tweet;
	private $data;

	public function __get($atributo) {
		return $this->$atributo;
	}

	public function __set($atributo, $valor){
		$this->$atributo = $valor;
	}

	//Salvar
	public function salvar() {

		$query = 'insert into twetts(id_usuario, tweet)values(:id_usuario, :tweet)';
		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':id_usuario', $this->__get('id_usuario'));
		$stmt->bindValue(':tweet', $this->__get('tweet'));
		$stmt->execute();

		return $this;
	}

	//Recuperar
	public function getAll() {
		$query = "
			select
			 	t.id,
			 	t.id_usuario,
			 	u.nome, t.tweet,
			 	DATE_FORMAT(t.data, '%d/%m/%Y %H:%i') as data
			 from
			 	twetts as t
			 	left join usuarios as u on (t.id_usuario = u.id)
			 where
			 	id_usuario = :id_usuario
			 	or t.id_usuario in (select id_usuario_seguindo from usuarios_seguidores
			 	where id_usuario = :id_usuario)
			 order by
			 	t.data desc
		";

		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':id_usuario', $this->__get('id_usuario'));
		$stmt->execute();

		return $stmt->fetchAll(\PDO::FETCH_ASSOC);

	}

	//Recuperar com páginação
	public function getPorPagina($limit, $offset) {
		$query = "
			select
			 	t.id,
			 	t.id_usuario,
			 	u.nome, t.tweet,
			 	DATE_FORMAT(t.data, '%d/%m/%Y %H:%i') as data
			 from
			 	twetts as t
			 	left join usuarios as u on (t.id_usuario = u.id)
			 where
			 	id_usuario = :id_usuario
			 	or t.id_usuario in (select id_usuario_seguindo from usuarios_seguidores
			 	where id_usuario = :id_usuario)
			 order by
			 	t.data desc
			 limit
			 	$limit
			 offset
			 	$offset
		";

		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':id_usuario', $this->__get('id_usuario'));
		$stmt->execute();

		return $stmt->fetchAll(\PDO::FETCH_ASSOC);

	}

	//Recuperar total tweets
	public function getTotalRegistros() {
		$query = "
			select
			 	count(*) as total
			 from
			 	twetts as t
			 	left join usuarios as u on (t.id_usuario = u.id)
			 where
			 	id_usuario = :id_usuario
			 	or t.id_usuario in (select id_usuario_seguindo from usuarios_seguidores
			 	where id_usuario = :id_usuario)
		";

		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':id_usuario', $this->__get('id_usuario'));
		$stmt->execute();

		return $stmt->fetch(\PDO::FETCH_ASSOC);

	}

	public function removerTweet() {

		$query = "delete from twetts where id_usuario = :id_usuario and id = :id";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':id', $this->__get('id'));
		$stmt->bindValue(':id_usuario', $this->__get('id_usuario'));
		$stmt->execute();

		return true;
		
	}
	
}