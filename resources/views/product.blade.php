@extends('layouts.base')

@section('title', $product->name . ' - ' . ($product->seller->user->name ?? $product->sellerUser->name ?? 'Vendedor'))

@section('content')
<div class="min-h-screen bg-gradient-to-b from-branco-fresco to-verde-suave/5">
    <!-- Main Product Section -->
    <section class="py-8 lg:py-12">
        <div class="container mx-auto px-4">
            <div class="bg-white rounded-3xl shadow-lg overflow-hidden">
                <div class="lg:flex">
                    <!-- Product Image -->
                    <div class="lg:w-1/2">
                        <div class="relative">
                            @if($product->images && $product->images->count() > 0)
                                <img src="{{ asset($product->images->first()->file_path) }}" 
                                     alt="{{ $product->name }}" 
                                     class="w-full h-64 sm:h-80 lg:h-full object-cover"
                                     loading="lazy">
                            @else
                                <div class="w-full h-64 sm:h-80 lg:h-full bg-gradient-to-br from-verde-suave/20 to-dourado/20 flex items-center justify-center">
                                    <x-icon name="photo" size="12" class="text-cinza-pedra" />
                                </div>
                            @endif
                            
                            @if($product->featured)
                                <div class="absolute top-4 left-4 bg-verde-suave text-white px-3 py-1 rounded-full text-sm font-medium shadow-lg">
                                    Destaque
                                </div>
                            @endif
                            
                            @if($product->stock_quantity <= 0)
                                <div class="absolute inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center">
                                    <span class="bg-danger-600 text-white px-4 py-2 rounded-full font-medium shadow-lg">
                                        Esgotado
                                    </span>
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Product Information -->
                    <div class="lg:w-1/2 p-6 lg:p-10">
                        <!-- Product Header -->
                        <div class="mb-6">
                            <h1 class="text-3xl lg:text-4xl font-display font-semibold text-verde-mata mb-3 leading-tight">
                                {{ $product->name }}
                            </h1>
                            <div class="flex items-center space-x-2 text-cinza-pedra">
                                <x-icon name="user" size="5" />
                                <span class="font-roboto">por {{ $product->seller->user->name ?? $product->sellerUser->name ?? 'Vendedor' }}</span>
                            </div>
                        </div>
                        
                        <!-- Pricing -->
                        <div class="mb-8">
                            @if($product->compare_at_price && $product->compare_at_price > $product->price)
                                <div class="text-lg text-cinza-pedra line-through mb-2 font-roboto">
                                    De R$ {{ number_format($product->compare_at_price, 2, ',', '.') }}
                                </div>
                            @endif
                            <div class="text-4xl font-display font-bold text-orange-500 mb-2">
                                R$ {{ number_format($product->price, 2, ',', '.') }}
                            </div>
                            @if($product->compare_at_price && $product->compare_at_price > $product->price)
                                @php
                                    $discount = round((($product->compare_at_price - $product->price) / $product->compare_at_price) * 100);
                                @endphp
                                <span class="inline-block bg-success-100 text-success-600 px-3 py-1 rounded-full text-sm font-medium">
                                    Economia de {{ $discount }}%
                                </span>
                            @endif
                        </div>
                        
                        <!-- Short Description -->
                        @if($product->short_description)
                            <div class="mb-8">
                                <h3 class="text-xl font-display font-semibold text-verde-mata mb-3">Sobre o produto</h3>
                                <p class="text-cinza-pedra leading-relaxed">{{ $product->short_description }}</p>
                            </div>
                        @endif
                        
                        <!-- Product Info Table -->
                        <div class="mb-8">
                            <div class="bg-verde-suave/5 rounded-2xl p-6">
                                <h4 class="font-display font-semibold text-verde-mata mb-4">Informações técnicas</h4>
                                <div class="space-y-3">
                                    <div class="flex justify-between items-center py-2 border-b border-verde-suave/10 last:border-b-0">
                                        <span class="font-roboto text-cinza-pedra">Categoria</span>
                                        <span class="font-medium text-verde-mata">{{ $product->category->name ?? 'N/A' }}</span>
                                    </div>
                                    <div class="flex justify-between items-center py-2 border-b border-verde-suave/10 last:border-b-0">
                                        <span class="font-roboto text-cinza-pedra">Estoque disponível</span>
                                        <span class="font-medium text-verde-mata">{{ $product->stock_quantity }} unidades</span>
                                    </div>
                                    @if($product->views_count)
                                        <div class="flex justify-between items-center py-2">
                                            <span class="font-roboto text-cinza-pedra">Visualizações</span>
                                            <span class="font-medium text-verde-mata">{{ number_format($product->views_count) }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <!-- Action Buttons -->
                        <div class="flex flex-col sm:flex-row space-y-3 sm:space-y-0 sm:space-x-4">
                            <button class="flex-1 bg-verde-suave text-white px-6 py-4 rounded-xl font-medium hover:bg-verde-mata transition duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1 flex items-center justify-center space-x-2"
                                    onclick="addToCart({{ $product->id }})"
                                    @if($product->stock_quantity <= 0) disabled @endif>
                                <x-icon name="shopping-bag" size="5" />
                                <span>{{ $product->stock_quantity > 0 ? 'Adicionar ao Carrinho' : 'Indisponível' }}</span>
                            </button>
                            <button class="px-6 py-4 border-2 border-orange-500 text-orange-500 rounded-xl font-medium hover:bg-orange-500 hover:text-white transition duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1 flex items-center justify-center"
                                    onclick="toggleFavorite({{ $product->id }})">
                                <x-icon name="heart" size="5" />
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Full Description Section -->
    @if($product->description)
        <section class="py-12">
            <div class="container mx-auto px-4">
                <div class="bg-white rounded-3xl shadow-lg p-8 lg:p-12">
                    <h3 class="text-3xl font-display font-semibold text-verde-mata mb-6">Descrição Completa</h3>
                    <div class="prose prose-lg max-w-none text-cinza-pedra leading-relaxed">
                        <div class="whitespace-pre-wrap">{{ $product->description }}</div>
                    </div>
                </div>
            </div>
        </section>
    @endif
    
    <!-- Related Products Section -->
    @if($relatedProducts->count() > 0)
        <section class="py-16 bg-white">
            <div class="container mx-auto px-4">
                <div class="text-center mb-12">
                    <h3 class="text-4xl font-display font-semibold text-verde-mata mb-4">Produtos Relacionados</h3>
                    <p class="text-xl text-cinza-pedra">Outros produtos que podem interessar você</p>
                </div>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach($relatedProducts as $related)
                        <div class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-xl transition duration-500 border border-verde-suave/10 group">
                            <div class="relative">
                                <a href="{{ route('product.show', $related->id) }}">
                                    @if($related->images && $related->images->count() > 0)
                                        <img src="{{ asset($related->images->first()->file_path) }}" 
                                             alt="{{ $related->name }}" 
                                             class="w-full h-48 object-cover group-hover:scale-105 transition duration-500"
                                             loading="lazy">
                                    @else
                                        <div class="w-full h-48 bg-gradient-to-br from-verde-suave/20 to-dourado/20 flex items-center justify-center group-hover:scale-105 transition duration-500">
                                            <x-icon name="photo" size="10" class="text-cinza-pedra" />
                                        </div>
                                    @endif
                                </a>
                                
                                @if($related->featured)
                                    <div class="absolute top-3 left-3 bg-verde-suave text-white px-2 py-1 rounded-full text-xs font-medium">
                                        Destaque
                                    </div>
                                @endif
                                
                                @if($related->stock_quantity <= 0)
                                    <div class="absolute inset-0 bg-black/50 flex items-center justify-center">
                                        <span class="bg-danger-600 text-white text-xs font-medium px-2 py-1 rounded">
                                            Esgotado
                                        </span>
                                    </div>
                                @endif
                            </div>
                            
                            <div class="p-5">
                                <h4 class="font-display font-semibold text-verde-mata mb-2 line-clamp-2">
                                    <a href="{{ route('product.show', $related->id) }}" class="hover:text-verde-suave transition-colors">
                                        {{ $related->name }}
                                    </a>
                                </h4>
                                
                                <div class="text-sm text-cinza-pedra mb-3 flex items-center space-x-1">
                                    <x-icon name="user" size="4" />
                                    <span>{{ $related->seller->user->name ?? $related->sellerUser->name ?? 'Vendedor' }}</span>
                                </div>
                                
                                <div class="flex items-center justify-between mb-4">
                                    <div>
                                        @if($related->compare_at_price && $related->compare_at_price > $related->price)
                                            <div class="text-xs text-cinza-pedra line-through">
                                                R$ {{ number_format($related->compare_at_price, 2, ',', '.') }}
                                            </div>
                                        @endif
                                        <span class="text-xl font-display font-bold text-orange-500">
                                            R$ {{ number_format($related->price, 2, ',', '.') }}
                                        </span>
                                    </div>
                                    @if($related->category)
                                        <span class="text-xs bg-verde-suave/10 text-verde-mata px-2 py-1 rounded-full font-roboto">
                                            {{ $related->category->name }}
                                        </span>
                                    @endif
                                </div>
                                
                                <a href="{{ route('product.show', $related->id) }}" 
                                   class="w-full bg-verde-suave text-white px-4 py-2 rounded-xl text-sm font-medium hover:bg-verde-mata transition duration-300 text-center block">
                                    Ver Produto
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <!-- View More Products Link -->
                @if($product->category)
                    <div class="text-center mt-12">
                        <a href="{{ route('search') }}?categoria={{ $product->category->slug }}" 
                           class="inline-flex items-center space-x-2 bg-gradient-to-r from-verde-suave to-verde-mata text-white px-8 py-4 rounded-full font-medium hover:shadow-xl transition duration-300 transform hover:-translate-y-1">
                            <span>Ver mais produtos da categoria</span>
                            <x-icon name="arrow-right" size="4" />
                        </a>
                    </div>
                @endif
            </div>
        </section>
    @endif
</div>

<script>
    // Smooth scrolling enhancement
    document.addEventListener('DOMContentLoaded', function() {
        // Lazy loading optimization for images
        if ('IntersectionObserver' in window) {
            const imageObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        img.src = img.dataset.src;
                        img.classList.remove('lazy');
                        imageObserver.unobserve(img);
                    }
                });
            });
            
            document.querySelectorAll('img[data-src]').forEach(img => {
                imageObserver.observe(img);
            });
        }
        
        // Smooth scroll to product sections
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    });
    
    // Cart functionality placeholder
    function addToCart(productId) {
        // Show loading state
        const button = event.target.closest('button');
        const originalContent = button.innerHTML;
        button.innerHTML = '<svg class="animate-spin h-5 w-5 mr-2" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Adicionando...';
        button.disabled = true;
        
        // Simulate API call
        setTimeout(() => {
            button.innerHTML = originalContent;
            button.disabled = false;
            
            // Show success message
            const notification = document.createElement('div');
            notification.className = 'fixed top-4 right-4 bg-success-600 text-white px-6 py-3 rounded-xl shadow-lg z-50 animate-fade-in';
            notification.innerHTML = '✅ Produto adicionado ao carrinho!';
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.remove();
            }, 3000);
        }, 1000);
    }
    
    // Favorites functionality placeholder
    function toggleFavorite(productId) {
        const button = event.target.closest('button');
        const icon = button.querySelector('svg');
        
        // Toggle visual state
        if (icon.classList.contains('text-orange-500')) {
            icon.classList.remove('text-orange-500');
            icon.classList.add('text-red-500');
            button.classList.add('bg-red-50', 'border-red-500', 'text-red-500');
            button.classList.remove('border-orange-500', 'text-orange-500');
        } else {
            icon.classList.remove('text-red-500');
            icon.classList.add('text-orange-500');
            button.classList.remove('bg-red-50', 'border-red-500', 'text-red-500');
            button.classList.add('border-orange-500', 'text-orange-500');
        }
        
        // Show notification
        const notification = document.createElement('div');
        notification.className = 'fixed top-4 right-4 bg-orange-500 text-white px-6 py-3 rounded-xl shadow-lg z-50 animate-fade-in';
        notification.innerHTML = button.classList.contains('text-red-500') ? '❤️ Adicionado aos favoritos!' : '💔 Removido dos favoritos';
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.remove();
        }, 2000);
    }
</script>
@endsection