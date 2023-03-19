<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\ProductVariantPrice;
use App\Models\Variant;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function index()
    {
        return view('products.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function create()
    {
        $variants = Variant::all();
        return view('products.create', compact('variants'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $product = Product::create([
            'title' => $request->input('product_name'),
            'sku' => $request->input('product_sku'),
            'description' => $request->input('product_description'),
        ]);

        $mediaPaths = $request->input('product_medias', []);

        foreach($mediaPaths as $mediaPath)
        {
            $productImage = new ProductImage;
            $productImage->product_id = $product->id;
            $productImage->file_path = $mediaPath;
            $productImage->save();
        }

        $productVariantsMap = [];

        $productVariantInputs = $request->input('product_variant', []);

        foreach($productVariantInputs as $productVariantInput)
        {
            foreach($productVariantInput['value'] as $value)
            {
                $productVariant = ProductVariant::create([
                    'variant' => $value,
                    'product_id' => $product->id,
                    'variant_id' => $productVariantInput['option'],
                ]);

                $productVariantsMap[$value] = $productVariant->id;
            }
        }

        $productPreviews = $request->input('product_preview');

        foreach($productPreviews as $productPreview)
        {
            $combinations = array_filter(explode('/', $productPreview['variant']));

            $productVariantPrice = ProductVariantPrice::create([
                'product_id' => $product->id,
                'price' => $productPreview['price'],
                'stock' => $productPreview['stock'],
            ]);

            $count = 0;

            foreach($combinations as $combination)
            {
                if($count == 0) $productVariantPrice->product_variant_one = $productVariantsMap[$combination];
                if($count == 1) $productVariantPrice->product_variant_two = $productVariantsMap[$combination];
                if($count == 2) $productVariantPrice->product_variant_three = $productVariantsMap[$combination];
                $productVariantPrice->update();

                $count++;
                if($count > 2) break;
            }
        }

        return response()->redirectTo(route('product.index'));
    }


    /**
     * Display the specified resource.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function show($product)
    {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product)
    {
        $variants = Variant::all();
        return view('products.edit', compact('variants'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        //
    }

    public function storeMedia(Request $request)
    {
        $path = $request->file('file')->store('product_photos');

        return $path;
    }
}
