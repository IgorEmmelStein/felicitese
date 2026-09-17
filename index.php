<?php
/**
 * Redirecionamento da raiz do projeto para a Central de Conteúdo (Blog)
 * Projeto: Felicite-se - IFSul
 * Página Inicial (Landing Page)
 * Projeto: Felicite-se - IFSul Câmpus Venâncio Aires
 */
header("Location: blog.php");
exit;
require_once __DIR__ . '/config/conexao.php';
require_once __DIR__ . '/controllers/PostController.php';

$pageTitle = "Felicite-se - Saúde Mental e Bem-Estar no Cotidiano Escolar";
$mainClass = "landing-main";

// Instancia o controlador para carregar as 3 últimas publicações
$controller = new PostController($pdo);
$ultimosPosts = $controller->listarRecentes(3);

/**
 * Retorna a classe CSS de cor para o badge sobreposto na imagem
 */
function getBadgeClass($nomeCategoria) {
    $nome = mb_strtolower(trim($nomeCategoria ?? ''));
    if (strpos($nome, 'artigo') !== false) return 'badge-artigos';
    if (strpos($nome, 'evento') !== false || strpos($nome, 'oficina') !== false) return 'badge-eventos';
    if (strpos($nome, 'notícia') !== false || strpos($nome, 'noticia') !== false) return 'badge-noticias';
    if (strpos($nome, 'psicologia') !== false || strpos($nome, 'mente') !== false || strpos($nome, 'psico') !== false) return 'badge-psico';
    if (strpos($nome, 'vivência') !== false || strpos($nome, 'vivencia') !== false || strpos($nome, 'lúdica') !== false) return 'badge-vivencias';
    return 'badge-artigos';
}

/**
 * Estima o tempo de leitura dinamicamente baseado no conteúdo
 */
function getTempoLeitura($texto, $id) {
    $temposFixos = [
        1 => 6,
        2 => 3,
        3 => 4,
        4 => 5,
        5 => 3,
    ];
    if (isset($temposFixos[$id])) {
        return $temposFixos[$id];
    }
    $palavras = str_word_count(strip_tags($texto));
    return max(3, min(10, ceil($palavras / 50)));
}

include_once __DIR__ . '/includes/header.php';
?>

<!-- 1. HERO SECTION -->
<section class="hero-landing">
    <div class="container">
        <div class="hero-landing-content">
            <div class="hero-badge-pill">
                <span>🌱</span>
                <span>Iniciativa de Acolhimento &middot; IFSul Câmpus Venâncio Aires</span>
            </div>
            
            <h1 class="hero-landing-title">
                Cuidar da mente é cultivar o seu <span class="hero-title-highlight">melhor</span>.
            </h1>
            
            <p class="hero-landing-subtitle">
                Um espaço seguro e acolhedor dedicado à promoção da saúde emocional, inteligência socioemocional e apoio mútuo para os jovens e a comunidade escolar.
            </p>

            <div class="hero-actions-row">
                <a href="#sobre-o-projeto" class="btn-hero-primary">
                    <span>Conhecer o Projeto</span>
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <polyline points="19 12 12 19 5 12"></polyline>
                    </svg>
                </a>
                <a href="<?= (defined('BASE_URL') ? BASE_URL : '') ?>blog.php" class="btn-hero-secondary">
                    <span>Acessar Central de Conteúdo</span>
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                        <polyline points="12 5 19 12 12 19"></polyline>
                    </svg>
                </a>
            </div>

            <div class="hero-highlights-strip">
                <div class="hero-stat-item">
                    <div class="hero-stat-icon">💬</div>
                    <span>Rodas de Conversa & Oficinas</span>
                </div>
                <div class="hero-stat-item">
                    <div class="hero-stat-icon">🤝</div>
                    <span>Escuta Empática & Acolhimento</span>
                </div>
                <div class="hero-stat-item">
                    <div class="hero-stat-icon">📚</div>
                    <span>Psicoeducação com Base Científica</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 2. O QUE É O PROJETO COM SLIDER DE FOTOS -->
<section class="about-section" id="sobre-o-projeto">
    <div class="container">
        <div class="about-grid">
            
            <!-- Coluna Texto Descritivo -->
            <div class="about-text-col">
                <span class="section-tag-label">Sobre a Iniciativa</span>
                <h2 class="about-title">
                    Cultivando o direito à felicidade e ao bem-estar no cotidiano dos jovens
                </h2>
                
                <p class="about-text">
                    O projeto <strong>Felicite-se</strong> surgiu no Instituto Federal Sul-rio-grandense (IFSul) - Câmpus Venâncio Aires com um propósito essencial: ser um porto seguro para os estudantes em suas transformações emocionais, sociais e acadêmicas.
                </p>
                
                <p class="about-text">
                    Acreditamos que saúde psicológica e rendimento escolar andam lado a lado. Por isso, desenvolvemos momentos de vivência, diálogo e conexão que desmistificam tabus e constroem redes reais de apoio no ambiente escolar.
                </p>

                <ul class="about-features-list">
                    <li class="about-feature-item">
                        <svg class="about-check-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                        <span><strong>Ambiente sem julgamentos:</strong> rodas de conversa onde cada voz, angústia e conquista são ouvidas e acolhidas.</span>
                    </li>
                    <li class="about-feature-item">
                        <svg class="about-check-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                        <span><strong>Desenvolvimento socioemocional:</strong> práticas de autorregulação, inteligência emocional e gestão de ansiedade.</span>
                    </li>
                    <li class="about-feature-item">
                        <svg class="about-check-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                        <span><strong>Integração e comunidade:</strong> aproximando estudantes, professores, orientadores e a comunidade regional.</span>
                    </li>
                </ul>

                <a href="<?= (defined('BASE_URL') ? BASE_URL : '') ?>blog.php?categoria=2" class="btn-saiba-mais">
                    <span>Conhecer nossas Oficinas e Eventos</span>
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                        <polyline points="12 5 19 12 12 19"></polyline>
                    </svg>
                </a>
            </div>

            <!-- Coluna Carrossel / Slider de Fotos -->
            <div class="about-slider-col">
                <div class="slider-wrapper" id="projectSlider">
                    <div class="slider-container">
                        <div class="slider-track" id="sliderTrack">
                            
                            <!-- Slide 1: MOVACI Minicursos -->
                            <div class="slider-slide">
                                <img src="<?= (defined('BASE_URL') ? BASE_URL : '') ?>assets/images/estudantes/felicitese-ifsul-movaci-oficinas-minicursos.webp" alt="Oficinas e Minicursos MOVACI IFSul - Projeto Felicite-se" loading="lazy">
                                <div class="slider-caption-overlay">
                                    <span class="slider-caption-tag">Ação Pedagógica</span>
                                    <h4 class="slider-caption-text">Oficinas e minicursos práticos na MOVACI - IFSul</h4>
                                </div>
                            </div>

                            <!-- Slide 2: Estações Vivenciais -->
                            <div class="slider-slide">
                                <img src="<?= (defined('BASE_URL') ? BASE_URL : '') ?>assets/images/estudantes/felicitese-estacoes-vivenciais-psicologia-positiva-dinamicas.webp" alt="Estações Vivenciais de Psicologia Positiva e Dinâmicas em Grupo" loading="lazy">
                                <div class="slider-caption-overlay">
                                    <span class="slider-caption-tag">Dinâmica em Grupo</span>
                                    <h4 class="slider-caption-text">Estações Vivenciais: psicologia positiva e diálogo aberto</h4>
                                </div>
                            </div>

                            <!-- Slide 3: Fachada do Câmpus -->
                            <div class="slider-slide">
                                <img src="<?= (defined('BASE_URL') ? BASE_URL : '') ?>assets/images/estudantes/felicitese-premiacao-fachada-ifsul-campus-venancio-aires.webp" alt="Equipe de Bolsistas e Orientadoras no IFSul Câmpus Venâncio Aires" loading="lazy">
                                <div class="slider-caption-overlay">
                                    <span class="slider-caption-tag">Câmpus Venâncio Aires</span>
                                    <h4 class="slider-caption-text">Pesquisa e extensão com reconhecimento acadêmico</h4>
                                </div>
                            </div>

                            <!-- Slide 4: Congresso Felicidar -->
                            <div class="slider-slide">
                                <img src="<?= (defined('BASE_URL') ? BASE_URL : '') ?>assets/images/estudantes/felicitese-congresso-felicidar-bandeira-ifsul-venancio-aires.webp" alt="Estudantes e Orientadoras no Congresso Felicidar no Rio de Janeiro" loading="lazy">
                                <div class="slider-caption-overlay">
                                    <span class="slider-caption-tag">Congresso Felicidar</span>
                                    <h4 class="slider-caption-text">Levando a felicidade aplicada e a ciência da mente além das fronteiras</h4>
                                </div>
                            </div>

                            <!-- Slide 5: Ação Social Parceiros da Esperança -->
                            <div class="slider-slide">
                                <img src="<?= (defined('BASE_URL') ? BASE_URL : '') ?>assets/images/estudantes/felicitese-acao-social-parceiros-da-esperanca-estudantes.webp" alt="Ação Comunitária e Social com Crianças e Jovens" loading="lazy">
                                <div class="slider-caption-overlay">
                                    <span class="slider-caption-tag">Comunidade</span>
                                    <h4 class="slider-caption-text">Ação solidária e voluntariado com os Parceiros da Esperança</h4>
                                </div>
                            </div>

                            <!-- Slide 6: Vivência e Bem-estar ao ar livre -->
                            <div class="slider-slide">
                                <img src="<?= (defined('BASE_URL') ? BASE_URL : '') ?>assets/images/estudantes/felicitese-vivencia-ludica-equilibrio-bem-estar-natureza.webp" alt="Vivência Lúdica de Autocuidado, Equilíbrio e Contato com a Natureza" loading="lazy">
                                <div class="slider-caption-overlay">
                                    <span class="slider-caption-tag">Autocuidado</span>
                                    <h4 class="slider-caption-text">Vivências lúdicas integrando equilíbrio físico e mental</h4>
                                </div>
                            </div>

                        </div>

                        <!-- Botões de Navegação -->
                        <button type="button" class="slider-btn prev" id="sliderPrev" aria-label="Foto anterior">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="15 18 9 12 15 6"></polyline>
                            </svg>
                        </button>
                        <button type="button" class="slider-btn next" id="sliderNext" aria-label="Próxima foto">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="9 18 15 12 9 6"></polyline>
                            </svg>
                        </button>

                        <!-- Indicadores / Dots -->
                        <div class="slider-indicators" id="sliderDots"></div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- 3. SEÇÃO SOBRE SAÚDE PSICOLÓGICA -->
<section class="psico-section">
    <div class="container">
        
        <div class="section-header-centered">
            <span class="section-tag-label">Equilíbrio & Conexão</span>
            <h2 class="section-main-title">Por que falar sobre Saúde Mental na Juventude?</h2>
            <p class="section-main-subtitle">
                O período estudantil traz desafios intensos, transições e escolhas para o futuro. Fortalecer a saúde emocional é a chave para aprender com confiança e viver com plenitude.
            </p>
        </div>

        <div class="psico-cards-grid">
            
            <!-- Card 1: Autocuidado -->
            <div class="psico-card">
                <div class="psico-icon-box heart">
                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                    </svg>
                </div>
                <h3 class="psico-card-title">Autocuidado & Prevenção</h3>
                <p class="psico-card-desc">
                    Reconhecer os próprios limites, priorizar o descanso, cultivar hobbies e adotar pequenas pausas são hábitos que blindam a mente contra a exaustão.
                </p>
            </div>

            <!-- Card 2: Inteligência Emocional -->
            <div class="psico-card">
                <div class="psico-icon-box brain">
                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <path d="M12 2a14.5 14.5 0 0 0 0 20 14.5 14.5 0 0 0 0-20"></path>
                        <path d="M2 12h20"></path>
                    </svg>
                </div>
                <h3 class="psico-card-title">Inteligência Emocional</h3>
                <p class="psico-card-desc">
                    Nomear sentimentos — ansiedade, frustração, alegria e medo — ajuda a responder às pressões e provas com clareza, empatia e assertividade.
                </p>
            </div>

            <!-- Card 3: Rede de Apoio -->
            <div class="psico-card">
                <div class="psico-icon-box support">
                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                    </svg>
                </div>
                <h3 class="psico-card-title">Rede de Apoio & Escuta</h3>
                <p class="psico-card-desc">
                    Você não precisa carregar tudo nos ombros sozinho. O diálogo entre colegas, professores e equipe pedagógica constrói uma verdadeira rede de proteção.
                </p>
            </div>

            <!-- Card 4: Psicoeducação Científica -->
            <div class="psico-card">
                <div class="psico-icon-box science">
                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                        <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                        <line x1="12" y1="6" x2="16" y2="6"></line>
                        <line x1="12" y1="10" x2="16" y2="10"></line>
                    </svg>
                </div>
                <h3 class="psico-card-title">Psicoeducação Científica</h3>
                <p class="psico-card-desc">
                    Desmistificamos mitos através de estudos da psicologia positiva e neurociência, entregando ferramentas práticas aplicadas à vida real.
                </p>
            </div>

        </div>

    </div>
</section>

<!-- 4. SEÇÃO ÚLTIMAS PUBLICAÇÕES -->
<section class="home-posts-section">
    <div class="container">
        
        <div class="posts-header-row">
            <div>
                <span class="section-tag-label">Conteúdo Atualizado</span>
                <h2 class="section-main-title" style="margin-bottom: 0;">Últimas da Central de Conteúdo</h2>
            </div>
            <div>
                <a href="<?= (defined('BASE_URL') ? BASE_URL : '') ?>blog.php" class="btn-outline-pill">
                    <span>Ver todas as publicações</span>
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                        <polyline points="12 5 19 12 12 19"></polyline>
                    </svg>
                </a>
            </div>
        </div>

        <div class="home-posts-grid">
            <?php if (!empty($ultimosPosts)): ?>
                <?php foreach ($ultimosPosts as $post): ?>
                    <?php
                    $temImagem = !empty($post['imagem']) && file_exists(__DIR__ . '/uploads/imagens/' . $post['imagem']);
                    $srcImagem = $temImagem
                        ? (defined('BASE_URL') ? BASE_URL : '') . 'uploads/imagens/' . htmlspecialchars($post['imagem'])
                        : (defined('BASE_URL') ? BASE_URL : '') . 'assets/images/fallback-image.jpeg';

                    $badgeClass = getBadgeClass($post['categoria_nome'] ?? 'Artigos');
                    $tempoLeitura = getTempoLeitura($post['conteudo'] ?? '', $post['id']);
                    $dataFormatada = date('d \d\e M, Y', strtotime($post['data_criacao']));
                    $resumoTexto = mb_substr(strip_tags($post['conteudo']), 0, 115) . '...';
                    ?>
                    <article class="home-post-card">
                        <div class="home-post-thumb-box">
                            <span class="home-post-badge <?= $badgeClass ?>">
                                <?= htmlspecialchars($post['categoria_nome'] ?? 'Geral') ?>
                            </span>
                            <a href="<?= (defined('BASE_URL') ? BASE_URL : '') ?>artigo.php?id=<?= $post['id'] ?>">
                                <img src="<?= $srcImagem ?>" alt="<?= htmlspecialchars($post['titulo']) ?>" loading="lazy">
                            </a>
                        </div>
                        <div class="home-post-body">
                            <div class="home-post-meta">
                                <span><?= $dataFormatada ?></span>
                                <span>&bull;</span>
                                <span><?= $tempoLeitura ?> min de leitura</span>
                            </div>
                            <h3 class="home-post-title">
                                <a href="<?= (defined('BASE_URL') ? BASE_URL : '') ?>artigo.php?id=<?= $post['id'] ?>">
                                    <?= htmlspecialchars($post['titulo']) ?>
                                </a>
                            </h3>
                            <p class="home-post-excerpt">
                                <?= htmlspecialchars($resumoTexto) ?>
                            </p>
                            <a href="<?= (defined('BASE_URL') ? BASE_URL : '') ?>artigo.php?id=<?= $post['id'] ?>" class="home-post-link">
                                <span>Ler publicação completa</span>
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="5" y1="12" x2="19" y2="12"></line>
                                    <polyline points="12 5 19 12 12 19"></polyline>
                                </svg>
                            </a>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php else: ?>
                <div style="grid-column: 1 / -1; text-align: center; padding: 40px; background-color: #f8fafc; border-radius: 16px; color: var(--texto-secundario);">
                    <p>Nenhuma publicação encontrada no momento. Em breve novos conteúdos serão disponibilizados!</p>
                </div>
            <?php endif; ?>
        </div>

    </div>
</section>

<!-- 5. SEÇÃO CTA BANNER -->
<section class="cta-banner-section">
    <div class="container">
        <div class="cta-banner-box">
            <div class="cta-banner-content">
                <span class="cta-banner-tag">Central de Conteúdo Aberta</span>
                <h2 class="cta-banner-title">
                    Explore todo o nosso acervo de artigos, eventos e novidades
                </h2>
                <p class="cta-banner-desc">
                    Quer conferir todas as oficinas anteriores, leituras recomendadas, editais acadêmicos e registros do projeto? Acesse a Central de Conteúdo e fique por dentro.
                </p>
                <div class="cta-banner-btns">
                    <a href="<?= (defined('BASE_URL') ? BASE_URL : '') ?>blog.php" class="btn-cta-white">
                        <span>Acessar Central de Conteúdo</span>
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                            <polyline points="12 5 19 12 12 19"></polyline>
                        </svg>
                    </a>
                    <a href="<?= (defined('BASE_URL') ? BASE_URL : '') ?>login.php" class="btn-cta-glass">
                        <span>Área do Membro</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Script do Slider Interativo -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const track = document.getElementById('sliderTrack');
    const slides = document.querySelectorAll('.slider-slide');
    const prevBtn = document.getElementById('sliderPrev');
    const nextBtn = document.getElementById('sliderNext');
    const dotsContainer = document.getElementById('sliderDots');
    const sliderWrapper = document.getElementById('projectSlider');

    if (!track || slides.length === 0) return;

    let currentIndex = 0;
    const totalSlides = slides.length;
    let autoPlayTimer = null;

    // Cria os dots dinamicamente
    slides.forEach((_, idx) => {
        const dot = document.createElement('button');
        dot.classList.add('slider-dot');
        dot.setAttribute('aria-label', 'Ir para foto ' + (idx + 1));
        if (idx === 0) dot.classList.add('active');
        dot.addEventListener('click', () => {
            goToSlide(idx);
            resetAutoPlay();
        });
        dotsContainer.appendChild(dot);
    });

    const dots = document.querySelectorAll('.slider-dot');

    function updateSlider() {
        track.style.transform = `translateX(-${currentIndex * 100}%)`;
        dots.forEach((dot, idx) => {
            dot.classList.toggle('active', idx === currentIndex);
        });
    }

    function goToSlide(index) {
        currentIndex = (index + totalSlides) % totalSlides;
        updateSlider();
    }

    function nextSlide() {
        goToSlide(currentIndex + 1);
    }

    function prevSlide() {
        goToSlide(currentIndex - 1);
    }

    if (nextBtn) {
        nextBtn.addEventListener('click', () => {
            nextSlide();
            resetAutoPlay();
        });
    }

    if (prevBtn) {
        prevBtn.addEventListener('click', () => {
            prevSlide();
            resetAutoPlay();
        });
    }

    // Autoplay com pausa ao passar o mouse
    function startAutoPlay() {
        if (!autoPlayTimer) {
            autoPlayTimer = setInterval(nextSlide, 5000);
        }
    }

    function stopAutoPlay() {
        if (autoPlayTimer) {
            clearInterval(autoPlayTimer);
            autoPlayTimer = null;
        }
    }

    function resetAutoPlay() {
        stopAutoPlay();
        startAutoPlay();
    }

    if (sliderWrapper) {
        sliderWrapper.addEventListener('mouseenter', stopAutoPlay);
        sliderWrapper.addEventListener('mouseleave', startAutoPlay);
    }

    // Suporte a gestos touch (swipe no mobile)
    let touchStartX = 0;
    let touchEndX = 0;

    sliderWrapper.addEventListener('touchstart', function(e) {
        touchStartX = e.changedTouches[0].screenX;
        stopAutoPlay();
    }, { passive: true });

    sliderWrapper.addEventListener('touchend', function(e) {
        touchEndX = e.changedTouches[0].screenX;
        if (touchStartX - touchEndX > 50) {
            nextSlide();
        } else if (touchEndX - touchStartX > 50) {
            prevSlide();
        }
        startAutoPlay();
    }, { passive: true });

    startAutoPlay();
});
</script>

<?php
include_once __DIR__ . '/includes/footer.php';
?>
