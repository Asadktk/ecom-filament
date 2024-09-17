<?php

namespace App\Livewire;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Products - Ecommerce')]
class ProductsPage extends Component
{
    use WithPagination;

    #[Url]
    public $selected_categories = [];

    #[Url]
    public $brands_selected = [];

    #[Url]
    public $featured;

    #[Url]
    public $on_sale;

    #[Url]
    public $price_range = 50000;

    public $sortBy = 'latest';

    public function render()
    {
        $productsQuery = Product::query()->where('is_active', 1);

        if (!empty($this->selected_categories)) {
            $productsQuery->whereIn('category_id', $this->selected_categories);
        }

        if (!empty($this->brands_selected)) {
            $productsQuery->whereIn('brand_id', $this->brands_selected);
        }

        if ($this->featured) {
            $productsQuery->where('is_featured', 1);
        }

        if ($this->on_sale) {
            $productsQuery->where('on_sale', 1);
        }

        if ($this->price_range) {
            $productsQuery->whereBetween('price', [0, $this->price_range]);
        }

        if ($this->sortBy == 'price') {
            $productsQuery->orderBy('price');
        } else {
            $productsQuery->orderBy('created_at', 'desc'); // Latest by default
        }

        $products = $productsQuery->paginate(6);

        return view('livewire.products-page', [
            'products' => $products,
            'brands' => Brand::where('is_active', 1)->get(['id', 'name', 'slug']),
            'categories' => Category::where('is_active', 1)->get(['id', 'name', 'slug']),

        ]);
    }
}
