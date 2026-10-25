<?php
$heroImages = [
    'assets/images/estudantes/felicitese-equipe-sala-com-jogos.jpeg',
    'assets/images/estudantes/felicitese-apresentacao-estacoes-vivenciais-rodada-oficinas.webp',
    'assets/images/estudantes/felicitese-acao-social-parceiros-da-esperanca-estudantes.webp',
    'assets/images/estudantes/felicitese-aluna-orientadora-congresso-felicidade-aplicada.webp',
    'assets/images/estudantes/felicitese-apresentacao-cultivando-direito-felicidade-ifsul.webp',
    'assets/images/estudantes/felicitese-congresso-felicidar-bandeira-ifsul-venancio-aires.webp',
    'assets/images/estudantes/felicitese-debate-saude-psicologica-bem-estar-educacao.webp',
    'assets/images/estudantes/felicitese-equipe-estudantes-cristo-redentor-rio-de-janeiro.webp',
    'assets/images/estudantes/felicitese-equipe-movaci-ifsul-venancio-aires.webp',
    'assets/images/estudantes/felicitese-estacoes-vivenciais-psicologia-positiva-dinamicas.webp',
    'assets/images/estudantes/felicitese-grupo-pesquisa-mirante-rio-de-janeiro.webp',
];
?>
<section class="relative py-20 lg:py-28 overflow-hidden border-b border-slate-200/80 bg-slate-950 min-h-[90vh] flex items-center justify-center">

    <div class="absolute inset-0 w-full h-full overflow-hidden pointer-events-none">
        <?php foreach ($heroImages as $index => $image): ?>
            <img
                src="<?= $image ?>"
                alt="projeto Felicite-se"
                class="hero-slide absolute inset-0 w-full h-full object-cover object-center <?= $index === 0 ? 'active' : '' ?>"
                loading="<?= $index === 0 ? 'eager' : 'lazy' ?>">
        <?php endforeach; ?>
    </div>

    <div class="absolute inset-0 bg-gradient-to-r from-slate-950 via-slate-950/50 to-slate-950/10 z-10"></div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 z-20 w-full">
        <div class="flex flex-col lg:flex-row items-center justify-between gap-10 lg:gap-12">

            <div class="w-full lg:w-1/2 space-y-6 text-center lg:text-left">
                <div>
                    <span class="inline-flex items-center gap-2 bg-purple-950/80 text-purple-300 border border-purple-800/60 px-4 py-1.5 rounded-full text-xs font-bold uppercase tracking-wider backdrop-blur-sm">
                        PSICOLOGIA POSITIVA
                    </span>
                </div>

                <h1 class="t-h1 text-white">
                    Desenvolva o seu potencial e cultive o <span class="text-purple-400">bem-estar</span> no quotidiano escolar
                </h1>

                <p class="p-base text-white max-w-2xl mx-auto lg:mx-0">
                    Explore conteúdos, pesquisas e oficinas focadas no desenvolvimento da <strong>inteligência emocional</strong>, fortalecimento das forças de caráter e promoção da saúde mental para estudantes e comunidade.
                </p>

                <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start pt-2">
                    <a href="#artigos" class="inline-flex justify-center items-center px-7 py-3.5 rounded-full font-bold text-white bg-purple hover:bg-purple-hover shadow-lg shadow-purple/20 transition-all">
                        Explorar Artigos
                    </a>
                    <a href="sobre.php" class="inline-flex justify-center items-center px-7 py-3.5 rounded-full font-bold text-slate-900 bg-white hover:bg-slate-100 transition-all">
                        Conheça a Nossa História
                    </a>
                </div>
            </div>

            <div class="w-full lg:w-1/2 flex justify-center lg:justify-end">
                <img src="../../assets/images/felicitese-logo.png" alt="Logo Felicite-se" class="max-w-xs sm:max-w-sm lg:max-w-md h-auto object-contain" />
            </div>

        </div>
    </div>
</section>

<style>
    .hero-slide {
        opacity: 0;
        transform: scale(1);
        transition: opacity 1.5s ease-in-out, transform 10s linear;
    }

    .hero-slide.active {
        opacity: 0.35;
        transform: scale(1.15);
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const slides = document.querySelectorAll('.hero-slide');
        if (!slides.length) return;
        let current = 0;

        setInterval(() => {
            slides[current].classList.remove('active');
            current = (current + 1) % slides.length;
            slides[current].classList.add('active');
        }, 10000);
    });
</script>