<?php
/**
 * Classe Model: Post
 * Representa a entidade de publicações do Clube Felicite-se
 */
class Post {
    private $id;
    private $titulo;
    private $conteudo;
    private $categoria_id;
    private $autor_id;
    private $data_criacao;

    // Construtor para inicializar o objeto Post
    public function __construct($titulo = null, $conteudo = null, $categoria_id = null, $autor_id = null) {
        $this->titulo = $titulo;
        $this->conteudo = $conteudo;
        $this->categoria_id = $categoria_id;
        $this->autor_id = $autor_id;
    }

    // Getters e Setters
    public function getId() { return $this->id; }
    public function setId($id) { $this->id = $id; }

    public function getTitulo() { return $this->titulo; }
    public function setTitulo($titulo) { $this->titulo = $titulo; }

    public function getConteudo() { return $this->conteudo; }
    public function setConteudo($conteudo) { $this->conteudo = $conteudo; }

    public function getCategoriaId() { return $this->categoria_id; }
    public function setCategoriaId($categoria_id) { $this->categoria_id = $categoria_id; }

    public function getAutorId() { return $this->autor_id; }
    public function setAutorId($autor_id) { $this->autor_id = $autor_id; }
    
    public function getDataCriacao() { return $this->data_criacao; }
    public function setDataCriacao($data_criacao) { $this->data_criacao = $data_criacao; }
}