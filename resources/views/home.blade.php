@extends('layouts.base')

@section('title', 'valedosol.org - Marketplace da Comunidade')

@section('content')
    <!-- Hero Carousel Section -->
    <section class="relative overflow-hidden" 
             x-data="{
                 currentSlide: 0,
                 slides: [
                     {
                         title: 'Onde vizinhos se tornam',
                         highlight: 'parceiros',
                         description: 'Descubra produtos únicos, serviços especializados e sabores autênticos da nossa comunidade. Cada compra fortalece os laços que nos unem.',
                         image: 'https://images.unsplash.com/photo-1556909114-f6e7ad7d3136?ixlib=rb-4.0.3&auto=format&fit=crop&w=2400&q=80',
                         primaryCta: 'Explorar Produtos Locais',
                         primaryLink: '{{ route('products.index') }}',
                         secondaryCta: 'Conheça os Artesãos',
                         secondaryLink: '#vendors'
                     },
                     {
                         title: 'Produtos orgânicos',
                         highlight: 'da nossa terra',
                         description: 'Alimentos frescos e saudáveis direto dos produtores locais. Cultivados com amor e sem agrotóxicos para sua família.',
                         image: 'https://images.unsplash.com/photo-1560472354-b33ff0c44a43?ixlib=rb-4.0.3&auto=format&fit=crop&w=2400&q=80',
                         primaryCta: 'Ver Orgânicos',
                         primaryLink: '{{ route('products.index') }}?category=organicos',
                         secondaryCta: 'Saiba Mais',
                         secondaryLink: '#features'
                     },
                     {
                         title: 'Arte e artesanato',
                         highlight: 'únicos',
                         description: 'Peças exclusivas criadas por artesãos locais. Cada item conta uma história e traz a essência da nossa comunidade.',
                         image: 'https://images.unsplash.com/photo-1513475382585-d06e58bcb0e0?ixlib=rb-4.0.3&auto=format&fit=crop&w=2400&q=80',
                         primaryCta: 'Explorar Arte',
                         primaryLink: '{{ route('products.index') }}?category=artesanato',
                         secondaryCta: 'Ver Artesãos',
                         secondaryLink: '#vendors'
                     }
                 ],
                 autoplay: null,
                 isTransitioning: false,
                 startAutoplay() {
                     this.autoplay = setInterval(() => {
                         if (!this.isTransitioning) {
                             this.nextSlide();
                         }
                     }, 6000);
                 },
                 stopAutoplay() {
                     if (this.autoplay) {
                         clearInterval(this.autoplay);
                         this.autoplay = null;
                     }
                 },
                 nextSlide() {
                     if (this.isTransitioning) return;
                     this.isTransitioning = true;
                     this.currentSlide = (this.currentSlide + 1) % this.slides.length;
                     setTimeout(() => { this.isTransitioning = false; }, 800);
                 },
                 prevSlide() {
                     if (this.isTransitioning) return;
                     this.isTransitioning = true;
                     this.currentSlide = this.currentSlide === 0 ? this.slides.length - 1 : this.currentSlide - 1;
                     setTimeout(() => { this.isTransitioning = false; }, 800);
                 },
                 goToSlide(index) {
                     if (this.isTransitioning || index === this.currentSlide) return;
                     this.isTransitioning = true;
                     this.currentSlide = index;
                     this.stopAutoplay();
                     setTimeout(() => { 
                         this.isTransitioning = false;
                         this.startAutoplay();
                     }, 800);
                 }
             }"
             x-init="startAutoplay()"
             @mouseenter="stopAutoplay()"
             @mouseleave="startAutoplay()">
        
        <!-- Carousel Container -->
        <div class="relative h-[500px] sm:h-[600px] lg:h-[700px] overflow-hidden">
            <!-- Slides Container -->
            <div class="flex h-full transition-transform duration-800 ease-in-out"
                 :style="`transform: translateX(-${currentSlide * 100}%)`">
                
                <template x-for="(slide, index) in slides" :key="index">
                    <div class="w-full h-full flex-shrink-0 relative">
                        
                        <!-- Background Image -->
                        <img :src="slide.image" 
                             :alt="slide.title"
                             class="absolute inset-0 w-full h-full object-cover">
                        
                        <!-- Gradient Overlay -->
                        <div class="absolute inset-0 bg-gradient-to-r from-black/40 via-black/20 to-transparent"></div>
                        
                        <!-- Content Container -->
                        <div class="relative container mx-auto h-full flex items-center px-4">
                            <div class="max-w-4xl">
                                <!-- Main Content with Background Card -->
                                <div class="max-w-2xl bg-white/10 backdrop-blur-md rounded-3xl p-8 sm:p-10 lg:p-12 shadow-2xl border border-white/20">
                                    <!-- Title -->
                                    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-display font-semibold text-white mb-6 leading-tight drop-shadow-lg">
                                        <span class="block" x-text="slide.title"></span>
                                        <span class="block text-orange-400 drop-shadow-lg" x-text="slide.highlight"></span>
                                    </h1>
                                    
                                    <!-- Description -->
                                    <p class="text-lg sm:text-xl text-white/95 mb-8 leading-relaxed max-w-xl drop-shadow-md"
                                       x-text="slide.description">
                                    </p>
                                    
                                    <!-- CTA Buttons -->
                                    <div class="flex flex-col sm:flex-row space-y-4 sm:space-y-0 sm:space-x-4">
                                        <a :href="slide.primaryLink" 
                                           class="bg-verde-suave text-white px-8 py-4 rounded-full font-medium hover:bg-verde-mata transition duration-300 shadow-lg text-center inline-flex items-center justify-center hover:shadow-xl transform hover:-translate-y-1">
                                            <span x-text="slide.primaryCta"></span>
                                        </a>
                                        <a :href="slide.secondaryLink" 
                                           class="border-2 border-orange-400 text-orange-400 bg-white/20 backdrop-blur-sm px-8 py-4 rounded-full font-medium hover:bg-orange-500 hover:text-white transition duration-300 text-center inline-flex items-center justify-center hover:shadow-xl transform hover:-translate-y-1">
                                            <span x-text="slide.secondaryCta"></span>
                                        </a>
                                    </div>
                                </div>
                                
                                <!-- Features Grid - Positioned at bottom right on larger screens -->
                                <div class="hidden lg:block absolute bottom-8 right-8">
                                    <div class="bg-white/10 backdrop-blur-sm rounded-3xl p-6 shadow-2xl">
                                        <div class="grid grid-cols-2 gap-4">
                                            <div class="bg-white rounded-2xl p-4 text-center">
                                                <i class="fas fa-seedling text-2xl text-verde-suave mb-2"></i>
                                                <h4 class="font-semibold text-verde-mata text-sm">Orgânicos</h4>
                                                <p class="text-xs text-cinza-pedra">Direto da horta</p>
                                            </div>
                                            <div class="bg-white rounded-2xl p-4 text-center">
                                                <i class="fas fa-hammer text-2xl text-orange-500 mb-2"></i>
                                                <h4 class="font-semibold text-verde-mata text-sm">Artesanal</h4>
                                                <p class="text-xs text-cinza-pedra">Feito à mão</p>
                                            </div>
                                            <div class="bg-white rounded-2xl p-4 text-center">
                                                <i class="fas fa-home text-2xl text-dourado mb-2"></i>
                                                <h4 class="font-semibold text-verde-mata text-sm">Serviços</h4>
                                                <p class="text-xs text-cinza-pedra">Para sua casa</p>
                                            </div>
                                            <div class="bg-white rounded-2xl p-4 text-center">
                                                <i class="fas fa-utensils text-2xl text-verde-suave mb-2"></i>
                                                <h4 class="font-semibold text-verde-mata text-sm">Gastronomia</h4>
                                                <p class="text-xs text-cinza-pedra">Sabores únicos</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Navigation Controls -->
            <!-- Slide Indicators -->
            <div class="absolute bottom-6 left-1/2 transform -translate-x-1/2 flex space-x-3 z-20">
                <template x-for="(slide, index) in slides" :key="index">
                    <button @click="goToSlide(index)"
                            :class="currentSlide === index ? 'bg-orange-500 w-8' : 'bg-white/50 w-3'"
                            class="h-3 rounded-full transition-all duration-300 hover:bg-orange-400"
                            :aria-label="'Ir para slide ' + (index + 1)">
                    </button>
                </template>
            </div>

            <!-- Previous Button -->
            <button @click="prevSlide()"
                    class="absolute left-4 top-1/2 -translate-y-1/2 z-20 bg-white/20 backdrop-blur-sm rounded-full p-3 shadow-lg hover:bg-white/30 transition-all duration-300 group">
                <svg class="w-6 h-6 text-white group-hover:text-orange-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </button>

            <!-- Next Button -->
            <button @click="nextSlide()"
                    class="absolute right-4 top-1/2 -translate-y-1/2 z-20 bg-white/20 backdrop-blur-sm rounded-full p-3 shadow-lg hover:bg-white/30 transition-all duration-300 group">
                <svg class="w-6 h-6 text-white group-hover:text-orange-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </button>
        </div>
    </section>


    <!-- Categories Navigation -->
    <section class="py-12 bg-white">
        <div class="container mx-auto px-4">
            <div class="text-center mb-8">
                <h3 class="text-3xl font-display font-semibold text-verde-mata mb-2">Categorias</h3>
                <p class="text-lg text-cinza-pedra">Explore nossos produtos por categoria</p>
            </div>
            
            @if($mainCategories->count() > 0)
                <div class="flex flex-wrap justify-center gap-6">
                    @foreach($mainCategories as $category)
                        <a href="{{ route('search') }}?categoria={{ $category->slug }}" 
                           class="flex items-center space-x-3 bg-verde-suave/10 px-6 py-3 rounded-full hover:bg-verde-mata hover:text-white hover:shadow-lg transform hover:scale-105 transition-all duration-300 group">
                            @if($category->icon)
                                <i class="{{ $category->icon }} text-verde-suave group-hover:text-white transition-colors duration-300"></i>
                            @else
                                <svg class="w-5 h-5 text-verde-suave group-hover:text-white transition-colors duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                </svg>
                            @endif
                            <span class="font-medium transition-colors duration-300">{{ $category->name }}</span>
                            @if($category->products_count > 0)
                                <span class="text-xs bg-verde-mata/20 text-verde-mata group-hover:bg-white/30 group-hover:text-white px-2 py-1 rounded-full transition-all duration-300 font-medium">{{ $category->products_count }}</span>
                            @endif
                        </a>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <p class="text-cinza-pedra">Nenhuma categoria disponível no momento.</p>
                </div>
            @endif
        </div>
    </section>

    <!-- Featured Vendors -->
    <section id="vendors" class="py-16 bg-gradient-to-b from-branco-fresco to-verde-suave/5">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h3 class="text-4xl font-display font-semibold text-verde-mata mb-4">Nossos Vizinhos Empreendedores</h3>
                <p class="text-xl text-cinza-pedra">Conheça as pessoas por trás dos produtos que você ama</p>
            </div>
            
            @if($featuredSellers->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach($featuredSellers->take(6) as $seller)
                        <div class="bg-white rounded-3xl shadow-lg overflow-hidden hover:shadow-xl transition duration-500 group">
                            <div class="relative">
                                @if($seller->avatar_path)
                                    <img src="{{ asset($seller->avatar_path) }}" 
                                         alt="{{ $seller->user->name }}" 
                                         class="w-full h-48 object-cover">
                                @else
                                    <div class="bg-gradient-to-br from-verde-suave to-dourado h-48 flex items-center justify-center">
                                        <div class="w-20 h-20 bg-white/30 rounded-full flex items-center justify-center">
                                            <span class="text-3xl font-bold text-white">
                                                {{ strtoupper(substr($seller->user->name, 0, 2)) }}
                                            </span>
                                        </div>
                                    </div>
                                @endif
                                
                                <div class="absolute top-4 right-4 bg-white/90 backdrop-blur-sm px-3 py-1 rounded-full">
                                    <span class="text-sm font-medium text-verde-mata">
                                        ⭐ {{ number_format($seller->rating ?? 4.5, 1) }}
                                    </span>
                                </div>
                            </div>
                            
                            <div class="p-6">
                                <div class="flex items-center space-x-3 mb-4">
                                    <div class="w-12 h-12 bg-verde-suave rounded-full flex items-center justify-center">
                                        <span class="text-white font-semibold">
                                            {{ strtoupper(substr($seller->user->name, 0, 2)) }}
                                        </span>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h4 class="font-display font-semibold text-verde-mata truncate">
                                            {{ $seller->user->name }}
                                        </h4>
                                        @if($seller->business_name)
                                            <p class="text-sm text-cinza-pedra truncate">
                                                {{ $seller->business_name }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                                
                                @if($seller->bio)
                                    <p class="text-cinza-pedra mb-4 line-clamp-3">
                                        {{ $seller->bio }}
                                    </p>
                                @else
                                    <p class="text-cinza-pedra mb-4">
                                        Vendedor verificado com produtos de qualidade.
                                    </p>
                                @endif
                                
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-verde-suave font-medium">
                                        {{ $seller->active_products_count }} produto{{ $seller->active_products_count != 1 ? 's' : '' }} disponível{{ $seller->active_products_count != 1 ? 'eis' : '' }}
                                    </span>
                                    <a href="{{ route('search') }}?vendedor={{ $seller->id }}" 
                                       class="bg-verde-suave text-white px-4 py-2 rounded-full hover:bg-verde-mata transition duration-300">
                                        Ver Loja
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-16 w-16 text-cinza-pedra/50 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    <h3 class="text-lg font-semibold text-cinza-pedra mb-2">Nenhum vendedor em destaque</h3>
                    <p class="text-cinza-pedra">Em breve teremos empreendedores incríveis aqui!</p>
                </div>
            @endif
        </div>
    </section>

    <!-- Featured Products -->
    <section class="py-16 bg-white">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h3 class="text-4xl font-display font-semibold text-verde-mata mb-4">Produtos em Destaque</h3>
                <p class="text-xl text-cinza-pedra">O melhor da nossa comunidade, selecionado especialmente para você</p>
            </div>
            
            @if($featuredProducts->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach($featuredProducts->take(8) as $product)
                        <div class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-xl transition duration-500 border border-verde-suave/10">
                            <div class="relative">
                                @if($product->images && $product->images->count() > 0)
                                    <img src="{{ asset($product->images->first()->file_path) }}" 
                                         alt="{{ $product->name }}" 
                                         class="w-full h-48 object-cover">
                                @else
                                    <div class="bg-gradient-to-br from-verde-suave/20 to-dourado/20 h-48 flex items-center justify-center">
                                        <svg class="w-12 h-12 text-cinza-pedra" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2-2v12a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                @endif
                                
                                @if($product->featured)
                                    <div class="absolute top-3 left-3 bg-verde-suave text-white px-2 py-1 rounded-full text-xs font-medium">
                                        Destaque
                                    </div>
                                @endif
                                
                                @if($product->stock_quantity <= 0)
                                    <div class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center">
                                        <span class="bg-red-600 text-white text-xs font-medium px-2 py-1 rounded">
                                            Esgotado
                                        </span>
                                    </div>
                                @endif
                            </div>
                            
                            <div class="p-5">
                                <h4 class="font-semibold text-verde-mata mb-2 line-clamp-2">
                                    <a href="{{ route('product.show', $product->id) }}" class="hover:text-verde-suave transition-colors">
                                        {{ $product->name }}
                                    </a>
                                </h4>
                                
                                @if($product->short_description)
                                    <p class="text-sm text-cinza-pedra mb-3 line-clamp-2">{{ $product->short_description }}</p>
                                @endif
                                
                                <div class="text-xs text-cinza-pedra mb-3">
                                    por {{ $product->seller->user->name ?? 'Vendedor' }}
                                </div>
                                
                                <div class="flex items-center justify-between mb-3">
                                    <div>
                                        @if($product->compare_at_price && $product->compare_at_price > $product->price)
                                            <div class="text-xs text-cinza-pedra line-through">
                                                R$ {{ number_format($product->compare_at_price, 2, ',', '.') }}
                                            </div>
                                        @endif
                                        <span class="text-xl font-bold text-orange-500">
                                            R$ {{ number_format($product->price, 2, ',', '.') }}
                                        </span>
                                    </div>
                                    @if($product->category)
                                        <span class="text-xs bg-verde-suave/10 text-verde-mata px-2 py-1 rounded-full">
                                            {{ $product->category->name }}
                                        </span>
                                    @endif
                                </div>
                                
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-1">
                                        <span class="text-xs text-cinza-pedra">
                                            {{ $product->views_count ?? 0 }} visualizações
                                        </span>
                                    </div>
                                    <a href="{{ route('product.show', $product->id) }}" 
                                       class="bg-verde-suave text-white px-4 py-2 rounded-full text-sm hover:bg-verde-mata transition duration-300">
                                        Ver Produto
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                @if($featuredProducts->count() > 8)
                    <div class="text-center mt-8">
                        <a href="{{ route('search') }}" class="inline-flex items-center px-6 py-3 bg-verde-suave text-white rounded-full hover:bg-verde-mata transition duration-300">
                            Ver Todos os Produtos
                            <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                    </div>
                @endif
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-16 w-16 text-cinza-pedra/50 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                    <h3 class="text-lg font-semibold text-cinza-pedra mb-2">Nenhum produto em destaque</h3>
                    <p class="text-cinza-pedra">Em breve teremos produtos incríveis aqui!</p>
                </div>
            @endif
        </div>
    </section>

    <!-- Community Newsletter -->
    <section class="py-16 bg-gradient-to-r from-verde-suave to-verde-mata text-white">
        <div class="container mx-auto px-4 text-center">
            <div class="max-w-3xl mx-auto">
                <h3 class="text-4xl font-display font-semibold mb-4">Fique Conectado com a Comunidade</h3>
                <p class="text-xl mb-8 opacity-90">
                    Receba novidades dos nossos vizinhos, produtos sazonais e eventos especiais do Vale do Sol
                </p>
                <div class="flex flex-col sm:flex-row max-w-md mx-auto">
                    <input type="email" id="newsletter-email" placeholder="Seu melhor e-mail" 
                           class="flex-1 px-6 py-4 rounded-l-full sm:rounded-r-none rounded-r-full text-verde-mata focus:outline-none">
                    <button onclick="subscribeNewsletter()" class="bg-orange-500 text-white px-8 py-4 rounded-r-full sm:rounded-l-none rounded-l-full font-semibold hover:bg-orange-600 transition duration-300 mt-4 sm:mt-0">
                        Participar
                    </button>
                </div>
                <p class="text-sm mt-4 opacity-75">
                    Prometemos não encher sua caixa de entrada. Apenas o essencial da nossa comunidade.
                </p>
            </div>
        </div>
    </section>

    <script>
        function subscribeNewsletter() {
            const email = document.getElementById('newsletter-email').value;
            if (email && email.includes('@')) {
                alert(`Bem-vindo à comunidade valedosol.org! 🌱\nVocê receberá nossas novidades em: ${email}`);
                document.getElementById('newsletter-email').value = '';
            } else {
                alert('Por favor, insira um e-mail válido para participar da nossa comunidade.');
            }
        }
    </script>
@endsection