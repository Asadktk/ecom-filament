<?php

namespace App\Livewire;

use App\Helpers\CartManagement;
use App\Models\Product;
use Livewire\Component;

class CartPage extends Component
{
   

    public function render()
    {
        return view('livewire.cart-page');
    }
}
