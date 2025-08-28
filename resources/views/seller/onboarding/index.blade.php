<x-layouts.marketplace>
    <div class="max-w-4xl mx-auto px-4 py-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Completar Cadastro de Vendedor</h1>
            <p class="mt-2 text-gray-600">
                Para começar a vender no marketplace, complete as informações abaixo e envie os documentos necessários.
            </p>
        </div>

        <!-- Progress Steps -->
        <div class="mb-8">
            <div class="flex items-center">
                <div class="flex items-center text-sm font-medium text-indigo-600">
                    <span class="flex items-center justify-center w-8 h-8 border-2 border-indigo-600 rounded-full bg-indigo-600 text-white mr-3">
                        1
                    </span>
                    Dados Pessoais/Empresariais
                </div>
                <div class="flex-1 h-px bg-gray-200 mx-4"></div>
                <div class="flex items-center text-sm font-medium text-gray-400">
                    <span class="flex items-center justify-center w-8 h-8 border-2 border-gray-200 rounded-full mr-3">
                        2
                    </span>
                    Aprovação Admin
                </div>
                <div class="flex-1 h-px bg-gray-200 mx-4"></div>
                <div class="flex items-center text-sm font-medium text-gray-400">
                    <span class="flex items-center justify-center w-8 h-8 border-2 border-gray-200 rounded-full mr-3">
                        3
                    </span>
                    Conectar Mercado Pago
                </div>
            </div>
        </div>

        <!-- Form -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Informações do Vendedor</h2>
            </div>

            <form method="POST" action="{{ route('seller.onboarding.store') }}" enctype="multipart/form-data" 
                  class="p-6" x-data="{ documentType: '{{ old('document_type', 'cpf') }}' }">
                @csrf

                <!-- Company Name -->
                <div class="mb-6">
                    <x-input-label for="company_name" :value="__('Nome da Empresa/Vendedor')" />
                    <x-text-input id="company_name" name="company_name" type="text" 
                                  class="mt-1 block w-full" :value="old('company_name', $profile->company_name)" required />
                    <x-input-error class="mt-2" :messages="$errors->get('company_name')" />
                </div>

                <!-- Document Type and Number -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <div>
                        <x-input-label for="document_type" :value="__('Tipo de Documento')" />
                        <select id="document_type" name="document_type" x-model="documentType" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                            <option value="cpf" {{ old('document_type', $profile->document_type) == 'cpf' ? 'selected' : '' }}>CPF</option>
                            <option value="cnpj" {{ old('document_type', $profile->document_type) == 'cnpj' ? 'selected' : '' }}>CNPJ</option>
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('document_type')" />
                    </div>

                    <div class="md:col-span-2">
                        <x-input-label for="document_number" x-text="documentType === 'cpf' ? 'Número do CPF' : 'Número do CNPJ'" />
                        <x-text-input id="document_number" name="document_number" type="text" 
                                      class="mt-1 block w-full" :value="old('document_number', $profile->document_number)" 
                                      x-bind:placeholder="documentType === 'cpf' ? '000.000.000-00' : '00.000.000/0001-00'" required />
                        <x-input-error class="mt-2" :messages="$errors->get('document_number')" />
                    </div>
                </div>

                <!-- Contact -->
                <div class="mb-6">
                    <x-input-label for="phone" :value="__('Telefone/WhatsApp')" />
                    <x-text-input id="phone" name="phone" type="tel" 
                                  class="mt-1 block w-full" :value="old('phone', $profile->phone ?? auth()->user()->phone)" 
                                  placeholder="(11) 99999-9999" required />
                    <x-input-error class="mt-2" :messages="$errors->get('phone')" />
                </div>

                <!-- Address -->
                <div class="mb-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Endereço</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <x-input-label for="address" :value="__('Endereço Completo')" />
                            <x-text-input id="address" name="address" type="text" 
                                          class="mt-1 block w-full" :value="old('address', $profile->address)" required />
                            <x-input-error class="mt-2" :messages="$errors->get('address')" />
                        </div>

                        <div>
                            <x-input-label for="city" :value="__('Cidade')" />
                            <x-text-input id="city" name="city" type="text" 
                                          class="mt-1 block w-full" :value="old('city', $profile->city)" required />
                            <x-input-error class="mt-2" :messages="$errors->get('city')" />
                        </div>

                        <div>
                            <x-input-label for="state" :value="__('Estado')" />
                            <select id="state" name="state" 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                <option value="">Selecione o Estado</option>
                                <option value="AC" {{ old('state', $profile->state) == 'AC' ? 'selected' : '' }}>AC</option>
                                <option value="AL" {{ old('state', $profile->state) == 'AL' ? 'selected' : '' }}>AL</option>
                                <option value="AP" {{ old('state', $profile->state) == 'AP' ? 'selected' : '' }}>AP</option>
                                <option value="AM" {{ old('state', $profile->state) == 'AM' ? 'selected' : '' }}>AM</option>
                                <option value="BA" {{ old('state', $profile->state) == 'BA' ? 'selected' : '' }}>BA</option>
                                <option value="CE" {{ old('state', $profile->state) == 'CE' ? 'selected' : '' }}>CE</option>
                                <option value="DF" {{ old('state', $profile->state) == 'DF' ? 'selected' : '' }}>DF</option>
                                <option value="ES" {{ old('state', $profile->state) == 'ES' ? 'selected' : '' }}>ES</option>
                                <option value="GO" {{ old('state', $profile->state) == 'GO' ? 'selected' : '' }}>GO</option>
                                <option value="MA" {{ old('state', $profile->state) == 'MA' ? 'selected' : '' }}>MA</option>
                                <option value="MT" {{ old('state', $profile->state) == 'MT' ? 'selected' : '' }}>MT</option>
                                <option value="MS" {{ old('state', $profile->state) == 'MS' ? 'selected' : '' }}>MS</option>
                                <option value="MG" {{ old('state', $profile->state) == 'MG' ? 'selected' : '' }}>MG</option>
                                <option value="PA" {{ old('state', $profile->state) == 'PA' ? 'selected' : '' }}>PA</option>
                                <option value="PB" {{ old('state', $profile->state) == 'PB' ? 'selected' : '' }}>PB</option>
                                <option value="PR" {{ old('state', $profile->state) == 'PR' ? 'selected' : '' }}>PR</option>
                                <option value="PE" {{ old('state', $profile->state) == 'PE' ? 'selected' : '' }}>PE</option>
                                <option value="PI" {{ old('state', $profile->state) == 'PI' ? 'selected' : '' }}>PI</option>
                                <option value="RJ" {{ old('state', $profile->state) == 'RJ' ? 'selected' : '' }}>RJ</option>
                                <option value="RN" {{ old('state', $profile->state) == 'RN' ? 'selected' : '' }}>RN</option>
                                <option value="RS" {{ old('state', $profile->state) == 'RS' ? 'selected' : '' }}>RS</option>
                                <option value="RO" {{ old('state', $profile->state) == 'RO' ? 'selected' : '' }}>RO</option>
                                <option value="RR" {{ old('state', $profile->state) == 'RR' ? 'selected' : '' }}>RR</option>
                                <option value="SC" {{ old('state', $profile->state) == 'SC' ? 'selected' : '' }}>SC</option>
                                <option value="SP" {{ old('state', $profile->state) == 'SP' ? 'selected' : '' }}>SP</option>
                                <option value="SE" {{ old('state', $profile->state) == 'SE' ? 'selected' : '' }}>SE</option>
                                <option value="TO" {{ old('state', $profile->state) == 'TO' ? 'selected' : '' }}>TO</option>
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('state')" />
                        </div>

                        <div class="md:col-span-2">
                            <x-input-label for="postal_code" :value="__('CEP')" />
                            <x-text-input id="postal_code" name="postal_code" type="text" 
                                          class="mt-1 block w-full" :value="old('postal_code', $profile->postal_code)" 
                                          placeholder="00000-000" required />
                            <x-input-error class="mt-2" :messages="$errors->get('postal_code')" />
                        </div>
                    </div>
                </div>

                <!-- Bank Information -->
                <div class="mb-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Dados Bancários</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="bank_name" :value="__('Banco')" />
                            <x-text-input id="bank_name" name="bank_name" type="text" 
                                          class="mt-1 block w-full" :value="old('bank_name', $profile->bank_name)" 
                                          placeholder="Ex: Banco do Brasil" required />
                            <x-input-error class="mt-2" :messages="$errors->get('bank_name')" />
                        </div>

                        <div>
                            <x-input-label for="bank_account" :value="__('Conta (Agência-Conta)')" />
                            <x-text-input id="bank_account" name="bank_account" type="text" 
                                          class="mt-1 block w-full" :value="old('bank_account', $profile->bank_account)" 
                                          placeholder="Ex: 1234-123456-7" required />
                            <x-input-error class="mt-2" :messages="$errors->get('bank_account')" />
                        </div>
                    </div>
                </div>

                <!-- Document Uploads -->
                <div class="mb-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Documentos</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="address_proof" :value="__('Comprovante de Endereço')" />
                            <input type="file" id="address_proof" name="address_proof" 
                                   class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" 
                                   accept=".pdf,.jpg,.jpeg,.png" required />
                            <p class="mt-1 text-xs text-gray-500">PDF, JPG ou PNG (máx. 2MB)</p>
                            <x-input-error class="mt-2" :messages="$errors->get('address_proof')" />
                        </div>

                        <div>
                            <x-input-label for="identity_proof" x-text="documentType === 'cpf' ? 'RG ou CNH' : 'Contrato Social ou CNPJ'" />
                            <input type="file" id="identity_proof" name="identity_proof" 
                                   class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" 
                                   accept=".pdf,.jpg,.jpeg,.png" required />
                            <p class="mt-1 text-xs text-gray-500">PDF, JPG ou PNG (máx. 2MB)</p>
                            <x-input-error class="mt-2" :messages="$errors->get('identity_proof')" />
                        </div>
                    </div>
                </div>

                <!-- Submit -->
                <div class="flex justify-end">
                    <x-primary-button>
                        {{ __('Enviar Documentos') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.marketplace>