@extends('layouts.guest')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-3xl mx-auto">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold text-gray-900 mb-2">
                🏪 Crie Sua Loja no Marketplace
            </h1>
            @if($userLoggedIn)
                <p class="text-lg text-gray-600">
                    Olá {{ $user->name }}! Vamos configurar sua loja.
                </p>
            @else
                <p class="text-lg text-gray-600">
                    Cadastre-se e crie sua loja em um único passo!
                </p>
            @endif
        </div>

        <!-- Benefícios -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
            <h3 class="text-lg font-semibold mb-4 text-gray-800">✨ Por que vender conosco?</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="flex items-start">
                    <svg class="w-6 h-6 text-green-500 mr-2 flex-shrink-0 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div>
                        <p class="font-medium text-gray-800">Comissão Baixa</p>
                        <p class="text-sm text-gray-600">Apenas 10% sobre vendas</p>
                    </div>
                </div>
                <div class="flex items-start">
                    <svg class="w-6 h-6 text-green-500 mr-2 flex-shrink-0 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div>
                        <p class="font-medium text-gray-800">Pagamento Seguro</p>
                        <p class="text-sm text-gray-600">Via Mercado Pago</p>
                    </div>
                </div>
                <div class="flex items-start">
                    <svg class="w-6 h-6 text-green-500 mr-2 flex-shrink-0 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div>
                        <p class="font-medium text-gray-800">Suporte Completo</p>
                        <p class="text-sm text-gray-600">Ajudamos você a vender</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Formulário -->
        <form method="POST" action="{{ route('seller.register.store') }}" class="space-y-6">
            @csrf

            <div class="bg-white rounded-lg shadow-sm p-6">
                @if(!$userLoggedIn)
                <!-- Seção 1: Dados Pessoais (só para não logados) -->
                <div class="mb-8">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                        <span class="bg-blue-500 text-white rounded-full w-8 h-8 flex items-center justify-center mr-3 text-sm">1</span>
                        Seus Dados Pessoais
                    </h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Nome -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                                Nome Completo *
                            </label>
                            <input type="text" 
                                   name="name" 
                                   id="name" 
                                   value="{{ old('name') }}"
                                   required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('name') border-red-500 @enderror"
                                   placeholder="João Silva">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                                E-mail *
                            </label>
                            <input type="email" 
                                   name="email" 
                                   id="email" 
                                   value="{{ old('email') }}"
                                   required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('email') border-red-500 @enderror"
                                   placeholder="joao@exemplo.com">
                            @error('email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Telefone -->
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">
                                Telefone/WhatsApp *
                            </label>
                            <input type="tel" 
                                   name="phone" 
                                   id="phone" 
                                   value="{{ old('phone') }}"
                                   required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('phone') border-red-500 @enderror"
                                   placeholder="(11) 98765-4321">
                            @error('phone')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Senha -->
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                                Senha *
                            </label>
                            <input type="password" 
                                   name="password" 
                                   id="password" 
                                   required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('password') border-red-500 @enderror"
                                   placeholder="Mínimo 8 caracteres">
                            @error('password')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Confirmar Senha -->
                        <div class="md:col-span-2">
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">
                                Confirmar Senha *
                            </label>
                            <input type="password" 
                                   name="password_confirmation" 
                                   id="password_confirmation" 
                                   required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="Digite a senha novamente">
                        </div>
                    </div>
                </div>

                <hr class="my-8">
                @endif

                <!-- Seção 2: Dados da Loja -->
                <div>
                    <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                        <span class="bg-blue-500 text-white rounded-full w-8 h-8 flex items-center justify-center mr-3 text-sm">
                            {{ $userLoggedIn ? '1' : '2' }}
                        </span>
                        Informações da Sua Loja
                    </h2>

                    <!-- Nome da Loja -->
                    <div class="mb-4">
                        <label for="company_name" class="block text-sm font-medium text-gray-700 mb-1">
                            Nome da Loja *
                        </label>
                        <input type="text" 
                               name="company_name" 
                               id="company_name" 
                               value="{{ old('company_name') }}"
                               required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('company_name') border-red-500 @enderror"
                               placeholder="Ex: Loja do João">
                        <p class="mt-1 text-sm text-gray-500">Este nome aparecerá para os clientes</p>
                        @error('company_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Descrição da Loja -->
                    <div class="mb-4">
                        <label for="company_description" class="block text-sm font-medium text-gray-700 mb-1">
                            Descrição da Loja (opcional)
                        </label>
                        <textarea name="company_description" 
                                  id="company_description" 
                                  rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('company_description') border-red-500 @enderror"
                                  placeholder="Conte um pouco sobre sua loja e os produtos que você vende...">{{ old('company_description') }}</textarea>
                        <p class="mt-1 text-sm text-gray-500">Máximo 500 caracteres</p>
                        @error('company_description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Termos e Condições -->
                <div class="mt-6 bg-gray-50 rounded-lg p-4">
                    <div class="flex items-start">
                        <input type="checkbox" 
                               name="accept_terms" 
                               id="accept_terms" 
                               value="1"
                               required
                               class="mt-1 mr-3 h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded @error('accept_terms') border-red-500 @enderror">
                        <label for="accept_terms" class="text-sm text-gray-700">
                            Li e aceito os <a href="#" class="text-blue-600 hover:underline">Termos de Uso</a> e a 
                            <a href="#" class="text-blue-600 hover:underline">Política de Privacidade</a> do Marketplace.
                            Concordo com a comissão de 10% sobre minhas vendas.
                        </label>
                    </div>
                    @error('accept_terms')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Botões de Ação -->
                <div class="mt-8 flex items-center justify-between">
                    @if($userLoggedIn)
                        <a href="{{ route('home') }}" class="text-gray-600 hover:text-gray-800">
                            ← Voltar
                        </a>
                    @else
                        <p class="text-sm text-gray-600">
                            Já tem uma conta? 
                            <a href="{{ route('login') }}" class="text-blue-600 hover:underline">Faça login</a>
                        </p>
                    @endif

                    <button type="submit" 
                            class="bg-blue-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                        {{ $userLoggedIn ? 'Criar Minha Loja' : 'Criar Conta e Loja' }} →
                    </button>
                </div>
            </div>
        </form>

        <!-- Informações Adicionais -->
        <div class="mt-8 text-center text-sm text-gray-600">
            <p>Após o cadastro, você precisará completar algumas informações adicionais</p>
            <p>para que sua loja seja aprovada e você possa começar a vender.</p>
        </div>

        <!-- FAQ Rápido -->
        <div class="mt-12 bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold mb-4 text-gray-800">❓ Perguntas Frequentes</h3>
            
            <div class="space-y-4">
                <div>
                    <p class="font-medium text-gray-800">Quanto tempo leva para aprovar minha loja?</p>
                    <p class="text-sm text-gray-600 mt-1">Normalmente em até 24 horas úteis após completar o cadastro.</p>
                </div>
                
                <div>
                    <p class="font-medium text-gray-800">Posso vender qualquer tipo de produto?</p>
                    <p class="text-sm text-gray-600 mt-1">Sim, desde que sejam produtos legais e estejam de acordo com nossos termos.</p>
                </div>
                
                <div>
                    <p class="font-medium text-gray-800">Como recebo o pagamento das vendas?</p>
                    <p class="text-sm text-gray-600 mt-1">Os pagamentos são processados via Mercado Pago e transferidos para sua conta bancária.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection