<?php
/**
 * Arquivo: app/Http/Controllers/Admin/CategoryController.php
 * Descrição: Controller para gerenciamento de categorias no painel administrativo
 * Laravel Version: 12.x
 * Criado em: 28/08/2025
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /**
     * Exibe a lista de categorias
     */
    public function index()
    {
        $categories = Category::with('parent')
            ->ordered()
            ->paginate(20);

        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Exibe o formulário para criar uma nova categoria
     */
    public function create()
    {
        $categories = Category::active()->mainCategories()->ordered()->get();
        
        return view('admin.categories.create', compact('categories'));
    }

    /**
     * Armazena uma nova categoria
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories',
            'description' => 'nullable|string|max:1000',
            'parent_id' => 'nullable|exists:categories,id',
            'icon' => 'nullable|string|max:100',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_active'] = $validated['is_active'] ?? true;
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        // Sistema de upload removido - imagem será null por enquanto
        // TODO: Implementar novo sistema de upload se necessário

        Category::create($validated);

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Categoria criada com sucesso!');
    }

    /**
     * Exibe o formulário para editar uma categoria
     */
    public function edit(Category $category)
    {
        $categories = Category::active()
            ->mainCategories()
            ->ordered()
            ->where('id', '!=', $category->id)
            ->get();
        
        return view('admin.categories.edit', compact('category', 'categories'));
    }

    /**
     * Atualiza uma categoria
     */
    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'description' => 'nullable|string|max:1000',
            'parent_id' => 'nullable|exists:categories,id',
            'icon' => 'nullable|string|max:100',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_active'] = $validated['is_active'] ?? true;

        // Sistema de upload removido - imagem não será alterada por enquanto
        // TODO: Implementar novo sistema de upload se necessário

        $category->update($validated);

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Categoria atualizada com sucesso!');
    }

    /**
     * Remove uma categoria
     */
    public function destroy(Category $category)
    {
        // Verifica se a categoria tem produtos
        if ($category->products()->exists()) {
            return back()->with('error', 'Não é possível excluir uma categoria que possui produtos.');
        }

        // Verifica se a categoria tem subcategorias
        if ($category->children()->exists()) {
            return back()->with('error', 'Não é possível excluir uma categoria que possui subcategorias.');
        }

        // Sistema de upload removido - imagem não será deletada por enquanto
        // TODO: Implementar novo sistema de upload se necessário

        $category->delete();

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Categoria excluída com sucesso!');
    }

    /**
     * Altera o status ativo/inativo de uma categoria
     */
    public function toggleStatus(Category $category)
    {
        $category->update(['is_active' => !$category->is_active]);

        $status = $category->is_active ? 'ativada' : 'desativada';
        
        return back()->with('success', "Categoria {$status} com sucesso!");
    }
}
