@extends('layouts.seller')

@section('title', 'Configurações da Loja')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Configurações da Loja</h1>
        <p class="text-gray-600">Gerencie as informações e configurações da sua loja</p>
    </div>

    <div x-data="{ activeTab: 'general' }" class="space-y-6">
        <!-- Tabs -->
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-8">
                <button @click="activeTab = 'general'" 
                    :class="{'border-blue-500 text-blue-600': activeTab === 'general', 'border-transparent text-gray-500': activeTab !== 'general'}"
                    class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                    Informações Gerais
                </button>
                <button @click="activeTab = 'appearance'" 
                    :class="{'border-blue-500 text-blue-600': activeTab === 'appearance', 'border-transparent text-gray-500': activeTab !== 'appearance'}"
                    class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                    Aparência
                </button>
                <button @click="activeTab = 'delivery'" 
                    :class="{'border-blue-500 text-blue-600': activeTab === 'delivery', 'border-transparent text-gray-500': activeTab !== 'delivery'}"
                    class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                    Entrega e Pagamento
                </button>
                <button @click="activeTab = 'banking'" 
                    :class="{'border-blue-500 text-blue-600': activeTab === 'banking', 'border-transparent text-gray-500': activeTab !== 'banking'}"
                    class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                    Dados Bancários
                </button>
                <button @click="activeTab = 'seo'" 
                    :class="{'border-blue-500 text-blue-600': activeTab === 'seo', 'border-transparent text-gray-500': activeTab !== 'seo'}"
                    class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                    SEO
                </button>
            </nav>
        </div>

        <!-- General Information Tab -->
        <div x-show="activeTab === 'general'" class="bg-white rounded-lg shadow-sm border p-6">
            <form method="POST" action="{{ route('seller.profile.update') }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Nome da Loja *
                        </label>
                        <input type="text" name="name" value="{{ old('name', $store->name) }}" 
                            class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        @error('name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Slug da Loja *
                        </label>
                        <input type="text" name="slug" value="{{ old('slug', $store->slug) }}" 
                            class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        <p class="text-xs text-gray-500 mt-1">URL: {{ url('/loja/') }}/{{ $store->slug }}</p>
                        @error('slug')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Descrição da Loja
                        </label>
                        <textarea name="description" rows="3" 
                            class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('description', $store->description) }}</textarea>
                        @error('description')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Telefone *
                        </label>
                        <input type="tel" name="phone" value="{{ old('phone', $store->phone) }}" 
                            class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        @error('phone')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            WhatsApp
                        </label>
                        <input type="tel" name="whatsapp" value="{{ old('whatsapp', $store->whatsapp) }}" 
                            class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('whatsapp')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Email *
                        </label>
                        <input type="email" name="email" value="{{ old('email', $store->email) }}" 
                            class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        @error('email')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            CNPJ *
                        </label>
                        <input type="text" name="cnpj" value="{{ old('cnpj', $store->cnpj) }}" 
                            class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        @error('cnpj')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Endereço *
                        </label>
                        <input type="text" name="address" value="{{ old('address', $store->address) }}" 
                            class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        @error('address')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Cidade *
                        </label>
                        <input type="text" name="city" value="{{ old('city', $store->city) }}" 
                            class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        @error('city')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Estado *
                        </label>
                        <select name="state" 
                            class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                            <option value="">Selecione</option>
                            <option value="AC" {{ old('state', $store->state) === 'AC' ? 'selected' : '' }}>Acre</option>
                            <option value="AL" {{ old('state', $store->state) === 'AL' ? 'selected' : '' }}>Alagoas</option>
                            <option value="AP" {{ old('state', $store->state) === 'AP' ? 'selected' : '' }}>Amapá</option>
                            <option value="AM" {{ old('state', $store->state) === 'AM' ? 'selected' : '' }}>Amazonas</option>
                            <option value="BA" {{ old('state', $store->state) === 'BA' ? 'selected' : '' }}>Bahia</option>
                            <option value="CE" {{ old('state', $store->state) === 'CE' ? 'selected' : '' }}>Ceará</option>
                            <option value="DF" {{ old('state', $store->state) === 'DF' ? 'selected' : '' }}>Distrito Federal</option>
                            <option value="ES" {{ old('state', $store->state) === 'ES' ? 'selected' : '' }}>Espírito Santo</option>
                            <option value="GO" {{ old('state', $store->state) === 'GO' ? 'selected' : '' }}>Goiás</option>
                            <option value="MA" {{ old('state', $store->state) === 'MA' ? 'selected' : '' }}>Maranhão</option>
                            <option value="MT" {{ old('state', $store->state) === 'MT' ? 'selected' : '' }}>Mato Grosso</option>
                            <option value="MS" {{ old('state', $store->state) === 'MS' ? 'selected' : '' }}>Mato Grosso do Sul</option>
                            <option value="MG" {{ old('state', $store->state) === 'MG' ? 'selected' : '' }}>Minas Gerais</option>
                            <option value="PA" {{ old('state', $store->state) === 'PA' ? 'selected' : '' }}>Pará</option>
                            <option value="PB" {{ old('state', $store->state) === 'PB' ? 'selected' : '' }}>Paraíba</option>
                            <option value="PR" {{ old('state', $store->state) === 'PR' ? 'selected' : '' }}>Paraná</option>
                            <option value="PE" {{ old('state', $store->state) === 'PE' ? 'selected' : '' }}>Pernambuco</option>
                            <option value="PI" {{ old('state', $store->state) === 'PI' ? 'selected' : '' }}>Piauí</option>
                            <option value="RJ" {{ old('state', $store->state) === 'RJ' ? 'selected' : '' }}>Rio de Janeiro</option>
                            <option value="RN" {{ old('state', $store->state) === 'RN' ? 'selected' : '' }}>Rio Grande do Norte</option>
                            <option value="RS" {{ old('state', $store->state) === 'RS' ? 'selected' : '' }}>Rio Grande do Sul</option>
                            <option value="RO" {{ old('state', $store->state) === 'RO' ? 'selected' : '' }}>Rondônia</option>
                            <option value="RR" {{ old('state', $store->state) === 'RR' ? 'selected' : '' }}>Roraima</option>
                            <option value="SC" {{ old('state', $store->state) === 'SC' ? 'selected' : '' }}>Santa Catarina</option>
                            <option value="SP" {{ old('state', $store->state) === 'SP' ? 'selected' : '' }}>São Paulo</option>
                            <option value="SE" {{ old('state', $store->state) === 'SE' ? 'selected' : '' }}>Sergipe</option>
                            <option value="TO" {{ old('state', $store->state) === 'TO' ? 'selected' : '' }}>Tocantins</option>
                        </select>
                        @error('state')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            CEP *
                        </label>
                        <input type="text" name="zip_code" value="{{ old('zip_code', $store->zip_code) }}" 
                            class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        @error('zip_code')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-6">
                    <button type="submit" 
                        class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 transition">
                        Salvar Informações Gerais
                    </button>
                </div>
            </form>
        </div>

        <!-- Appearance Tab -->
        <div x-show="activeTab === 'appearance'" class="bg-white rounded-lg shadow-sm border p-6">
            <form method="POST" action="{{ route('seller.profile.update') }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Logo da Loja
                        </label>
                        @if($store->logo)
                            <div class="mb-4">
                                <img src="{{ Storage::url($store->logo) }}" alt="Logo atual" class="w-24 h-24 object-cover rounded">
                                <p class="text-xs text-gray-500 mt-1">Logo atual</p>
                            </div>
                        @endif
                        <input type="file" name="logo" accept="image/*" 
                            class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <p class="text-xs text-gray-500 mt-1">Tamanho recomendado: 200x200px. Máximo 2MB.</p>
                        @error('logo')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Banner da Loja
                        </label>
                        @if($store->banner)
                            <div class="mb-4">
                                <img src="{{ Storage::url($store->banner) }}" alt="Banner atual" class="w-full h-32 object-cover rounded">
                                <p class="text-xs text-gray-500 mt-1">Banner atual</p>
                            </div>
                        @endif
                        <input type="file" name="banner" accept="image/*" 
                            class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <p class="text-xs text-gray-500 mt-1">Tamanho recomendado: 1200x300px. Máximo 5MB.</p>
                        @error('banner')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Facebook
                            </label>
                            <input type="url" name="facebook_url" value="{{ old('facebook_url', $store->facebook_url) }}" 
                                class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('facebook_url')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Instagram
                            </label>
                            <input type="url" name="instagram_url" value="{{ old('instagram_url', $store->instagram_url) }}" 
                                class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('instagram_url')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Twitter
                            </label>
                            <input type="url" name="twitter_url" value="{{ old('twitter_url', $store->twitter_url) }}" 
                                class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('twitter_url')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                YouTube
                            </label>
                            <input type="url" name="youtube_url" value="{{ old('youtube_url', $store->youtube_url) }}" 
                                class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('youtube_url')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="mt-6">
                    <button type="submit" 
                        class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 transition">
                        Salvar Aparência
                    </button>
                </div>
            </form>
        </div>

        <!-- Delivery & Payment Tab -->
        <div x-show="activeTab === 'delivery'" class="bg-white rounded-lg shadow-sm border p-6">
            <form method="POST" action="{{ route('seller.profile.update') }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Taxa de Entrega (R$)
                            </label>
                            <input type="number" step="0.01" min="0" name="delivery_fee" 
                                value="{{ old('delivery_fee', $store->delivery_fee) }}" 
                                class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('delivery_fee')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Pedido Mínimo (R$)
                            </label>
                            <input type="number" step="0.01" min="0" name="min_order_value" 
                                value="{{ old('min_order_value', $store->min_order_value) }}" 
                                class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('min_order_value')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Tempo de Entrega Estimado
                        </label>
                        <input type="text" name="estimated_delivery_time" 
                            value="{{ old('estimated_delivery_time', $store->estimated_delivery_time) }}" 
                            placeholder="Ex: 30-45 minutos, 1-2 horas"
                            class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('estimated_delivery_time')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center space-x-6">
                        <label class="flex items-center">
                            <input type="checkbox" name="accepts_pickup" value="1" 
                                {{ old('accepts_pickup', $store->accepts_pickup) ? 'checked' : '' }}
                                class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-700">Aceita retirada no local</span>
                        </label>

                        <label class="flex items-center">
                            <input type="checkbox" name="accepts_delivery" value="1" 
                                {{ old('accepts_delivery', $store->accepts_delivery) ? 'checked' : '' }}
                                class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-700">Faz entrega</span>
                        </label>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-3">
                            Formas de Pagamento Aceitas
                        </label>
                        @php 
                            $acceptedMethods = json_decode($store->payment_methods ?? '[]', true);
                        @endphp
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                            <label class="flex items-center">
                                <input type="checkbox" name="payment_methods[]" value="credit_card" 
                                    {{ in_array('credit_card', $acceptedMethods) ? 'checked' : '' }}
                                    class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700">Cartão de Crédito</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="payment_methods[]" value="debit_card" 
                                    {{ in_array('debit_card', $acceptedMethods) ? 'checked' : '' }}
                                    class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700">Cartão de Débito</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="payment_methods[]" value="pix" 
                                    {{ in_array('pix', $acceptedMethods) ? 'checked' : '' }}
                                    class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700">PIX</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="payment_methods[]" value="boleto" 
                                    {{ in_array('boleto', $acceptedMethods) ? 'checked' : '' }}
                                    class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700">Boleto</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="payment_methods[]" value="cash" 
                                    {{ in_array('cash', $acceptedMethods) ? 'checked' : '' }}
                                    class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700">Dinheiro</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="mt-6">
                    <button type="submit" 
                        class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 transition">
                        Salvar Configurações de Entrega
                    </button>
                </div>
            </form>
        </div>

        <!-- Banking Tab -->
        <div x-show="activeTab === 'banking'" class="bg-white rounded-lg shadow-sm border p-6">
            <form method="POST" action="{{ route('seller.profile.banking') }}">
                @csrf
                @method('PUT')
                
                @php 
                    $bankAccount = json_decode($store->bank_account ?? '{}', true);
                @endphp

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Nome do Banco *
                        </label>
                        <input type="text" name="bank_name" value="{{ old('bank_name', $bankAccount['bank_name'] ?? '') }}" 
                            class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Código do Banco *
                        </label>
                        <input type="text" name="bank_code" value="{{ old('bank_code', $bankAccount['bank_code'] ?? '') }}" 
                            class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Agência *
                        </label>
                        <input type="text" name="agency" value="{{ old('agency', $bankAccount['agency'] ?? '') }}" 
                            class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Número da Conta *
                        </label>
                        <input type="text" name="account_number" value="{{ old('account_number', $bankAccount['account_number'] ?? '') }}" 
                            class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Tipo de Conta *
                        </label>
                        <select name="account_type" 
                            class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                            <option value="">Selecione</option>
                            <option value="checking" {{ old('account_type', $bankAccount['account_type'] ?? '') === 'checking' ? 'selected' : '' }}>Conta Corrente</option>
                            <option value="savings" {{ old('account_type', $bankAccount['account_type'] ?? '') === 'savings' ? 'selected' : '' }}>Poupança</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Nome do Titular *
                        </label>
                        <input type="text" name="account_holder_name" value="{{ old('account_holder_name', $bankAccount['account_holder_name'] ?? '') }}" 
                            class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            CPF/CNPJ do Titular *
                        </label>
                        <input type="text" name="account_holder_document" value="{{ old('account_holder_document', $bankAccount['account_holder_document'] ?? '') }}" 
                            class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    </div>
                </div>

                <div class="mt-6">
                    <button type="submit" 
                        class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 transition">
                        Salvar Dados Bancários
                    </button>
                </div>
            </form>
        </div>

        <!-- SEO Tab -->
        <div x-show="activeTab === 'seo'" class="bg-white rounded-lg shadow-sm border p-6">
            <form method="POST" action="{{ route('seller.profile.seo') }}">
                @csrf
                @method('PUT')
                
                @php 
                    $seoSettings = json_decode($store->seo_settings ?? '{}', true);
                @endphp

                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Título SEO (Meta Title)
                        </label>
                        <input type="text" name="meta_title" maxlength="60" 
                            value="{{ old('meta_title', $seoSettings['meta_title'] ?? '') }}" 
                            class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <p class="text-xs text-gray-500 mt-1">Máximo 60 caracteres. Será exibido nos resultados do Google.</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Descrição SEO (Meta Description)
                        </label>
                        <textarea name="meta_description" maxlength="160" rows="3"
                            class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('meta_description', $seoSettings['meta_description'] ?? '') }}</textarea>
                        <p class="text-xs text-gray-500 mt-1">Máximo 160 caracteres. Será exibida nos resultados do Google.</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Palavras-chave (Meta Keywords)
                        </label>
                        <input type="text" name="meta_keywords" 
                            value="{{ old('meta_keywords', $seoSettings['meta_keywords'] ?? '') }}" 
                            class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <p class="text-xs text-gray-500 mt-1">Separe as palavras-chave por vírgula. Ex: roupas, moda, feminino</p>
                    </div>
                </div>

                <div class="mt-6">
                    <button type="submit" 
                        class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 transition">
                        Salvar Configurações de SEO
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection