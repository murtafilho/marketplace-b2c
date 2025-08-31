<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Category;

class ListCategories extends Command
{
    protected $signature = 'categories:list';
    protected $description = 'List all categories and their hierarchy';

    public function handle()
    {
        $this->info('Categories Structure:');
        $this->line('');

        $categories = Category::with('parent', 'children')->orderBy('parent_id')->orderBy('sort_order')->orderBy('name')->get();
        
        if ($categories->isEmpty()) {
            $this->warn('No categories found in database.');
            return;
        }

        $this->info('Total categories: ' . $categories->count());
        $this->line('');

        // Main categories first
        $mainCategories = $categories->whereNull('parent_id');
        
        foreach ($mainCategories as $category) {
            $this->info("📁 {$category->name} (ID: {$category->id})");
            $this->line("   Slug: {$category->slug}");
            $this->line("   Status: " . ($category->is_active ? 'Active' : 'Inactive'));
            
            // Show subcategories
            $subcategories = $categories->where('parent_id', $category->id);
            if ($subcategories->count() > 0) {
                foreach ($subcategories as $sub) {
                    $this->line("   └── 📂 {$sub->name} (ID: {$sub->id})");
                    $this->line("       Slug: {$sub->slug}");
                    $this->line("       Status: " . ($sub->is_active ? 'Active' : 'Inactive'));
                }
            }
            $this->line('');
        }
        
        // Orphaned subcategories (if any)
        $orphaned = $categories->whereNotNull('parent_id')->whereNotIn('parent_id', $mainCategories->pluck('id'));
        if ($orphaned->count() > 0) {
            $this->warn('Orphaned subcategories:');
            foreach ($orphaned as $category) {
                $this->line("❓ {$category->name} (ID: {$category->id}, Parent ID: {$category->parent_id})");
            }
        }
    }
}