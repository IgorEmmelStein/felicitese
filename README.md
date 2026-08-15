## Organização do projeto

/clube-felicite-se
│
├── /assets                # CSS, JS e Brand Kit
│   ├── /css
│   └── /img
│
├── /config                # Configuração global (conexao.php com PDO)
├── /includes              # Componentes reutilizáveis (header, footer)
├── /models                # Classes de Entidade (Post.php, Usuario.php)
├── /dao                   # Classes de Persistência (PostDAO.php, UsuarioDAO.php)
├── /controllers           # Controladores de Ação (PostController.php, AuthController.php)
├── /views                 # Arquivos de Interface (públicos e /admin)
│
├── index.php              # Ponto de entrada público / Roteador simples
└── .htaccess              # Opcional para amigabilizar rotas, se necessário