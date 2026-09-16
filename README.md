# Felicite-se - IFSul Campus Venâncio Aires

Portal e Central de Conteúdo sobre Saúde Mental e Produções Acadêmicas.

---

## 🚀 Como Iniciar o Projeto com Docker (Sem XAMPP)

### Passo 1: Abrir o Docker Desktop
1. No menu Iniciar do Windows, abra o aplicativo **Docker Desktop**.
2. Aguarde até que o ícone da baleia na barra de tarefas (perto do relógio) pare de animar e indique que o Docker está em execução (*Engine running*).

### Passo 2: Subir os Containers
No terminal (PowerShell, CMD ou terminal do VS Code), dentro da pasta do projeto:

```powershell
docker compose up -d
```

> **Dica:** O parâmetro `-d` roda os containers em segundo plano, liberando o terminal.

---

## 🔗 Links de Acesso Rápido

Com os containers em execução, utilize os links abaixo para navegar no sistema:

| Seção | URL no Navegador | Credenciais de Acesso |
| :--- | :--- | :--- |
| **🌐 Site Público (Blog)** | [http://localhost:8080](http://localhost:8080) *(ou [/blog.php](http://localhost:8080/blog.php))* | Acesso livre |
| **🔒 Login Administrativo** | [http://localhost:8080/login.php](http://localhost:8080/login.php) | **E-mail:** `admin@ifsul.edu.br`<br>**Senha:** `admin123` |
| **📊 Painel Admin (Dashboard)** | [http://localhost:8080/views/admin/index.php](http://localhost:8080/views/admin/index.php) | Requer login prévio |
| **➕ Criar Nova Publicação** | [http://localhost:8080/views/admin/post-criar.php](http://localhost:8080/views/admin/post-criar.php) | Requer login prévio |
| **🗄️ phpMyAdmin (Banco de Dados)** | [http://localhost:8081](http://localhost:8081) | **Usuário:** `root`<br>**Senha:** `root` |

---

## 🛑 Comandos Úteis do Docker

### Parar os containers
```powershell
docker compose stop
```

### Parar e remover os containers
```powershell
docker compose down
```

### Ver logs em tempo real (caso precise debugar)
```powershell
docker compose logs -f
```

### Reiniciar os containers
```powershell
docker compose restart
```

---

## 📂 Organização do Projeto

```text
/felicitese
│
├── /assets                # CSS, Imagens e Brand Kit
│   ├── /css
│   └── /images
│
├── /config                # Conexão PDO com suporte dinâmico a Docker e XAMPP
├── /controllers           # Controladores de Ação (PostController.php)
├── /dao                   # Camada de Persistência (PostDAO.php)
├── /docker                # Scripts de inicialização do banco (init.sql)
├── /includes              # Componentes reutilizáveis (header, footer)
├── /models                # Modelos de Entidade (Post.php)
├── /uploads               # Diretório de uploads de mídias e PDFs
├── /views                 # Telas do Painel Administrativo (/views/admin/)
│
├── Dockerfile             # Imagem PHP 8.2 + Apache
├── docker-compose.yml     # Orquestração (App, MariaDB, phpMyAdmin)
├── index.php              # Ponto de entrada / Redirecionamento
├── blog.php               # Central de Conteúdo
├── artigo.php             # Visualização individual de artigo
├── login.php              # Login e autenticação
└── README.md              # Documentação e instruções de execução
```