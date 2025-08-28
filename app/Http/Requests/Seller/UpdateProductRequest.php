<?php
/**
 * Arquivo: app/Http/Requests/Seller/UpdateProductRequest.php
 * Descrição: Form Request para atualização de produtos
 * Laravel Version: 12.x
 * Criado em: 28/08/2025
 */

namespace App\Http\Requests\Seller;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() 
               && auth()->user()->role === 'seller'
               && $this->route('product')->seller_id === auth()->user()->sellerProfile->id;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $product = $this->route('product');
        
        return [
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'short_description' => ['nullable', 'string', 'max:500'],
            'price' => ['required', 'numeric', 'min:0.01', 'max:999999.99'],
            'compare_at_price' => ['nullable', 'numeric', 'min:0.01', 'max:999999.99', 'gt:price'],
            'cost' => ['nullable', 'numeric', 'min:0', 'max:999999.99'],
            'sku' => ['nullable', 'string', 'max:100', Rule::unique('products', 'sku')->ignore($product->id)],
            'barcode' => ['nullable', 'string', 'max:100'],
            'stock_quantity' => ['required', 'integer', 'min:0'],
            'stock_status' => ['required', 'in:in_stock,out_of_stock,backorder'],
            'weight' => ['nullable', 'numeric', 'min:0', 'max:99999.999'],
            'length' => ['nullable', 'numeric', 'min:0', 'max:9999.99'],
            'width' => ['nullable', 'numeric', 'min:0', 'max:9999.99'],
            'height' => ['nullable', 'numeric', 'min:0', 'max:9999.99'],
            'status' => ['required', 'in:draft,active,inactive'],
            'featured' => ['boolean'],
            'digital' => ['boolean'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string'],
            'meta_keywords' => ['nullable', 'string'],
            'images' => ['nullable', 'array', 'max:5'],
            'images.*' => ['image', 'mimes:jpeg,jpg,png,webp', 'max:2048'], // 2MB max per image
        ];
    }

    /**
     * Get custom messages for validation errors.
     */
    public function messages(): array
    {
        return [
            'category_id.required' => 'Selecione uma categoria para o produto.',
            'category_id.exists' => 'A categoria selecionada não existe.',
            'name.required' => 'O nome do produto é obrigatório.',
            'name.max' => 'O nome não pode ter mais de 255 caracteres.',
            'description.string' => 'A descrição deve ser um texto válido.',
            'short_description.max' => 'A descrição curta não pode ter mais de 500 caracteres.',
            'price.required' => 'O preço é obrigatório.',
            'price.numeric' => 'O preço deve ser um número.',
            'price.min' => 'O preço mínimo é R$ 0,01.',
            'price.max' => 'O preço máximo é R$ 999.999,99.',
            'compare_at_price.numeric' => 'O preço comparativo deve ser um número.',
            'compare_at_price.gt' => 'O preço comparativo deve ser maior que o preço de venda.',
            'cost.numeric' => 'O custo deve ser um número.',
            'sku.unique' => 'Este SKU já está sendo usado por outro produto.',
            'sku.max' => 'O SKU não pode ter mais de 100 caracteres.',
            'stock_quantity.required' => 'A quantidade em estoque é obrigatória.',
            'stock_quantity.integer' => 'A quantidade deve ser um número inteiro.',
            'stock_quantity.min' => 'A quantidade não pode ser negativa.',
            'stock_status.required' => 'O status do estoque é obrigatório.',
            'stock_status.in' => 'Status de estoque inválido.',
            'weight.numeric' => 'O peso deve ser um número.',
            'length.numeric' => 'O comprimento deve ser um número.',
            'width.numeric' => 'A largura deve ser um número.',
            'height.numeric' => 'A altura deve ser um número.',
            'status.required' => 'O status do produto é obrigatório.',
            'status.in' => 'Status do produto inválido.',
            'images.max' => 'Você pode enviar no máximo 5 imagens.',
            'images.*.image' => 'Todos os arquivos devem ser imagens.',
            'images.*.mimes' => 'As imagens devem ser nos formatos: JPEG, JPG, PNG ou WebP.',
            'images.*.max' => 'Cada imagem deve ter no máximo 2MB.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Convert checkbox values to boolean
        $this->merge([
            'featured' => $this->boolean('featured'),
            'digital' => $this->boolean('digital'),
        ]);
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'category_id' => 'categoria',
            'name' => 'nome',
            'description' => 'descrição',
            'short_description' => 'descrição curta',
            'price' => 'preço',
            'compare_at_price' => 'preço comparativo',
            'cost' => 'custo',
            'sku' => 'SKU',
            'barcode' => 'código de barras',
            'stock_quantity' => 'quantidade em estoque',
            'stock_status' => 'status do estoque',
            'weight' => 'peso',
            'length' => 'comprimento',
            'width' => 'largura',
            'height' => 'altura',
            'status' => 'status',
            'featured' => 'produto destaque',
            'digital' => 'produto digital',
            'meta_title' => 'título SEO',
            'meta_description' => 'descrição SEO',
            'meta_keywords' => 'palavras-chave SEO',
            'images' => 'imagens',
        ];
    }
}