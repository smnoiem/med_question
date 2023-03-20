<?php

namespace App\Services;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\ProductVariantPrice;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class ProductService
{
    public $fileDirectory = 'product_photos';

    public function storeMediaFile($file): string
    {
        return Storage::putFile($this->fileDirectory, $file);
    }

    public function storeMediaPaths($mediaPaths, Product $product)
    {
        $this->removeUnusedMedias($mediaPaths, $product);

        foreach($mediaPaths as $mediaPath)
        {
            $productImage = new ProductImage;
            $productImage->product_id = $product->id;
            $productImage->file_path = $mediaPath;
            $productImage->save();
        }
    }

    public function removeUnusedMedias($mediaPaths, Product $product)
    {
        foreach($product->images as $image)
        {
            if(!in_array($image->file_path, $mediaPaths)){
                Storage::delete('product_photos'.$image->file_path);
                $image->delete();
            }
        }
    }

    public function addVariants($productVariantInputs, Product $product): array
    {
        $productVariantsMap = [];

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
        return $productVariantsMap;
    }

    public function updateVariants($productVariantInputs, Product $product): array
    {
        $productVariantsMap = [];

        foreach($productVariantInputs as $productVariantInput)
        {
            foreach($productVariantInput['value'] ?? [] as $value)
            {
                $productVariant = ProductVariant::updateOrCreate([
                    'variant' => $value,
                    'product_id' => $product->id,
                    'variant_id' => $productVariantInput['option'],
                ]);

                $productVariantsMap[$value] = $productVariant->id;
            }
        }

        // $this->removeUnusedVariants($productVariantsMap, $product);

        return $productVariantsMap;
    }

    public function removeUnusedVariants($productVariantsMap, Product $product): void
    {
        return;
    }

    public function addVariantPrices($variantPriceAllData, Product $product, $productVariantsMap)
    {
        foreach($variantPriceAllData as $variantPriceData)
        {
            $variantCombinations = array_filter(explode('/', $variantPriceData['variant']));

            $productVariantPrice = ProductVariantPrice::create([
                'product_id' => $product->id,
                'price' => $variantPriceData['price'],
                'stock' => $variantPriceData['stock'],
            ]);

            $count = 0;

            foreach($variantCombinations as $combination)
            {
                if($count == 0) $productVariantPrice->product_variant_one = $productVariantsMap[$combination];
                if($count == 1) $productVariantPrice->product_variant_two = $productVariantsMap[$combination];
                if($count == 2) $productVariantPrice->product_variant_three = $productVariantsMap[$combination];
                $productVariantPrice->update();

                $count++;
                if($count > 2) break;
            }
        }
    }

    public function applyFilters($queries)
    {
        $productVariantPrices = ProductVariantPrice::with(['product', 'product.productVariants']);

        if(isset($queries['title'])) {
            $keywords = array_filter(explode(' ', $queries['title']));
            foreach($keywords as $keyword)
            {
                $productVariantPrices = $productVariantPrices->whereHas('product', function ($query) use ($keyword) {
                    $query->where('title', 'LIKE', '%' . $keyword . '%');
                });
            }
        }

        if(isset($queries['price_from'])) {
            $productVariantPrices = $productVariantPrices->where('price', '>=', $queries['price_from']);
        }

        if(isset($queries['price_to']) ) {
            $productVariantPrices = $productVariantPrices->where('price', '<=', $queries['price_to']);
        }

        if(isset($queries['date']) ) {
            $productVariantPrices = $productVariantPrices->whereDate('created_at', new Carbon($queries['date']));
        }

        if(isset($queries['variants']) ) {
            foreach($queries['variants'] as $variant)
            {
                $productVariantPrices = $productVariantPrices->whereHas('product.productVariants', function ($query) use ($variant) {
                    $query->whereRaw('LOWER(`variant`) LIKE ?', '%' . $variant . '%');
                });
            }
        }

        // dd($productVariantPrices->toSql());

        return $productVariantPrices;
    }

    public function removeDuplicateVariants($variants)
    {
        $newVariants = [];
        foreach($variants as $variant)
        {
            if(!array_key_exists($variant->title, $newVariants)){
                $newVariants[$variant->title] = [];
            }

            foreach($variant->productVariants as $productVariant){
                $variantName = trim(strtolower($productVariant->variant));
                if(!in_array($variantName, $newVariants[$variant->title])) {
                    $newVariants[$variant->title][] = $variantName;
                }
            }
        }

        return $newVariants;
    }
}