<?php

namespace App\Helpers;

use App\Models\Product;
use Illuminate\Support\Facades\Cookie;

class CartManagement
{
    static public function addItemsToCart($product_id)
    {
        // Get existing cart items from the cookie
        $cart_items = self::getCartItemsFromCookie();

        // Check if the item already exists in the cart
        $existing_item_key = array_search($product_id, array_column($cart_items, 'product_id'));

        if ($existing_item_key !== false) {
            $cart_items[$existing_item_key]['quantity']++;
        } else {
            $product = Product::where('id', $product_id)->first(['id', 'name', 'price', 'images']);

            if ($product) {
                $cart_items[] = [
                    'product_id' => $product_id,
                    'name' => $product->name,
                    'image' => $product->images[0],
                    'quantity' => 1,
                    'unit_amount' => $product->price,
                    'total_amount' => $product->price,
                ];
            }
        }

        self::updateTotalAmounts($cart_items);
        self::addCartItemsToCookie($cart_items);

        return count($cart_items);
    }

    static private function updateTotalAmounts(&$cart_items)
    {
        foreach ($cart_items as &$item) {
            $item['total_amount'] = $item['quantity'] * $item['unit_amount'];
        }
    }


    static public function removeItemFromCart($product_id)
    {
        $cart_items = self::getCartItemsFromCookie();

        // Filter out the item to be removed
        $cart_items = array_filter($cart_items, function ($item) use ($product_id) {
            return $item['product_id'] !== $product_id;
        });

        // Update the cookie with the modified cart items
        self::addCartItemsToCookie(array_values($cart_items)); // Reindex the array

        return $cart_items;
    }


    static public function incrementItemQuantity($product_id)
    {
        $cart_items = self::getCartItemsFromCookie();

        foreach ($cart_items as $key => $item) {
            if ($item['product_id'] == $product_id) {
                $cart_items[$key]['quantity'] += 1;
                break; 
            }
        }

        self::updateTotalAmounts($cart_items);
        self::addCartItemsToCookie($cart_items);
        return $cart_items;
    }

    static public function decrementItemQuantity($product_id)
    {
        $cart_items = self::getCartItemsFromCookie();

        foreach ($cart_items as $key => $item) {
            if ($item['product_id'] == $product_id) {
                if ($item['quantity'] > 1) {
                    $cart_items[$key]['quantity'] -= 1;
                } else {
                    unset($cart_items[$key]);
                }
                break; // Exit the loop after updating
            }
        }

        self::updateTotalAmounts($cart_items);
        self::addCartItemsToCookie(array_values($cart_items));

        return $cart_items;
    }

    static public function getGrandTotal()
    {
        $cart_items = self::getCartItemsFromCookie();
        $totalAmount = 0;

        foreach ($cart_items as $item) {
            $totalAmount += $item['unit_amount'] * $item['quantity'];
        }

        return $totalAmount;
    }

    /**
 * Get the details of the cart items from the cookie.
 *
 * @return array
 */
    static public function getCartDetails()
    {
        return self::getCartItemsFromCookie();
    }

    static public function addCartItemsToCookie($cart_items)
    {
        Cookie::queue('cart_items', json_encode($cart_items), 60 * 24 * 30);
    }

    static public function clearCartItemsFromCookie()
    {
        Cookie::queue(Cookie::forget('cart_items'));
    }

    static public function getCartItemsFromCookie()
    {
        return json_decode(Cookie::get('cart_items'), true) ?? [];
    }
}
