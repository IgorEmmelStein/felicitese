<?php
$baseUrl = defined('BASE_URL') ? BASE_URL : '';

// Carrega os links centralizados
$navItems = file_exists(__DIR__ . '/../config/navigation.php') 
    ? require __DIR__ . '/../config/navigation.php' 
    : [];
?>
    </main>

    <footer class="bg-white border-t border-slate-200/80 mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-16">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 lg:gap-12">
                
                <!-- Coluna 1: Sobre e Logo -->
                <div class="md:col-span-2 space-y-4">
                    <a href="<?= $baseUrl ?>index.php" class="flex items-center gap-3">
                        <img src="<?= $baseUrl ?>assets/images/felicitese-logo-2.png" alt="Logo Felicite-se" class="h-10 w-auto">
                        <span class="text-xl font-extrabold tracking-tight text-slate-900">Felicite<span class="text-purple">-se</span></span>
                    </a>
                    <p class="text-sm text-slate-600 max-w-md leading-relaxed">
                        Promovendo ações socioemocionais, saúde mental e desenvolvimento humano no ecossistema acadêmico do IFSul e comunidade escolar.
                    </p>
                </div>

                <!-- Coluna 2: Navegação Dinâmica -->
                <div class="space-y-4">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-900">Navegação</h3>
                    <ul class="space-y-2.5">
                        <?php foreach ($navItems as $item): ?>
                            <li>
                                <a href="<?= $baseUrl . $item['url'] ?>" class="text-sm font-medium text-slate-600 hover:text-purple transition-colors">
                                    <?= htmlspecialchars($item['label']) ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <!-- Coluna 3: Acesso Rápido -->
                <div class="space-y-4">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-900">Acesso Restrito</h3>
                    <ul class="space-y-2.5">
                        <li>
                            <a href="<?= $baseUrl ?>login.php" class="text-sm font-medium text-slate-600 hover:text-purple transition-colors inline-flex items-center gap-1.5">
                                Área do Membro &rarr;
                            </a>
                        </li>
                    </ul>
                </div>

            </div>

            <!-- Linha Inferior / Direitos -->
            <div class="pt-8 mt-8 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between text-xs text-slate-500 gap-4">
                <p>&copy; <?= date('Y') ?> Projeto Felicite-se • IFSul Campus Venâncio Aires.</p>
                <p>Desenvolvido com foco na saúde mental e bem-estar comunitário.</p>
            </div>
        </div>
    </footer>

</body>
</html>