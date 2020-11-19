@extends('global.admin')
@section('content')
    <header class="dashboard-header dashboard-header--copy col-span-9 m-col-span-12 sm-col-span-6">
        <h1 class="dashboard-title header-one">Inventory</h1>
        @if(isset($signUpRoute) && $signUpRoute === true)
            <p>Simply add your order items and group them by categories, these can be changed in your dashboard at any time.</p>
        @else
            <p>Simply add your order items and group them by categories.</p>
        @endif
    </header>
    <section class="dashboard-content width-full col-span-9 m-col-span-12 sm-col-span-6 inline-grid grid-cols-9 m-grid-col-12 sm-grid-cols-6">
        <div class="inventory inventory--editable col-span-6 m-col-span-7 sm-col-span-6">
            <div class="inventory__categories inventory__categories--editable width-full">
                <add-category merchant={{ $merchant->url_slug }}></add-category>
                <div class="inventory__group inline-flex flex-col width-full">
                    <add-item></add-item>
                </div>
            </div>
            @if (!$fullInventory->isEmpty())
                @foreach ($fullInventory as $category)
                    <categories
                        category-name="{{ $category->title }}"
                        :sub-category="1"
                        :inventory-items="{{ json_encode($category->inventories->toArray()) }}">
                    </categories>
                @endforeach
            @endif
        </div>
        {{--@foreach ($fullInventory as $category)--}}
            {{--@if ($category->title !== null && !empty($category->title))--}}
            {{--<div class="inventory__item col-span-9">--}}
                {{--<header class="inventory__header">--}}
                    {{--<h3 class="header header--five">{{ $category->title }}</h3>--}}
                {{--</header>--}}
                {{--<div class="inventory__listing">--}}
                    {{--<table>--}}
                        {{--<thead>--}}
                            {{--<tr>--}}
                                {{--<th>Item Image</th>--}}
                                {{--<th>Item Title</th>--}}
                                {{--<th>Item Description</th>--}}
                                {{--<th>Price (&pound;)</th>--}}
                                {{--<th class="actions">Actions</th>--}}
                            {{--</tr>--}}
                        {{--</thead>--}}
                        {{--<tbody>--}}
                            {{--<form--}}
                                {{--id="inventoryForm_{{$category->id}}"--}}
                                {{--name="inventoryForm"--}}
                                {{--autocomplete="off"--}}
                                {{--action="{{ route('admin.inventory.post') }}"--}}
                                {{--method="POST">--}}
                                {{--@csrf--}}
                                {{--<input type="hidden" name="category-id" value="{{ $category->id }}">--}}
                                {{--<tr>--}}
                                    {{--<td>--}}
                                        {{--<div class="choose_file">--}}
                                            {{--<button type="button" class="button">--}}
                                                        {{--<span class="field--upload icon">--}}
                                                            {{--@svg('upload')--}}
                                                        {{--</span>--}}
                                                {{--<input type="file" name="image" id="image" accept="image/*"/>--}}
                                            {{--</button>--}}
                                        {{--</div>--}}
                                    {{--</td>--}}
                                    {{--<td>--}}
                                        {{--<div class="field @error('title') input-error @enderror">--}}
                                            {{--<input type="text" name="title" id="title" placeholder="Item title" />--}}
                                            {{--@error('title')--}}
                                            {{--<p class="form__validation-error">{{ $message }}</p>--}}
                                            {{--@enderror--}}
                                        {{--</div>--}}
                                    {{--</td>--}}
                                    {{--<td>--}}
                                        {{--<div class="field @error('description') input-error @enderror">--}}
                                            {{--<textarea name="description" id="description" placeholder="Item description" rows="1"></textarea>--}}
                                            {{--@error('description')--}}
                                            {{--<p class="form__validation-error">{{ $message }}</p>--}}
                                            {{--@enderror--}}
                                        {{--</div>--}}
                                    {{--</td>--}}
                                    {{--<td>--}}
                                        {{--<div class="field @error('amount') input-error @enderror">--}}
                                            {{--<input type="text" name="amount" id="amount" placeholder="Item price (&pound;)"/>--}}
                                            {{--@error('amount')--}}
                                            {{--<p class="form__validation-error">{{ $message }}</p>--}}
                                            {{--@enderror--}}
                                        {{--</div>--}}
                                    {{--</td>--}}
                                    {{--<td class="actions">--}}
                                        {{--<div class="actions__wrapper">--}}
                                            {{--<button type="submit" class="button button--icon button--add" title="Add item">--}}
                                                {{--<span class="icon icon--add">@svg('add')</span>--}}
                                            {{--</button>--}}
                                        {{--</div>--}}
                                    {{--</td>--}}
                                {{--</tr>--}}
                            {{--</form>--}}
                            {{--@foreach ($category->inventories as $item)--}}
                                {{--<form--}}
                                    {{--id="updateInventoryForm_{{$item->id}}"--}}
                                    {{--name="updateInventoryForm"--}}
                                    {{--autocomplete="off"--}}
                                    {{--action="{{ route('admin.inventory.update', $item->id) }}"--}}
                                    {{--method="POST"--}}
                                    {{--enctype="multipart/form-data"--}}
                                    {{-->--}}
                                    {{--@csrf--}}
                                    {{--@method('PUT')--}}
                                    {{--<input type="hidden" name="category-id" value="{{ $category->id }}">--}}
                                    {{--<tr class="inventory__current">--}}
                                        {{--<td>--}}
                                            {{--@if(!$item->image)--}}
                                                {{--<div class="choose_file">--}}
                                                    {{--<button type="button" class="button">--}}
                                                            {{--<span class="field--upload icon">--}}
                                                                {{--@svg('upload')--}}
                                                            {{--</span>--}}
                                                        {{--<input type="file" name="image" id="image" accept="image/*"/>--}}
                                                    {{--</button>--}}
                                                {{--</div>--}}
                                            {{--@else--}}
                                                {{--<img src="{{ $item->getTemporaryInventoryImageLink() }}"--}}
                                            {{--@endif--}}
                                        {{--</td>--}}
                                        {{--<td class="inventory__title">--}}
                                            {{--<input type="text" name="title" id="title" value="{{ $item->title }}" />--}}
                                            {{--@error('title')--}}
                                            {{--<p class="form__validation-error">{{ $message }}</p>--}}
                                            {{--@enderror--}}
                                        {{--</td>--}}
                                        {{--<td class="inventory__desc">--}}
                                            {{--<input type="text" name="description" id="description" value="{{ $item->description }}" />--}}
                                            {{--@error('description')--}}
                                            {{--<p class="form__validation-error">{{ $message }}</p>--}}
                                            {{--@enderror--}}
                                        {{--</td>--}}
                                        {{--<td class="inventory__price">--}}
                                            {{--£<input type="text" name="price" id="price" value="{{ $item->getFormattedUKPriceAttribute($item->price) }}" />--}}
                                            {{--@error('price')--}}
                                            {{--<p class="form__validation-error">{{ $message }}</p>--}}
                                            {{--@enderror--}}
                                            {{--</td>--}}
                                        {{--<td class="actions">--}}
                                            {{--<div class="actions__wrapper">--}}
                                                {{--<a href="{{ route('admin.inventory.status', ['id' => $item->id]) }}" class="button button--icon button--visibility" title="Show or hide item from customers’">--}}
                                                    {{--@if ($item->available)--}}
                                                    {{--<span class="icon icon--visibility">--}}
                                                        {{--@svg('visible')--}}
                                                    {{--</span>--}}
                                                    {{--@else--}}
                                                    {{--<span class="icon icon--visibility icon--visibility-hidden">--}}
                                                        {{--@svg('hide-visibility')--}}
                                                    {{--</span>--}}
                                                    {{--@endif--}}
                                                {{--</a>--}}
                                                {{--<a href="{{ route('admin.inventory.delete', ['id' => $item->id]) }}" class="button button--icon button--outline button--remove" title="Remove item">--}}
                                                   {{--<span class="icon icon--remove">--}}
                                                       {{--@svg('remove')--}}
                                                   {{--</span>--}}
                                                {{--</a>--}}
                                                {{--<button type="submit" class="button button--icon button--add" title="Save item">--}}
                                                    {{--<span class="icon icon--add">@svg('tick')</span>--}}
                                                {{--</button>--}}
                                            {{--</div>--}}
                                        {{--</td>--}}
                                    {{--</tr>--}}
                                {{--</form>--}}
                            {{--@endforeach--}}
                        {{--</tbody>--}}
                    {{--</table>--}}
                {{--</div>--}}
            {{--</div>--}}
            {{--@endif--}}
        {{--@endforeach--}}
        {{--<div class="inventory__process col col--lg-12-10 col--sm-6-6">--}}
            {{--<a href="{{ route('admin.dashboard') }}" class="button button--icon-right button--filled button--filled-carnation button--end">--}}
                {{--<span class="button__content">Continue to Dashboard</span>--}}
                {{--<span class="icon icon-right">@svg('arrow-right')</span>--}}
            {{--</a>--}}
        {{--</div>--}}
    </section>


@endsection
