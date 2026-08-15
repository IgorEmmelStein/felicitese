<?php
/**
 * Classe Controller: PostController
 * Intermedeia as requisições do painel administrativo e a persistência via PostDAO
 */
require_once __DIR__ . '/../config/conexao.php';
require_once __DIR__ . '/../models/Post.php';
require_once __DIR__ . '/../dao/PostDAO.php';

class PostController {
    private $postDAO;

    public function __construct($pdo) {
        // Inicializa o DAO injetando a conexão PDO
        $this->postDAO = new PostDAO($pdo);
    }

    // Processa a criação de um novo post vindo do painel administrativo
    public function criar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Captura e sanitiza os dados básicos do formulário
            $titulo = filter_input(INPUT_POST, 'titulo', FILTER_SANITIZE_SPECIAL_CHARS);
            $conteudo = $_POST['conteudo'] ?? ''; // Conteúdo bruto do editor Rich Text
            $categoria_id = filter_input(INPUT_POST, 'categoria_id', FILTER_VALIDATE_INT);
            $autor_id = 1; // ID padrão do administrador logado (será dinâmico com a sessão)

            // Validação de segurança para o upload de documentos PDF (Limite de 5MB a 10MB)
            if (isset($_FILES['pdf_anexo']) && $_FILES['pdf_anexo']['error'] === UPLOAD_ERR_OK) {
                $tamanhoMaximo = 10 * 1024 * 1024; // 10 MB em bytes
                
                if ($_FILES['pdf_anexo']['size'] > $tamanhoMaximo) {
                    die("Erro de infraestrutura: O arquivo PDF enviado excede o limite máximo recomendado de 10 MB.");
                }
                
                $extensao = strtolower(pathinfo($_FILES['pdf_anexo']['name'], PATHINFO_EXTENSION));
                if ($extensao !== 'pdf') {
                    die("Erro: Apenas arquivos em formato PDF são permitidos para anexos científicos.");
                }

                // Caminho físico para salvar o arquivo no servidor
                $nomeArquivo = uniqid('pdf_') . '.' . $extensao;
                $caminhoDestino = __DIR__ . '/../uploads/pdfs/' . $nomeArquivo;
                
                move_uploaded_file($_FILES['pdf_anexo']['tmp_name'], $caminhoDestino);
            }

            // Instancia o Objeto Model com os dados validados
            $novoPost = new Post($titulo, $conteudo, $categoria_id, $autor_id);

            // Executa a persistência utilizando o DAO com Prepared Statements
            if ($this->postDAO->inserir($novoPost)) {
                // Redireciona de volta ao painel administrativo com sucesso
                header("Location: ../views/admin/index.php?status=sucesso");
                exit;
            } else {
                echo "Erro crítico: Não foi possível registrar a publicação no MariaDB.";
            }
        }
    }

    // Método para listar todos os posts na Central de Conteúdo pública
    public function listarPublicos() {
        return $this->postDAO->listarTodos();
    }

    // Método para recuperar os dados de um artigo específico
    public function exibirArtigo($id) {
        $idValidado = filter_var($id, FILTER_VALIDATE_INT);
        if (!$idValidado) {
            return null;
        }
        return $this->postDAO->buscarPorId($idValidado);
    }

    // Processa a atualização de um post existente
    public function editar($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $titulo = filter_input(INPUT_POST, 'titulo', FILTER_SANITIZE_SPECIAL_CHARS);
            $conteudo = $_POST['conteudo'] ?? '';
            $categoria_id = filter_input(INPUT_POST, 'categoria_id', FILTER_VALIDATE_INT);
            
            $post = new Post($titulo, $conteudo, $categoria_id, null);
            $post->setId($id);

            // Validação de novo anexo PDF, se enviado
            if (isset($_FILES['pdf_anexo']) && $_FILES['pdf_anexo']['error'] === UPLOAD_ERR_OK) {
                $extensao = strtolower(pathinfo($_FILES['pdf_anexo']['name'], PATHINFO_EXTENSION));
                if ($extensao === 'pdf') {
                    $nomeArquivo = uniqid('pdf_') . '.' . $extensao;
                    move_uploaded_file($_FILES['pdf_anexo']['tmp_name'], __DIR__ . '/../uploads/pdfs/' . $nomeArquivo);
                    $post->setPdfAnexo($nomeArquivo);
                }
            }

            if ($this->postDAO->atualizar($post)) {
                header("Location: index.php?status=atualizado");
                exit;
            } else {
                echo "Erro crítico: Não foi possível atualizar a publicação.";
            }
        }
    }

    // Processa a exclusão de um post
    public function deletar($id) {
        $idValidado = filter_var($id, FILTER_VALIDATE_INT);
        if ($idValidado && $this->postDAO->excluir($idValidado)) {
            header("Location: index.php?status=excluido");
            exit;
        } else {
            echo "Erro crítico: Não foi possível eliminar a publicação.";
        }
    }
}