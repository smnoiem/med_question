<div class="row" data-test="test">
    <div class="col-md-4">
        <div class="form-group">
            <label for="">Option</label>
            <select id="select2-option-{{$currentIndex}}" data-index="{{$currentIndex}}" name="product_variant[{{$currentIndex}}][option]" class="form-control custom-select select2 select2-option">
                @foreach($variants as $variantTemp)
                    <option value="{{$variantTemp->id}}" data-title="{{$variantTemp->title}}" {{ $variant->id == $variantTemp->id ? "selected" : ""}}>{{$variantTemp->title}} </option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="col-md-8">
        <div class="form-group">
            <label class="d-flex justify-content-between">
                <span>Value</span>
                <a href="#" class="remove-btn" data-index="{{$currentIndex}}" onclick="removeVariant(event, this);">Remove</a>
            </label>
            <select id="select2-value-{{$currentIndex}}" data-index="{{$currentIndex}}" name="product_variant[{{$currentIndex}}][value][]" class="select2 select2-value form-control custom-select" multiple="multiple">
                @foreach($product->productVariants()->where('variant_id', $variant->id)->get() as $productVariant)
                    <option value="{{$productVariant->variant}}" data-title="{{$productVariant->variant}}" selected>{{$productVariant->variant}} </option>
                @endforeach
            </select>
        </div>
    </div>
</div>