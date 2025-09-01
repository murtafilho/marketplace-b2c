@extends('layouts.base')

@section('title', 'Marketplace - Início')

@section('content')
<!-- Hero Section with Smooth Sliding Carousel -->
<section class="relative overflow-hidden bg-gradient-to-br from-emerald-50 to-white" 
         x-data="{ 
             currentSlide: 0,
             slides: [
                 {
                     title: 'Marketplace',
                     subtitle: 'Comunitário',
                     description: 'Conecte-se com vendedores locais e descubra produtos únicos da sua região.',
                     image: 'https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?ixlib=rb-4.0.3&auto=format&fit=crop&w=2340&q=80',
                     primaryCta: 'Explorar Produtos',
                     primaryLink: '{{ route("products.index") }}',
                     secondaryCta: 'Vender no Marketplace',
                     secondaryLink: '{{ route("seller.register") }}'
                 },
                 {
                     title: 'Ofertas',
                     subtitle: 'Especiais',
                     description: 'Aproveite descontos exclusivos e promoções imperdíveis todos os dias.',
                     image: 'https://images.unsplash.com/photo-1607082348824-0a96f2a4b9da?ixlib=rb-4.0.3&auto=format&fit=crop&w=2340&q=80',
                     primaryCta: 'Ver Ofertas',
                     primaryLink: '{{ route("products.index") }}?sort=price_low',
                     secondaryCta: 'Criar Conta',
                     secondaryLink: '{{ route("register") }}'
                 },
                 {
                     title: 'Produtos',
                     subtitle: 'Locais',
                     description: 'Apoie negócios da sua comunidade e fortaleça a economia local.',
                     image: 'https://images.unsplash.com/photo-1472851294608-062f824d29cc?ixlib=rb-4.0.3&auto=format&fit=crop&w=2340&q=80',
                     primaryCta: 'Começar Agora',
                     primaryLink: '{{ route("products.index") }}',
                     secondaryCta: 'Saiba Mais',
                     secondaryLink: '#features'
                 }
             ],
             autoplay: null,
             isTransitioning: false,
             startAutoplay() {
                 this.autoplay = setInterval(() => {
                     if (!this.isTransitioning) {
                         this.nextSlide();
                     }
                 }, 5000);
             },
             stopAutoplay() {
                 if (this.autoplay) {
                     clearInterval(this.autoplay);
                 }
             },
             nextSlide() {
                 if (this.isTransitioning) return;
                 this.isTransitioning = true;
                 this.currentSlide = (this.currentSlide + 1) % this.slides.length;
                 setTimeout(() => { this.isTransitioning = false; }, 700);
             },
             prevSlide() {
                 if (this.isTransitioning) return;
                 this.isTransitioning = true;
                 this.currentSlide = this.currentSlide === 0 ? this.slides.length - 1 : this.currentSlide - 1;
                 setTimeout(() => { this.isTransitioning = false; }, 700);
             },
             goToSlide(index) {
                 if (this.isTransitioning || index === this.currentSlide) return;
                 this.isTransitioning = true;
                 this.currentSlide = index;
                 this.stopAutoplay();
                 this.startAutoplay();
                 setTimeout(() => { this.isTransitioning = false; }, 700);
             }
         }"
         x-init="startAutoplay()"
         @mouseenter="stopAutoplay()"
         @mouseleave="startAutoplay()">
    
    <!-- Carousel Container -->
    <div class="relative h-[400px] sm:h-[500px] md:h-[600px] lg:h-[720px] overflow-hidden">
        <!-- Slides Container with Transform -->
        <div class="flex h-full transition-transform duration-700 ease-in-out"
             :style="`transform: translateX(-${currentSlide * 100}%)`">
            
            <template x-for="(slide, index) in slides" :key="index">
                <div class="w-full h-full flex-shrink-0 relative">
                    
                    <!-- Background Image -->
                    <img :src="slide.image" 
                         :alt="slide.title"
                         class="absolute inset-0 w-full h-full object-cover">
                    
                    <!-- Gradient Overlay -->
                    <div class="absolute inset-0 bg-gradient-to-r from-black/70 via-black/50 to-transparent"></div>
                    
                    <!-- Content Container -->
                    <div class="relative mx-auto max-w-7xl h-full flex items-center">
                        <div class="relative z-10 w-full px-4 sm:px-6 lg:px-8">
                            <div class="max-w-2xl lg:ml-16">
                                <!-- Heading -->
                                <h1 class="text-4xl font-bold tracking-tight text-white sm:text-5xl md:text-6xl">
                                    <span class="block" x-text="slide.title"></span>
                                    <span class="block text-emerald-400" x-text="slide.subtitle"></span>
                                </h1>
                                
                                <!-- Description -->
                                <p class="mt-3 text-base text-gray-100 sm:mt-5 sm:text-lg md:mt-5 md:text-xl"
                                   x-text="slide.description">
                                </p>
                                
                                <!-- CTA Buttons -->
                                <div class="mt-5 sm:mt-8 sm:flex sm:justify-start">
                                    <div class="rounded-md shadow-lg">
                                        <a :href="slide.primaryLink" 
                                           class="flex w-full items-center justify-center rounded-md bg-emerald-600 px-8 py-3 text-base font-medium text-white hover:bg-emerald-700 transition-colors md:px-10 md:py-4 md:text-lg"
                                           x-text="slide.primaryCta">
                                        </a>
                                    </div>
                                    <div class="mt-3 sm:ml-3 sm:mt-0">
                                        <a :href="slide.secondaryLink" 
                                           class="flex w-full items-center justify-center rounded-md border-2 border-white bg-white/10 backdrop-blur-sm px-8 py-3 text-base font-medium text-white hover:bg-white/20 transition-colors md:px-10 md:py-4 md:text-lg"
                                           x-text="slide.secondaryCta">
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        
        <!-- Carousel Controls -->
        <!-- Mobile Dots -->
        <div class="absolute inset-x-0 bottom-4 flex justify-center space-x-2 z-20">
            <template x-for="(slide, index) in slides" :key="index">
                <button @click="goToSlide(index)"
                        :class="currentSlide === index ? 'bg-white w-8' : 'bg-white/50 w-2'"
                        class="h-2 rounded-full transition-all duration-300"
                        :aria-label="'Go to slide ' + (index + 1)">
                </button>
            </template>
        </div>

        <!-- Navigation Arrows -->
        <!-- Previous Button -->
        <button @click="prevSlide()"
                class="absolute left-4 top-1/2 -translate-y-1/2 z-20 bg-white/20 backdrop-blur-sm rounded-full p-3 shadow-lg hover:bg-white/30 transition-colors group">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </button>

        <!-- Next Button -->
        <button @click="nextSlide()"
                class="absolute right-4 top-1/2 -translate-y-1/2 z-20 bg-white/20 backdrop-blur-sm rounded-full p-3 shadow-lg hover:bg-white/30 transition-colors group">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </button>
    </div>
</section>

<!-- Features Section - Mobile First Grid -->
<section id="features" class="py-12 bg-gray-50">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <h2 class="text-3xl font-bold text-gray-900 sm:text-4xl">Por que escolher nosso marketplace?</h2>
            <p class="mt-4 text-lg text-gray-600">Benefícios para compradores e vendedores</p>
        </div>
        
        <!-- Mobile: 1 column, Tablet: 2 columns, Desktop: 3 columns -->
        <div class="mt-10 grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
            <!-- Feature 1 -->
            <div class="relative rounded-lg bg-white p-6 shadow-sm">
                <div>
                    <span class="inline-flex rounded-lg bg-emerald-500 p-3">
                        <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                        </svg>
                    </span>
                </div>
                <h3 class="mt-4 text-lg font-medium text-gray-900">Compra Segura</h3>
                <p class="mt-2 text-base text-gray-500">
                    Proteção total em suas compras com garantia de entrega e qualidade.
                </p>
            </div>
            
            <!-- Feature 2 -->
            <div class="relative rounded-lg bg-white p-6 shadow-sm">
                <div>
                    <span class="inline-flex rounded-lg bg-emerald-500 p-3">
                        <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                        </svg>
                    </span>
                </div>
                <h3 class="mt-4 text-lg font-medium text-gray-900">Vendedores Verificados</h3>
                <p class="mt-2 text-base text-gray-500">
                    Todos os vendedores passam por um processo de verificação rigoroso.
                </p>
            </div>
            
            <!-- Feature 3 -->
            <div class="relative rounded-lg bg-white p-6 shadow-sm">
                <div>
                    <span class="inline-flex rounded-lg bg-emerald-500 p-3">
                        <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 00-10.026 0 1.106 1.106 0 00-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12" />
                        </svg>
                    </span>
                </div>
                <h3 class="mt-4 text-lg font-medium text-gray-900">Entrega Rápida</h3>
                <p class="mt-2 text-base text-gray-500">
                    Receba seus produtos rapidamente com nosso sistema de logística otimizado.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Categories Section - Horizontal scroll on mobile -->
@if($mainCategories && $mainCategories->count() > 0)
<section class="py-12 bg-white">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <h2 class="text-2xl font-bold text-gray-900 sm:text-3xl">Categorias Populares</h2>
        
        <!-- Mobile: Horizontal scroll, Desktop: Grid -->
        <div class="mt-6 flex gap-4 overflow-x-auto pb-4 sm:grid sm:grid-cols-3 sm:gap-6 sm:overflow-visible sm:pb-0 lg:grid-cols-6">
            @foreach($mainCategories->take(6) as $category)
                <a href="{{ route('products.category', $category) }}" class="flex-none sm:flex-auto">
                    <div class="group relative h-32 w-32 overflow-hidden rounded-lg bg-gray-100 sm:h-auto sm:w-auto">
                        <div class="flex h-full w-full items-center justify-center bg-emerald-100 group-hover:bg-emerald-200">
                            @if($category->icon)
                                <span class="text-2xl mb-2">{{ $category->icon }}</span>
                            @endif
                            <span class="text-sm font-medium text-gray-900 text-center">{{ $category->name }}</span>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- Products Grid - Responsive -->
<section class="py-12 bg-gray-50">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-bold text-gray-900 sm:text-3xl">Produtos em Destaque</h2>
            <a href="{{ route('products.index') }}" class="text-sm font-medium text-emerald-600 hover:text-emerald-500">
                Ver todos
                <span aria-hidden="true"> &rarr;</span>
            </a>
        </div>
        
        <!-- Mobile: 2 columns, Tablet: 3 columns, Desktop: 4 columns -->
        @if($featuredProducts && $featuredProducts->count() > 0)
            <div class="mt-6 grid grid-cols-2 gap-4 sm:grid-cols-3 sm:gap-6 lg:grid-cols-4">
                @foreach($featuredProducts as $product)
                    <div class="group relative">
                        <div class="aspect-square w-full overflow-hidden rounded-lg bg-gray-200">
                            @if($product->images && $product->images->first())
                                <img src="{{ $product->images->first()->url }}" 
                                     alt="{{ $product->name }}" 
                                     class="h-full w-full object-cover object-center group-hover:opacity-75"
                                     loading="lazy">
                            @else
                                <img src="https://via.placeholder.com/300?text=Sem+Imagem" 
                                     alt="{{ $product->name }}" 
                                     class="h-full w-full object-cover object-center group-hover:opacity-75"
                                     loading="lazy">
                            @endif
                        </div>
                        <div class="mt-4">
                            <h3 class="text-sm text-gray-700 line-clamp-2">
                                <a href="{{ route('product.show', $product->id) }}">
                                    <span aria-hidden="true" class="absolute inset-0"></span>
                                    {{ $product->name }}
                                </a>
                            </h3>
                            <p class="mt-1 text-sm text-gray-500">{{ $product->category->name ?? 'Categoria' }}</p>
                            <p class="mt-1 text-sm font-medium text-gray-900">R$ {{ number_format($product->price, 2, ',', '.') }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <!-- Fallback for when no featured products exist -->
            <div class="mt-6 grid grid-cols-2 gap-4 sm:grid-cols-3 sm:gap-6 lg:grid-cols-4">
                @for($i = 1; $i <= 8; $i++)
                    <div class="group relative">
                        <div class="aspect-square w-full overflow-hidden rounded-lg bg-gray-200">
                            <img src="https://via.placeholder.com/300?text=Produto+{{ $i }}" 
                                 alt="Produto {{ $i }}" 
                                 class="h-full w-full object-cover object-center group-hover:opacity-75"
                                 loading="lazy">
                        </div>
                        <div class="mt-4">
                            <h3 class="text-sm text-gray-700">
                                <a href="#">
                                    <span aria-hidden="true" class="absolute inset-0"></span>
                                    Produto Exemplo {{ $i }}
                                </a>
                            </h3>
                            <p class="mt-1 text-sm text-gray-500">Categoria</p>
                            <p class="mt-1 text-sm font-medium text-gray-900">R$ {{ number_format($i * 50 + rand(20, 99), 2, ',', '.') }}</p>
                        </div>
                    </div>
                @endfor
            </div>
        @endif
    </div>
</section>

<!-- CTA Section -->
<section class="bg-emerald-700">
    <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:flex lg:items-center lg:justify-between lg:px-8 lg:py-16">
        <h2 class="text-3xl font-bold tracking-tight text-white sm:text-4xl">
            <span class="block">Pronto para começar?</span>
            <span class="block text-emerald-200">Crie sua loja hoje mesmo.</span>
        </h2>
        <div class="mt-8 flex flex-col gap-3 sm:flex-row lg:mt-0 lg:flex-shrink-0">
            <a href="{{ route('seller.register') }}" 
               class="inline-flex items-center justify-center rounded-md bg-white px-5 py-3 text-base font-medium text-emerald-700 hover:bg-emerald-50">
                Criar Minha Loja
            </a>
            <a href="#" 
               class="inline-flex items-center justify-center rounded-md border border-white bg-emerald-700 px-5 py-3 text-base font-medium text-white hover:bg-emerald-600">
                Saiba Mais
            </a>
        </div>
    </div>
</section>
@endsection