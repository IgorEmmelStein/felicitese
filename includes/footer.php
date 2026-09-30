<?php
$baseUrl = defined('BASE_URL') ? BASE_URL : '';
?>
    </main>

    <footer class="bg-white border-t border-slate-200/80 py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-12 pb-12 border-b border-slate-200/80">
                
                <div class="md:col-span-6 space-y-5">
                    <a href="<?= $baseUrl ?>index.php" class="flex items-center gap-3">
                        <img src="<?= $baseUrl ?>assets/images/felicitese-logo-2.png" alt="Logo Felicite-se" class="h-10 w-auto">
                        <span class="text-xl font-extrabold tracking-tight text-slate-900">Felicite<span class="text-azul-felicite">-se</span></span>
                    </a>
                    
                    <p class="p-base max-w-md">
                        Promovemos a <strong>saúde psicológica e inteligência emocional</strong> para estudantes e educadores através de conteúdos de confiança e acolhimento contínuo.
                    </p>
                </div>

                <div class="md:col-span-3 space-y-4">
                    <h3 class="text-sm font-bold uppercase tracking-wider text-slate-900">
                        Navegação
                    </h3>
                    <ul class="space-y-2.5 text-sm font-semibold">
                        <li><a href="<?= $baseUrl ?>index.php" class="text-slate-600 hover:text-azul-felicite transition-colors">Início</a></li>
                        <li><a href="<?= $baseUrl ?>blog.php" class="text-slate-600 hover:text-azul-felicite transition-colors">Central de Conteúdo</a></li>
                        <li><a href="<?= $baseUrl ?>sobre.php" class="text-slate-600 hover:text-azul-felicite transition-colors">Nossa História</a></li>
                        <li><a href="<?= $baseUrl ?>login.php" class="text-slate-600 hover:text-azul-felicite transition-colors">Área do Membro</a></li>
                    </ul>
                </div>

                <div class="md:col-span-3 space-y-4">
                    <h3 class="text-sm font-bold uppercase tracking-wider text-slate-900">
                        Contato
                    </h3>
                    <ul class="space-y-2 text-sm text-slate-600 font-medium">
                        <li class="font-semibold text-slate-800">contato@felicite-se.org</li>
                        <li>IFSul Câmpus Venâncio Aires</li>
                        <li>Venâncio Aires, RS</li>
                    </ul>
                </div>

            </div>

            <div class="pt-8 text-center sm:text-left">
                <p class="text-xs font-medium text-slate-500">
                    &copy; 2026 Felicite-se. Projeto educacional de <strong>saúde mental e bem-estar</strong> vinculado ao IFSul.
                </p>
            </div>
        </div>
    </footer>

</body>
</html>