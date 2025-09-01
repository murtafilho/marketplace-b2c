@extends('layouts.base')

@section('title', 'Teste Mobile-First')

@section('content')
<div class="space-y-8">
    <!-- Test Header -->
    <div class="text-center">
        <h1 class="text-3xl font-bold text-gray-900">Teste Mobile-First Layout</h1>
        <p class="mt-2 text-gray-600">Testando responsividade e componentes</p>
    </div>

    <!-- Breakpoint Indicators -->
    <div class="rounded-lg bg-blue-50 p-4">
        <h2 class="text-lg font-semibold text-blue-900">Breakpoint Atual:</h2>
        <div class="mt-2 space-y-1 text-sm">
            <p class="block text-red-600 sm:hidden">📱 Mobile (< 640px)</p>
            <p class="hidden text-orange-600 sm:block md:hidden">📱 Small (640px - 767px)</p>
            <p class="hidden text-yellow-600 md:block lg:hidden">💻 Medium (768px - 1023px)</p>
            <p class="hidden text-green-600 lg:block xl:hidden">💻 Large (1024px - 1279px)</p>
            <p class="hidden text-blue-600 xl:block 2xl:hidden">🖥️ XL (1280px - 1535px)</p>
            <p class="hidden text-purple-600 2xl:block">🖥️ 2XL (1536px+)</p>
        </div>
    </div>

    <!-- Grid Test -->
    <div>
        <h2 class="text-xl font-semibold text-gray-900">Grid Responsivo</h2>
        <p class="text-sm text-gray-600">Mobile: 1 col → Tablet: 2 cols → Desktop: 4 cols</p>
        <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            @for($i = 1; $i <= 8; $i++)
                <div class="rounded-lg bg-emerald-100 p-4 text-center">
                    <h3 class="font-medium">Item {{ $i }}</h3>
                    <p class="text-sm text-gray-600">Grid responsivo</p>
                </div>
            @endfor
        </div>
    </div>

    <!-- Typography Scale -->
    <div>
        <h2 class="text-xl font-semibold text-gray-900">Tipografia Responsiva</h2>
        <div class="mt-4 space-y-4">
            <h1 class="text-2xl font-bold sm:text-3xl lg:text-4xl xl:text-5xl">
                H1: Mobile 2xl → Desktop 5xl
            </h1>
            <h2 class="text-xl font-bold sm:text-2xl lg:text-3xl">
                H2: Mobile xl → Desktop 3xl
            </h2>
            <p class="text-sm sm:text-base lg:text-lg">
                Parágrafo: Mobile sm → Desktop lg
            </p>
        </div>
    </div>

    <!-- Spacing Test -->
    <div>
        <h2 class="text-xl font-semibold text-gray-900">Espaçamentos Progressivos</h2>
        <div class="mt-4 space-y-4">
            <div class="rounded-lg bg-gray-100 p-2 sm:p-4 lg:p-6 xl:p-8">
                <p class="text-sm">Padding: p-2 → sm:p-4 → lg:p-6 → xl:p-8</p>
            </div>
            <div class="space-y-2 sm:space-y-4 lg:space-y-6">
                <div class="rounded bg-blue-100 p-2">Item 1</div>
                <div class="rounded bg-blue-100 p-2">Item 2</div>
                <div class="rounded bg-blue-100 p-2">Gap: space-y-2 → sm:space-y-4 → lg:space-y-6</div>
            </div>
        </div>
    </div>

    <!-- Button Tests -->
    <div>
        <h2 class="text-xl font-semibold text-gray-900">Botões Touch-Friendly</h2>
        <div class="mt-4 flex flex-col gap-4 sm:flex-row">
            <button class="min-h-[44px] rounded-lg bg-emerald-600 px-4 py-3 text-white hover:bg-emerald-700 active:scale-95 transition-transform touch-manipulation">
                Botão Touch (44px mínimo)
            </button>
            <button class="min-h-[44px] rounded-lg border border-gray-300 px-4 py-3 hover:bg-gray-50 active:scale-95 transition-transform touch-manipulation">
                Botão Secundário
            </button>
        </div>
    </div>

    <!-- Navigation Test -->
    <div class="lg:hidden">
        <h2 class="text-xl font-semibold text-gray-900">Mobile Navigation</h2>
        <p class="text-sm text-gray-600">Visible apenas em mobile/tablet</p>
        <nav class="mt-4 rounded-lg bg-white border border-gray-200">
            <div class="divide-y">
                <a href="#" class="block px-4 py-3 hover:bg-gray-50">🏠 Início</a>
                <a href="#" class="block px-4 py-3 hover:bg-gray-50">📦 Produtos</a>
                <a href="#" class="block px-4 py-3 hover:bg-gray-50">📂 Categorias</a>
                <a href="#" class="block px-4 py-3 hover:bg-gray-50">👤 Perfil</a>
            </div>
        </nav>
    </div>

    <!-- Desktop Only Content -->
    <div class="hidden lg:block">
        <h2 class="text-xl font-semibold text-gray-900">Conteúdo Desktop</h2>
        <p class="text-gray-600">Este conteúdo só aparece em telas grandes (lg+)</p>
        <div class="mt-4 grid grid-cols-3 gap-6">
            <div class="rounded-lg bg-purple-100 p-4">Feature 1</div>
            <div class="rounded-lg bg-purple-100 p-4">Feature 2</div>
            <div class="rounded-lg bg-purple-100 p-4">Feature 3</div>
        </div>
    </div>

    <!-- Image Responsive Test -->
    <div>
        <h2 class="text-xl font-semibold text-gray-900">Imagens Responsivas</h2>
        <div class="mt-4 grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4">
            @for($i = 1; $i <= 4; $i++)
                <div class="aspect-square overflow-hidden rounded-lg bg-gray-200">
                    <img src="https://picsum.photos/300/300?random={{ $i }}" 
                         alt="Test image {{ $i }}"
                         class="h-full w-full object-cover hover:scale-105 transition-transform"
                         loading="lazy">
                </div>
            @endfor
        </div>
    </div>

    <!-- Performance Info -->
    <div class="rounded-lg bg-green-50 p-4">
        <h2 class="text-lg font-semibold text-green-900">✅ Mobile-First Checklist</h2>
        <ul class="mt-2 space-y-1 text-sm text-green-800">
            <li>✅ Classes base para mobile (sem prefixo)</li>
            <li>✅ Progressive enhancement (sm:, lg:, xl:)</li>
            <li>✅ Touch targets 44x44px mínimo</li>
            <li>✅ Tipografia escalável</li>
            <li>✅ Grids responsivos</li>
            <li>✅ Espaçamentos progressivos</li>
            <li>✅ Imagens com lazy loading</li>
            <li>✅ Hover states apenas desktop</li>
        </ul>
    </div>
</div>

<script>
// Log do breakpoint atual
function logBreakpoint() {
    const width = window.innerWidth;
    let breakpoint = 'mobile';
    
    if (width >= 1536) breakpoint = '2xl';
    else if (width >= 1280) breakpoint = 'xl';
    else if (width >= 1024) breakpoint = 'lg';
    else if (width >= 768) breakpoint = 'md';
    else if (width >= 640) breakpoint = 'sm';
    
    console.log(`Current breakpoint: ${breakpoint} (${width}px)`);
}

// Log inicial e ao redimensionar
window.addEventListener('load', logBreakpoint);
window.addEventListener('resize', logBreakpoint);
</script>
@endsection