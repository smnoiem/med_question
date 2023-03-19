
<tr>
    <td>{{ $productVariantPrice->product->id }}</td>
    <td>{{ $productVariantPrice->product->title }} <br> Created at : {{ $productVariantPrice->product->created_at }}</td>
    <td>{{ $productVariantPrice->product->description }}</td>
    <td>
        <dl class="row mb-0" style="height: 80px; overflow: hidden" id="variant-{{ $productVariantPrice->id }}">

            <dt class="col-sm-3 pb-0">
                {{ $productVariantPrice->productVariantOne ? $productVariantPrice->productVariantOne->variant : "" }}
                {{ $productVariantPrice->productVariantTwo ? "/" . $productVariantPrice->productVariantTwo->variant : ""}}
                {{ $productVariantPrice->productVariantThree ? "/" . $productVariantPrice->productVariantThree->variant : ""}}
            </dt>
            <dd class="col-sm-9">
                <dl class="row mb-0">
                    <dt class="col-sm-4 pb-0">Price : {{ number_format( $productVariantPrice->price,2) }}</dt>
                    <dd class="col-sm-8 pb-0">InStock : {{ number_format($productVariantPrice->stock) }}</dd>
                </dl>
            </dd>
        </dl>
        <button onclick="$('#variant-{{ $productVariantPrice->id }}').toggleClass('h-auto')" class="btn btn-sm btn-link">Show more</button>
    </td>
    <td>
        <div class="btn-group btn-group-sm">
            <a href="{{ route('product.edit', $productVariantPrice->id) }}" class="btn btn-success">Edit</a>
        </div>
    </td>
</tr>