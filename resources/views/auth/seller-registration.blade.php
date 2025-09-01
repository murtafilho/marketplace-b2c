@extends('layouts.base')

@section('title', 'Criar Minha Loja - ' . config('app.name') . ' Marketplace')
@section('description', 'Crie sua loja no ' . config('app.name') . ' e comece a vender para a comunidade local. Processo simples e rápido.')

@section('content')
<div class="py-6 sm:py-8 lg:py-12">
    <div class="max-w-3xl mx-auto">
        <!-- Header -->
        <div class="text-center mb-6 sm:mb-8">
            <h1 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-gray-900 mb-3 sm:mb-4">
                Crie Sua <span class="text-emerald-600">Loja</span> no <span class="text-emerald-800">{{ config('app.name') }}</span>
            </h1>
            @if($userLoggedIn)
                <p class="text-base sm:text-lg text-gray-600 px-4 sm:px-0">
                    Olá {{ $user->name }}! Vamos configurar sua loja.
                </p>
            @else
                <p class="text-base sm:text-lg text-gray-600 px-4 sm:px-0">
                    Cadastre-se e crie sua loja em um único passo!
                </p>
            @endif
        </div>

        <!-- Benefícios -->
        <div class="bg-white rounded-xl sm:rounded-2xl shadow-soft p-4 sm:p-6 lg:p-8 mb-6 sm:mb-8">
            <h3 class="text-base sm:text-lg font-semibold mb-4 sm:mb-6 text-gray-900">Por que vender conosco?</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
                <div class="flex items-start space-x-3">
                    <div class="flex-shrink-0 w-8 h-8 sm:w-10 sm:h-10 bg-emerald-600/10 rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm sm:text-base font-semibold text-gray-900">Comissão Baixa</p>
                        <p class="text-xs sm:text-sm text-gray-600 mt-0.5 sm:mt-1">Apenas 10% sobre vendas</p>
                    </div>
                </div>
                <div class="flex items-start space-x-3">
                    <div class="flex-shrink-0 w-8 h-8 sm:w-10 sm:h-10 bg-blue-100 rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm sm:text-base font-semibold text-gray-900">Pagamento Seguro</p>
                        <p class="text-xs sm:text-sm text-gray-600 mt-0.5 sm:mt-1">Via Mercado Pago</p>
                    </div>
                </div>
                <div class="flex items-start space-x-3">
                    <div class="flex-shrink-0 w-8 h-8 sm:w-10 sm:h-10 bg-yellow-100 rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 text-emerald-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm sm:text-base font-semibold text-gray-900">Suporte Completo</p>
                        <p class="text-xs sm:text-sm text-gray-600 mt-0.5 sm:mt-1">Ajudamos você a vender</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Formulário -->
        <form method="POST" action="{{ route('seller.register.store') }}" class="space-y-6">
            @csrf

            <div class="bg-white rounded-xl sm:rounded-2xl shadow-soft p-4 sm:p-6 lg:p-8">
                @if(!$userLoggedIn)
                <!-- Seção 1: Dados Pessoais (só para não logados) -->
                <div class="mb-6 sm:mb-8">
                    <h2 class="text-lg sm:text-xl font-semibold text-gray-900 mb-4 sm:mb-6 flex items-center">
                        <span class="bg-emerald-600 text-white rounded-full w-7 h-7 sm:w-8 sm:h-8 flex items-center justify-center mr-2 sm:mr-3 text-xs sm:text-sm font-bold">1</span>
                        <span class="text-base sm:text-xl">Seus Dados Pessoais</span>
                    </h2>
                    
                    <div class="space-y-4 sm:space-y-5">
                        <!-- Nome -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1.5 sm:mb-2">
                                Nome Completo <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   name="name" 
                                   id="name" 
                                   value="{{ old('name') }}"
                                   required
                                   class="w-full px-3 sm:px-4 py-2.5 sm:py-3 text-sm sm:text-base border border-gray-300 rounded-lg sm:rounded-xl focus:ring-2 focus:ring-emerald-600 focus:border-emerald-600 transition-colors @error('name') border-red-500 @enderror"
                                   placeholder="João Silva">
                            @error('name')
                                <p class="mt-1 text-xs sm:text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5 sm:mb-2">
                                E-mail <span class="text-red-500">*</span>
                            </label>
                            <input type="email" 
                                   name="email" 
                                   id="email" 
                                   value="{{ old('email') }}"
                                   required
                                   class="w-full px-3 sm:px-4 py-2.5 sm:py-3 text-sm sm:text-base border border-gray-300 rounded-lg sm:rounded-xl focus:ring-2 focus:ring-emerald-600 focus:border-emerald-600 transition-colors @error('email') border-red-500 @enderror"
                                   placeholder="joao@exemplo.com">
                            @error('email')
                                <p class="mt-1 text-xs sm:text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Telefone -->
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 mb-1.5 sm:mb-2">
                                Telefone/WhatsApp <span class="text-red-500">*</span>
                            </label>
                            <input type="tel" 
                                   name="phone" 
                                   id="phone" 
                                   value="{{ old('phone') }}"
                                   required
                                   class="w-full px-3 sm:px-4 py-2.5 sm:py-3 text-sm sm:text-base border border-gray-300 rounded-lg sm:rounded-xl focus:ring-2 focus:ring-emerald-600 focus:border-emerald-600 transition-colors @error('phone') border-red-500 @enderror"
                                   placeholder="(11) 98765-4321">
                            @error('phone')
                                <p class="mt-1 text-xs sm:text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Senha -->
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-1.5 sm:mb-2">
                                Senha <span class="text-red-500">*</span>
                            </label>
                            <input type="password" 
                                   name="password" 
                                   id="password" 
                                   required
                                   class="w-full px-3 sm:px-4 py-2.5 sm:py-3 text-sm sm:text-base border border-gray-300 rounded-lg sm:rounded-xl focus:ring-2 focus:ring-emerald-600 focus:border-emerald-600 transition-colors @error('password') border-red-500 @enderror"
                                   placeholder="Mínimo 8 caracteres">
                            @error('password')
                                <p class="mt-1 text-xs sm:text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Confirmar Senha -->
                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1.5 sm:mb-2">
                                Confirmar Senha <span class="text-red-500">*</span>
                            </label>
                            <input type="password" 
                                   name="password_confirmation" 
                                   id="password_confirmation" 
                                   required
                                   class="w-full px-3 sm:px-4 py-2.5 sm:py-3 text-sm sm:text-base border border-gray-300 rounded-lg sm:rounded-xl focus:ring-2 focus:ring-emerald-600 focus:border-emerald-600 transition-colors"
                                   placeholder="Digite a senha novamente">
                        </div>
                    </div>
                </div>

                <div class="border-t border-gray-200 my-6 sm:my-8"></div>
                @endif

                <!-- Seção 2: Dados da Loja -->
                <div>
                    <h2 class="text-lg sm:text-xl font-semibold text-gray-900 mb-4 sm:mb-6 flex items-center">
                        <span class="bg-emerald-600 text-white rounded-full w-7 h-7 sm:w-8 sm:h-8 flex items-center justify-center mr-2 sm:mr-3 text-xs sm:text-sm font-bold">
                            {{ $userLoggedIn ? '1' : '2' }}
                        </span>
                        <span class="text-base sm:text-xl">Informações da Sua Loja</span>
                    </h2>

                    <!-- Nome da Loja -->
                    <div class="mb-4 sm:mb-6">
                        <label for="company_name" class="block text-sm font-medium text-gray-700 mb-1.5 sm:mb-2">
                            Nome da Loja <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="company_name" 
                               id="company_name" 
                               value="{{ old('company_name') }}"
                               required
                               class="w-full px-3 sm:px-4 py-2.5 sm:py-3 text-sm sm:text-base border border-gray-300 rounded-lg sm:rounded-xl focus:ring-2 focus:ring-emerald-600 focus:border-emerald-600 transition-colors @error('company_name') border-red-500 @enderror"
                               placeholder="Ex: Loja do João">
                        <p class="mt-1 text-xs sm:text-sm text-gray-500">Este nome aparecerá para os clientes</p>
                        @error('company_name')
                            <p class="mt-1 text-xs sm:text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Descrição da Loja -->
                    <div class="mb-4 sm:mb-6">
                        <label for="company_description" class="block text-sm font-medium text-gray-700 mb-1.5 sm:mb-2">
                            Descrição da Loja <span class="text-gray-400 text-xs">(opcional)</span>
                        </label>
                        <textarea name="company_description" 
                                  id="company_description" 
                                  rows="3"
                                  class="w-full px-3 sm:px-4 py-2.5 sm:py-3 text-sm sm:text-base border border-gray-300 rounded-lg sm:rounded-xl focus:ring-2 focus:ring-emerald-600 focus:border-emerald-600 transition-colors @error('company_description') border-red-500 @enderror"
                                  placeholder="Conte um pouco sobre sua loja e os produtos que você vende...">{{ old('company_description') }}</textarea>
                        <p class="mt-1 text-xs sm:text-sm text-gray-500">Máximo 500 caracteres</p>
                        @error('company_description')
                            <p class="mt-1 text-xs sm:text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Termos e Condições -->
                <div class="mt-6 sm:mt-8 bg-gray-50 rounded-lg sm:rounded-xl p-4 sm:p-6">
                    <div class="flex items-start">
                        <input type="checkbox" 
                               name="accept_terms" 
                               id="accept_terms" 
                               value="1"
                               required
                               class="mt-0.5 sm:mt-1 mr-2 sm:mr-3 h-4 w-4 text-emerald-600 focus:ring-emerald-600 border-gray-300 rounded @error('accept_terms') border-red-500 @enderror">
                        <label for="accept_terms" class="text-xs sm:text-sm text-gray-700 leading-relaxed">
                            Li e aceito os <a href="#" class="text-emerald-600 hover:text-emerald-600-dark font-medium">Termos de Uso</a> e a 
                            <a href="#" class="text-emerald-600 hover:text-emerald-600-dark font-medium">Política de Privacidade</a> do Marketplace.
                            Concordo com a comissão de 10% sobre minhas vendas.
                        </label>
                    </div>
                    @error('accept_terms')
                        <p class="mt-1 text-xs sm:text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Botões de Ação -->
                <div class="mt-6 sm:mt-8 flex flex-col-reverse sm:flex-row items-center justify-between gap-4">
                    @if($userLoggedIn)
                        <a href="{{ route('home') }}" class="text-sm sm:text-base text-gray-600 hover:text-gray-900 font-medium flex items-center space-x-1 sm:space-x-2 group">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                            <span>Voltar</span>
                        </a>
                    @else
                        <p class="text-xs sm:text-sm text-gray-600 text-center sm:text-left">
                            Já tem uma conta? 
                            <a href="{{ route('login') }}" class="text-emerald-600 hover:text-emerald-600-dark font-medium">Faça login</a>
                        </p>
                    @endif

                    <button type="submit" 
                            class="w-full sm:w-auto bg-gradient-to-r from-emerald-600 to-emerald-700 text-white px-6 sm:px-8 py-2.5 sm:py-3 rounded-lg sm:rounded-xl font-semibold sm:font-bold hover:shadow-lg transform hover:scale-105 transition-all flex items-center justify-center space-x-2">
                        <span class="text-sm sm:text-base">{{ $userLoggedIn ? 'Criar Minha Loja' : 'Criar Conta e Loja' }}</span>
                        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>
                </div>
            </div>
        </form>

        <!-- Informações Adicionais -->
        <div class="mt-6 sm:mt-8 text-center text-xs sm:text-sm text-gray-600 px-4 sm:px-0">
            <p>Após o cadastro, você precisará completar algumas informações adicionais</p>
            <p>para que sua loja seja aprovada e você possa começar a vender.</p>
        </div>

        <!-- FAQ Rápido -->
        <div class="mt-8 sm:mt-12 bg-white rounded-xl sm:rounded-2xl shadow-soft p-4 sm:p-6 lg:p-8">
            <h3 class="text-lg sm:text-xl font-bold mb-4 sm:mb-6 text-gray-900">Perguntas Frequentes</h3>
            
            <div class="space-y-4 sm:space-y-6">
                <div class="border-l-4 border-emerald-600 pl-3 sm:pl-4">
                    <p class="text-sm sm:text-base font-semibold text-gray-900">Quanto tempo leva para aprovar minha loja?</p>
                    <p class="text-xs sm:text-sm text-gray-600 mt-1 sm:mt-2">Normalmente em até 24 horas úteis após completar o cadastro.</p>
                </div>
                
                <div class="border-l-4 border-blue-600 pl-3 sm:pl-4">
                    <p class="text-sm sm:text-base font-semibold text-gray-900">Posso vender qualquer tipo de produto?</p>
                    <p class="text-xs sm:text-sm text-gray-600 mt-1 sm:mt-2">Sim, desde que sejam produtos legais e estejam de acordo com nossos termos.</p>
                </div>
                
                <div class="border-l-4 border-yellow-600 pl-3 sm:pl-4">
                    <p class="text-sm sm:text-base font-semibold text-gray-900">Como recebo o pagamento das vendas?</p>
                    <p class="text-xs sm:text-sm text-gray-600 mt-1 sm:mt-2">Os pagamentos são processados via Mercado Pago e transferidos para sua conta bancária.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection