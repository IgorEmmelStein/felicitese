1. Construção de Componentes e Interface
Estrutura de Pastas: Todos os componentes devem ser salvos diretamente dentro da pasta components/, sem a criação de subpastas.

Código Limpo: Nunca comente código. Remova trechos não utilizados, rascunhos e explicações inline.

Sombras Proibidas: É estritamente proibido o uso de sombras (shadow, box-shadow, drop-shadow) em qualquer elemento (cards, botões, containers ou seções). O design deve ser completamente flat e limpo.

Imagens Sempre Arredondadas: Todas as imagens, fotografias e ilustrações devem SEMPRE utilizar bordas arredondadas (rounded-lg, rounded-xl, rounded-2xl ou equivalente).

Paleta de Cores Estrita:

Proibição de Variações e Opacidade: Nunca utilize tons intermediários de cinza/cor (ink-200, ink-500), nunca use opacidade fracionada (text-ink-50/80, /70, etc.) e nunca utilize text-white ou text-black. Se um texto precisa parecer mais suave, ele ainda deve manter ink-50 ou ink-950.

Padronização Tipográfica:

Tags de título (h1, h2, h3, etc.) devem obrigatoriamente usar as classes de utilitário t-* para tipografia, mantendo na tag apenas as definições de cor e alinhamento.

Parágrafos (<p>) devem obrigatoriamente utilizar a classe p-base.

Eyebrows e Badges: Seções com títulos em tema claro devem exibir uma eyebrow / badge superior em estilo pílula arredondada (ex: fundo lilás/secundário com texto em caixa alta, como EXTENSÃO • EDIÇÃO 2026).

Alternância de Temas:

O Hero fica fora da sequência de temas.

O primeiro componente após o Hero deve sempre utilizar o tema claro (light theme).

A partir dele, mantenha uma alternância estrita e contínua: light, dark, light, dark até a última seção ou formulário da página.

Componentes que são permanentemente claros e que gerem duas seções claras consecutivas devem ser separados por um componente ImageCarousel no tema escuro (theme: "dark") e com id único.

1. Redação de Conteúdo (Copywriting)
Fonte da Verdade: Utilize apenas documentações oficiais do projeto, notas de reuniões, transcrições ou o site base oficial como fonte de informações. Nunca utilize textos vindos de templates genéricos.

Tom Humano e Institucional:

Evite linguagem robótica, jargões de IA ou estruturas de frases repetitivas (como "isso não é X, é Y").

O texto deve soar acolhedor, transparente e direto, como um membro da equipe da instituição conversando com o leitor.

Nunca utilize metalinguagem no texto final.

Escaneabilidade com Negrito: Todos os parágrafos devem conter pelo menos uma palavra ou trecho relevante destacado em negrito (<strong>). Nenhum parágrafo deve ficar sem esse destaque.

Proibição de Travessões: É estritamente proibido o uso de travessões (— ou --) na redação.

Eliminação de Dúvidas Internas: Notas de auditoria ou incertezas de bastidores (ex: "solicite estimativa", "sujeito a confirmação", "confirmar valores", "sujeito a alterações") jamais devem virar texto final. Apresente o benefício em linguagem clara e deixe que o botão de ação (CTA) inicie o contato.

Volume de Texto: Garanta densidade adequada em componentes com texto e imagem (mínimo de 2 parágrafos desenvolvidos, lista com pontos de destaque e 1 CTA claro).

Descrição do Hero: O Hero deve conter uma descrição polida de 1 a 2 frases explicando o benefício principal ao visitante. Não construa descrições unindo códigos, preços ou metadados separados por pontos.

3. Boas Práticas Gerais e Formatação de Títulos
Uso Obrigatório da Tag <br> em Títulos: Nas propriedades e tags de títulos (h1, h2, h3, etc.), utilize a tag <br> para quebrar as linhas manualmente, garantindo um volume e equilíbrio visual similar de texto em cada linha.

Proibição da Tag <strong> em Títulos: NUNCA utilize a tag <strong> em propriedades de título (headings). O uso de negrito é exclusivo para parágrafos e textos explicativos.