<?php
$faqs = [
    [
        'pergunta' => 'O que é o projeto Felicite-se?',
        'resposta' => 'O Felicite-se é um <strong>projeto educacional do IFSul Campus Venâncio Aires</strong> focado no estudo e disseminação da Psicologia Positiva, da saúde mental.'
    ],
    [
        'pergunta' => 'Quem pode participar das oficinas e atividades?',
        'resposta' => 'As nossas ações são voltadas para <strong>estudantes, educadores e toda a comunidade</strong>, oferecendo dinâmicas interativas, momentos de escuta empática e autoconhecimento.'
    ],
    [
        'pergunta' => 'Como posso levar o projeto para a minha escola?',
        'resposta' => 'Basta entrar em contato através do <strong>nosso formulário de contato</strong> para combinarmos o agendamento de palestras, oficinas vivenciais e minicursos.'
    ],
    [
        'pergunta' => 'O conteúdo disponibilizado possui base científica?',
        'resposta' => 'Sim, todas as nossas publicações e metodologias são <strong>fundamentadas em artigos e pesquisas científicas</strong> sobre inteligência emocional e forças de caráter.'
    ],
    [
        'pergunta' => 'Os materiais e artigos do site são gratuitos?',
        'resposta' => 'Todo o acervo disponível na <strong>nossa Central de Conteúdo</strong> é aberto e gratuito para consulta de alunos, professores e pesquisadores.'
    ],
    [
        'pergunta' => 'Como funcionam os encontros de extensão comunitária?',
        'resposta' => 'Realizamos vivências práticas em instituições parceiras como a <strong>PARESP com crianças e jovens</strong>, promovendo o desenvolvimento de competências socioemocionais.'
    ]
];
?>
<section class="py-20 bg-fundo-app border-b border-slate-200/80" id="faq">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="text-center max-w-2xl mx-auto mb-12 space-y-3">
            <span class="inline-flex items-center gap-1.5 bg-sky-100 text-azul-texto border border-sky-200/60 px-4 py-1.5 rounded-full text-xs font-bold uppercase tracking-wider">
                PERGUNTAS FREQUENTES • DÚVIDAS
            </span>
            
            <h2 class="t-h2">
                Perguntas Frequentes sobre<br>o projeto Felicite-se
            </h2>
            
            <p class="p-base">
                Esclareça as suas dúvidas principais sobre as <strong>nossas oficinas, pesquisas e conteúdos</strong> voltados para o bem-estar escolar.
            </p>
        </div>

        <div class="space-y-4">
            <?php foreach ($faqs as $item): ?>
                <details class="group bg-white rounded-2xl border border-slate-200/80 transition-all duration-200">
                    <summary class="flex items-center justify-between p-6 cursor-pointer list-none font-bold text-slate-900 group-open:border-b group-open:border-slate-100">
                        <span class="text-base sm:text-lg pr-4">
                            <?= htmlspecialchars($item['pergunta']) ?>
                        </span>
                        <span class="w-8 h-8 rounded-full bg-slate-100 text-slate-600 group-open:bg-azul-suave group-open:text-azul-felicite flex items-center justify-center font-bold text-xl flex-shrink-0 transition-colors">
                            +
                        </span>
                    </summary>
                    <div class="p-6 pt-4 bg-slate-50/50 rounded-b-2xl">
                        <p class="p-base">
                            <?= $item['resposta'] ?>
                        </p>
                    </div>
                </details>
            <?php endforeach; ?>
        </div>

        <div class="mt-12 p-8 bg-white rounded-3xl border border-slate-200/80 text-center space-y-4">
            <h3 class="t-h3">
                Ainda tem alguma dúvida?
            </h3>
            <p class="p-base max-w-xl mx-auto">
                A nossa equipa está sempre disponível para <strong>conversar com a sua instituição</strong> e esclarecer qualquer questão.
            </p>
            <div>
                <a href="contato.php" class="inline-flex justify-center items-center px-7 py-3 rounded-full font-bold text-white bg-azul-felicite hover:bg-azul-hover transition-all">
                    Falar com a Equipa
                </a>
            </div>
        </div>

    </div>
</section>

<style>
    details summary::-webkit-details-marker {
        display: none;
    }
    details[open] summary span:last-child {
        transform: rotate(45deg);
    }
</style>