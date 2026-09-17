<?php
/**
 * Componente Base: Footer (Rodapé)
 * Projeto: Felicite-se
 */
?>
    </main>

    <footer class="site-footer">
        <div class="container footer-container">
            <div class="footer-grid">
                
                <!-- Coluna 1: Marca, Missão e Redes -->
                <div class="footer-col brand-col">
                    <a href="<?= (defined('BASE_URL') ? BASE_URL : '') ?>index.php" class="brand-logo footer-logo" title="Felicite-se - Início">
                        <img src="<?= (defined('BASE_URL') ? BASE_URL : '') ?>assets/images/felicitese-logo-2.png" alt="Logo Felicite-se" class="brand-logo-img">
                        <span class="brand-text">Felicite<span class="brand-dash">-</span><span class="brand-accent">se</span></span>
                    </a>
                    <p class="footer-mission">
                        Promovendo saúde psicológica e inteligência emocional para jovens, com acolhimento e conteúdo de confiança.
                    </p>
                    <div class="footer-socials">
                        <a href="#" class="social-circle-btn" title="Threads / Instagram">@</a>
                        <a href="#" class="social-circle-btn" title="Chat / Comunidade">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                            </svg>
                        </a>
                        <a href="mailto:contato@felicite-se.org" class="social-circle-btn" title="E-mail de Contato">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                                <polyline points="22,6 12,13 2,6"></polyline>
                            </svg>
                        </a>
                    </div>
                </div>

                <!-- Coluna 2: Navegação -->
                <div class="footer-col">
                    <h4 class="footer-col-title">Navegação</h4>
                    <ul class="footer-links">
                        <li><a href="<?= (defined('BASE_URL') ? BASE_URL : '') ?>index.php">Início</a></li>
                        <li><a href="<?= (defined('BASE_URL') ? BASE_URL : '') ?>blog.php">Central de Conteúdo</a></li>
                        <li><a href="<?= (defined('BASE_URL') ? BASE_URL : '') ?>login.php">Área do Membro</a></li>
                    </ul>
                </div>

                <!-- Coluna 3: Contato -->
                <div class="footer-col">
                    <h4 class="footer-col-title">Contato</h4>
                    <ul class="footer-contact-list">
                        <li><a href="mailto:contato@felicite-se.org">contato@felicite-se.org</a></li>
                        <li>IFSul Câmpus Venâncio Aires</li>
                        <li>Venâncio Aires, RS</li>
                    </ul>
                </div>

            </div>

            <!-- Rodapé inferior com copyright -->
            <div class="footer-bottom">
                <p>&copy; 2026 Clube Felicite-se &middot; Projeto educacional de saúde mental</p>
            </div>
        </div>
    </footer>

</body>
</html>