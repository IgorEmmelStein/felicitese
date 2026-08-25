<?php
/**
 * Classe Data Access Object: PostDAO
 * Responsável pela persistência e manipulação da tabela 'posts'
 * Projeto: Clube Felicite-se
 */

require_once __DIR__ . '/../config/conexao.php';
require_once __DIR__ . '/../models/Post.php';

class PostDAO {
    private $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Lista publicações com suporte a busca textual por palavra-chave e filtro de categoria
     */
    public function listarTodos($busca = null, $categoriaId = null) {
        $sql = "SELECT p.*, c.nome AS categoria_nome 
                FROM posts p 
                LEFT JOIN categorias c ON p.categoria_id = c.id 
                WHERE 1=1";
        
        $parametros = [];

        if (!empty($busca)) {
            $sql .= " AND (p.titulo LIKE :busca_titulo OR p.conteudo LIKE :busca_conteudo)";
            $termoBusca = '%' . $busca . '%';
            $parametros[':busca_titulo'] = $termoBusca;
            $parametros[':busca_conteudo'] = $termoBusca;
        }

        if (!empty($categoriaId)) {
            $sql .= " AND p.categoria_id = :categoria_id";
            $parametros[':categoria_id'] = $categoriaId;
        }

        $sql .= " ORDER BY p.data_criacao DESC, p.id DESC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($parametros);
        return $stmt->fetchAll();
    }

    /**
     * Lista todas as categorias com a contagem de posts associados
     */
    public function listarCategorias() {
        $sql = "SELECT c.id, c.nome, COUNT(p.id) AS total_posts 
                FROM categorias c 
                LEFT JOIN posts p ON p.categoria_id = c.id 
                GROUP BY c.id, c.nome 
                ORDER BY c.nome ASC";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll();
    }

    /**
     * Busca um artigo específico pelo ID
     */
    public function buscarPorId($id) {
        $sql = "SELECT p.*, c.nome AS categoria_nome 
                FROM posts p 
                LEFT JOIN categorias c ON p.categoria_id = c.id 
                WHERE p.id = :id";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch();
    }

    /**
     * Insere um novo post com suporte a imagem de capa e PDF anexo
     */
    public function inserir(Post $post) {
        $sql = "INSERT INTO posts (titulo, conteudo, imagem, pdf_anexo, categoria_id, autor_id, data_criacao) 
                VALUES (:titulo, :conteudo, :imagem, :pdf_anexo, :categoria_id, :autor_id, NOW())";
        
        $stmt = $this->pdo->prepare($sql);
        
        return $stmt->execute([
            ':titulo'       => $post->getTitulo(),
            ':conteudo'     => $post->getConteudo(),
            ':imagem'       => $post->getImagem(),
            ':pdf_anexo'    => $post->getPdfAnexo(),
            ':categoria_id' => $post->getCategoriaId(),
            ':autor_id'     => $post->getAutorId()
        ]);
    }

    /**
     * Atualiza um post existente
     * Preserva as mídias atuais se nenhum novo arquivo for enviado
     */
    public function atualizar(Post $post) {
        $sql = "UPDATE posts 
                SET titulo = :titulo, 
                    conteudo = :conteudo, 
                    categoria_id = :categoria_id";
        
        $parametros = [
            ':titulo'       => $post->getTitulo(),
            ':conteudo'     => $post->getConteudo(),
            ':categoria_id' => $post->getCategoriaId(),
            ':id'           => $post->getId()
        ];

        // Atualiza a imagem apenas se uma nova foi fornecida
        if ($post->getImagem() !== null) {
            $sql .= ", imagem = :imagem";
            $parametros[':imagem'] = $post->getImagem();
        }

        // Atualiza o PDF apenas se um novo foi fornecido
        if ($post->getPdfAnexo() !== null) {
            $sql .= ", pdf_anexo = :pdf_anexo";
            $parametros[':pdf_anexo'] = $post->getPdfAnexo();
        }
        
        $sql .= " WHERE id = :id";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($parametros);
    }

    /**
     * Remove uma publicação pelo ID
     */
    public function excluir($id) {
        $sql = "DELETE FROM posts WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}