<?php
/**
 * Controller: PostController
 * Gerencia as requisições de criação, edição, listagem e exclusão de posts
 * Projeto: Clube Felicite-se
 */

require_once __DIR__ . '/../config/conexao.php';
require_once __DIR__ . '/../models/Post.php';
require_once __DIR__ . '/../dao/PostDAO.php';

class PostController {
    private $postDAO;

    public function __construct($pdo) {
        $this->postDAO = new PostDAO($pdo);
    }

    /**
     * Valida e realiza o upload da imagem de capa
     */
    private function uploadImagem($file) {
        if (isset($file) && $file['error'] === UPLOAD_ERR_OK) {
            $tamanhoMaximo = 2 * 1024 * 1024; // 2 MB
            if ($file['size'] > $tamanhoMaximo) {
                die("Erro: A imagem enviada excede o limite máximo permitido de 2 MB.");
            }

            $extensoesPermitidas = ['jpg', 'jpeg', 'png', 'webp'];
            $extensao = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

            if (!in_array($extensao, $extensoesPermitidas)) {
                die("Erro: Formato de imagem inválido. Use JPG, JPEG, PNG ou WEBP.");
            }

            $diretorio = __DIR__ . '/../uploads/imagens/';
            if (!is_dir($diretorio)) {
                mkdir($diretorio, 0755, true);
            }

            $nomeArquivo = uniqid('capa_') . '.' . $extensao;
            if (move_uploaded_file($file['tmp_name'], $diretorio . $nomeArquivo)) {
                return $nomeArquivo;
            }
        }
        return null;
    }

    /**
     * Valida e realiza o upload do arquivo PDF anexo
     */
    private function uploadPdf($file) {
        if (isset($file) && $file['error'] === UPLOAD_ERR_OK) {
            $tamanhoMaximo = 10 * 1024 * 1024; // 10 MB
            if ($file['size'] > $tamanhoMaximo) {
                die("Erro: O arquivo PDF excede o limite máximo permitido de 10 MB.");
            }

            $extensao = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            if ($extensao !== 'pdf') {
                die("Erro: Apenas arquivos no formato PDF são aceitos.");
            }

            $diretorio = __DIR__ . '/../uploads/pdfs/';
            if (!is_dir($diretorio)) {
                mkdir($diretorio, 0755, true);
            }

            $nomeArquivo = uniqid('doc_') . '.' . $extensao;
            if (move_uploaded_file($file['tmp_name'], $diretorio . $nomeArquivo)) {
                return $nomeArquivo;
            }
        }
        return null;
    }

    /**
     * Cadastra um novo artigo/publicação
     */
    public function criar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $titulo = filter_input(INPUT_POST, 'titulo', FILTER_SANITIZE_SPECIAL_CHARS);
            $conteudo = $_POST['conteudo'] ?? '';
            $categoria_id = filter_input(INPUT_POST, 'categoria_id', FILTER_VALIDATE_INT) ?: 1;
            $autor_id = $_SESSION['usuario_id'] ?? 1;

            $nomeImagem = $this->uploadImagem($_FILES['imagem'] ?? null);
            $nomePdf = $this->uploadPdf($_FILES['pdf_anexo'] ?? null);

            $novoPost = new Post($titulo, $conteudo, $categoria_id, $autor_id, $nomeImagem, $nomePdf);

            if ($this->postDAO->inserir($novoPost)) {
                header("Location: index.php?status=sucesso");
                exit;
            } else {
                echo "Erro crítico: Falha ao inserir o registro.";
            }
        }
    }

    /**
     * Remove com segurança um arquivo físico de imagem do disco
     */
    private function removerImagemDoDisco($nomeArquivo) {
        if (!empty($nomeArquivo)) {
            $caminho = __DIR__ . '/../uploads/imagens/' . basename($nomeArquivo);
            if (file_exists($caminho) && is_file($caminho)) {
                @unlink($caminho);
            }
        }
    }

    /**
     * Remove com segurança um arquivo físico de PDF do disco
     */
    private function removerPdfDoDisco($nomeArquivo) {
        if (!empty($nomeArquivo)) {
            $caminho = __DIR__ . '/../uploads/pdfs/' . basename($nomeArquivo);
            if (file_exists($caminho) && is_file($caminho)) {
                @unlink($caminho);
            }
        }
    }

    /**
     * Atualiza um artigo existente e remove arquivos físicos substituídos
     */
    public function editar($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $titulo = filter_input(INPUT_POST, 'titulo', FILTER_SANITIZE_SPECIAL_CHARS);
            $conteudo = $_POST['conteudo'] ?? '';
            $categoria_id = filter_input(INPUT_POST, 'categoria_id', FILTER_VALIDATE_INT) ?: 1;

            $postAtual = $this->postDAO->buscarPorId($id);

            $post = new Post($titulo, $conteudo, $categoria_id, null);
            $post->setId($id);

            $novaImagem = $this->uploadImagem($_FILES['imagem'] ?? null);
            if ($novaImagem !== null) {
                // Remove a imagem anterior do disco para evitar arquivos órfãos
                if ($postAtual && !empty($postAtual['imagem'])) {
                    $this->removerImagemDoDisco($postAtual['imagem']);
                }
                $post->setImagem($novaImagem);
            }

            $novoPdf = $this->uploadPdf($_FILES['pdf_anexo'] ?? null);
            if ($novoPdf !== null) {
                // Remove o PDF anterior do disco para evitar arquivos órfãos
                if ($postAtual && !empty($postAtual['pdf_anexo'])) {
                    $this->removerPdfDoDisco($postAtual['pdf_anexo']);
                }
                $post->setPdfAnexo($novoPdf);
            }

            if ($this->postDAO->atualizar($post)) {
                header("Location: index.php?status=atualizado");
                exit;
            } else {
                echo "Erro crítico: Falha ao atualizar o registro.";
            }
        }
    }

    /**
     * Lista publicações com suporte a busca textual e filtro por categoria
     */
    public function listarPublicos($busca = null, $categoria_id = null) {
        return $this->postDAO->listarTodos($busca, $categoria_id);
    }

    /**
     * Retorna todas as categorias disponíveis
     */
    public function listarCategorias() {
        return $this->postDAO->listarCategorias();
    }

    public function exibirArtigo($id) {
        $idValidado = filter_var($id, FILTER_VALIDATE_INT);
        return $idValidado ? $this->postDAO->buscarPorId($idValidado) : null;
    }

    /**
     * Exclui a publicação do banco de dados e remove todos os arquivos físicos vinculados (Descarte Seguro)
     */
    public function deletar($id) {
        $idValidado = filter_var($id, FILTER_VALIDATE_INT);
        if ($idValidado) {
            // Obtém os dados da publicação antes de remover do banco
            $post = $this->postDAO->buscarPorId($idValidado);
            if ($post) {
                // Limpeza física dos arquivos no disco
                $this->removerImagemDoDisco($post['imagem'] ?? null);
                $this->removerPdfDoDisco($post['pdf_anexo'] ?? null);

                // Exclui o registro no banco
                if ($this->postDAO->excluir($idValidado)) {
                    header("Location: index.php?status=excluido");
                    exit;
                }
            }
        }
        header("Location: index.php?status=erro");
        exit;
    }
}