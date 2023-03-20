@extends('layouts.app')

@section('content')

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Products</h1>
    </div>


    <div class="card">
        <form action="" method="get" class="card-header">
            <div class="form-row justify-content-between">
                <div class="col-md-2">
                    <input type="text" name="title" placeholder="Product Title" class="form-control" value="{{ app('request')->input('title') }}">
                </div>
                <div class="col-md-2">
                    <select name="variants[]" id="" class="form-control" multiple="multiple">

                        @foreach ($variants as $title => $variant)
                            
                            <optgroup label="{{ $title }}">

                                @foreach ($variant as $productVariant)
                                    
                                    <option value="{{$productVariant}}" {{ in_array($productVariant, app('request')->input('variants') ?? []) ? "selected":""}}>{{$productVariant}}</option>
                                    
                                @endforeach

                            </optgroup>

                        @endforeach
                        
                    </select>
                </div>

                <div class="col-md-3">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">Price Range</span>
                        </div>
                        <input type="text" name="price_from"  value="{{ app('request')->input('price_from') }}" aria-label="First name" placeholder="From" class="form-control">
                        <input type="text" name="price_to" value="{{ app('request')->input('price_to') }}" aria-label="Last name" placeholder="To" class="form-control">
                    </div>
                </div>
                <div class="col-md-2">
                    <input type="date" name="date"  value="{{ app('request')->input('date') }}" placeholder="Date" class="form-control">
                </div>
                <div class="col-md-1">
                    <button type="submit" class="btn btn-primary float-right"><i class="fa fa-search"></i></button>
                </div>
            </div>
        </form>

        <div class="card-body">
            <div class="table-response">
                <table class="table">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Title</th>
                        <th>Description</th>
                        <th>Variant</th>
                        <th width="150px">Action</th>
                    </tr>
                    </thead>

                    <tbody>

                    @foreach ($productVariantPrices as $productVariantPrice)
                        @include('products.sections.product-row', [$productVariantPrice])
                    @endforeach

                    </tbody>

                </table>
            </div>

        </div>

        <div class="card-footer">
            <div class="row justify-content-between">
                <div class="col-md-6">

                    @if($productVariantPrices->count())
                    <p>
                        Showing {{$productVariantPrices->firstItem()}} to {{$productVariantPrices->lastItem()}} out of {{$productVariantPrices->total()}}
                    </p>
                    @else
                    <p>
                        No product found!
                    </p>
                    @endif

                    {{ $productVariantPrices->withQueryString()->links() }}

                </div>
                <div class="col-md-2">

                </div>
            </div>
        </div>
    </div>

@endsection
