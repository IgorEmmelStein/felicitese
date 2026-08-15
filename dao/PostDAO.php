<?php
/**
 * Responsável pelas operações de persistência da entidade Post no MariaDB
 */
require_once __DIR__ . '/../config/conexao.php';
require_once __DIR__ . '/../models/Post.php';

    class PostDAO {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Método para listar todas as publicações na Central de Conteúdo
    public function listarTodos() {
        $sql = "SELECT p.*, c.nome as categoria_nome FROM posts p 
                LEFT JOIN categorias c ON p.categoria_id = c.id 
                ORDER BY p.data_criacao DESC";
        
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll();
    }

    // Método para inserir um novo post com segurança (Prepared Statements)
    public function inserir(Post $post) {
        $sql = "INSERT INTO posts (titulo, conteudo, categoria_id, autor_id, data_criacao) 
                VALUES (:titulo, :conteudo, :categoria_id, :autor_id, NOW())";
        
        $stmt = $this->pdo->prepare($sql);
        
        return $stmt->execute([
            ':titulo' => $post->getTitulo(),
            ':conteudo' => $post->getConteudo(),
            ':categoria_id' => $post->getCategoriaId(),
            ':autor_id' => $post->getAutorId()
        ]);
    }

    // Método para buscar um post específico pelo ID (para a página de leitura)
    public function buscarPorId($id) {
        $sql = "SELECT p.*, c.nome as categoria_nome FROM posts p 
                LEFT JOIN categorias c ON p.categoria_id = c.id 
                WHERE p.id = :id";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    // Método para atualizar uma publicação existente
    public function atualizar(Post $post) {
        $sql = "UPDATE posts SET titulo = :titulo, conteudo = :conteudo, categoria_id = :categoria_id";
        
        // Se houver um novo PDF anexo, atualiza também o campo correspondente
        if ($post->getPdfAnexo()) {
            $sql .= ", pdf_anexo = :pdf_anexo";
        }
        
        $sql .= " WHERE id = :id";
        
        $stmt = $this->pdo->prepare($sql);
        
        $parametros = [
            ':titulo' => $post->getTitulo(),
            ':conteudo' => $post->getConteudo(),
            ':categoria_id' => $post->getCategoriaId(),
            ':id' => $post->getId()
        ];
        
        if ($post->getPdfAnexo()) {
            $parametros[':pdf_anexo'] = $post->getPdfAnexo();
        }
        
        return $stmt->execute($parametros);
    }

    // Método para eliminar uma publicação pelo ID
    public function excluir($id) {
        $sql = "DELETE FROM posts WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }
}