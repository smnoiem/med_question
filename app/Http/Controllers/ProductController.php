<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\ProductVariantPrice;
use App\Models\Variant;
use App\Services\ProductService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $productVariantPrices = $this->productService->applyFilters($request->query());

        $productVariantPrices = $productVariantPrices->paginate(10);
        
        $variants = $this->productService->removeDuplicateVariants(Variant::get());
        
        return view('products.index', compact('productVariantPrices', 'variants'));
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

        $this->productService->storeMediaPaths($mediaPaths, $product);

        $productVariantInputs = $request->input('product_variant', []);

        $productVariantsMap = [];
        
        $productVariantsMap = $this->productService->addVariants($productVariantInputs, $product);

        $productVariantPriceAllData = $request->input('product_preview');

        $this->productService->addVariantPrices($productVariantPriceAllData, $product, $productVariantsMap);

        return redirect(route('product.index', ['title' => $product->title]));
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
        return view('products.edit', compact('product', 'variants'));
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
        $product->update([
            'title' => $request->input('product_name'),
            'sku' => $request->input('product_sku'),
            'description' => $request->input('product_description'),
        ]);

        $mediaPaths = $request->input('product_medias', []);

        $this->productService->storeMediaPaths($mediaPaths, $product);

        $productVariantInputs = $request->input('product_variant', []);

        $this->productService->updateVariants($productVariantInputs, $product);

        return redirect(route('product.index', ['title' => $product->title]));
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
        $file = $request->file('file');

        $path = $this->productService->storeMediaFile($file);

        return $path;
    }

    public function variationPriceHasProduct(Request $request)
    {
        $variantName = $request->input('variant');

        $exists = $this->productService->variationPriceHasProduct($variantName);

        if($exists) return response()->json(['product_exists' => true]);
        else return response()->json(['product_exists' => false]);
    }
}
