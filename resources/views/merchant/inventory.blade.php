@extends('global.app')
@section('content')
    <section class="hero background-off-white border-silver border-width-1 border-bottom-solid">
        <div class="row">
            <div class="content align-items-center">
                <header class="hero__header col-span-5 col-start-2 l-col-start-1 m-col-span-7 sm-col-span-4 sm-col-start-2 s-col-span-6 s-col-start-1 margin-bottom-15">
                    <h1 class="header-one color-carnation">{{ $merchant->name }}</h1>
                </header>
                <x-merchant-opening-hours :opening-hours="$merchant->openingHours" />
                <div class="hero__merchant-delivery wrap col-span-5 col-start-2 l-col-start-1 m-col-span-7 sm-col-span-4 sm-col-start-2 s-col-span-6 s-col-start-1">
                    @if ($merchant->doesMerchantAllowDeliveryAndCollection())
                    <p class="margin-right-40"><span class="icon icon--pinpoint">@svg('pinpoint', 'fill-casablanca')</span>We deliver within a {{ $merchant->delivery_radius }} mile radius</p>
                    <p class="margin-right-40"><span class="icon icon--delivery">@svg('delivery', 'fill-casablanca')</span>£{{ $merchant->getFormattedUKPriceAttribute($merchant->delivery_cost) }}</p>
                    <p><span class="icon icon--collection">@svg('collection', 'fill-casablanca')</span>Collection available</p>
                    @elseif ($merchant->deliveryOnly())

                    @endif
                </div>
                <div class="hero__merchant-address col-span-5 col-start-2 l-col-start-1 m-col-span-7 sm-col-span-4 sm-col-start-2 s-col-span-6 s-col-start-1">
                    <p class="margin-bottom-5">{{ $merchant->address }}</p>
                    <p><a href="tel:{{ $merchant->contact_number }}" class="color-cloud-burst text-underline">{{ $merchant->contact_number }}</a></p>
                </div>
                @if ($merchant->description  !== null)
                    <div class="hero__copy col-span-4 col-start-2 l-col-span-5 l-col-start-1 m-col-span-6 sm-col-span-4 sm-col-start-2 s-col-span-6 s-col-start-1 margin-top-50">
                        <p>{{ $merchant->description }}</p>
                    </div>
                @endif
                @if ($merchant->logo !== null)
                    <div class="hero__image hero__image--border hero__image--square inline-flex col-span-3 col-start-9 l-col-span-4 l-col-start-8 m-col-span-5 m-col-start-8 sm-col-span-2 sm-col-start-3 s-col-start-1 row-span-5 row-start-1 sm-row-start-5 sm-margin-top-50 s-row-start-5">
                        <img src="{{ $merchant->getTemporaryLogoLink() }}" alt="{{ $merchant->name }}">
                    </div>
                @endif
            </div>
        </div>
    </section>
    <section class="menu flex flex-col">
        <header class="menu__categories">
            <div class="row flex align-content-stretch">
                <div class="content align-content-stretch">
                    @if (!$merchant->visibleCategories->isEmpty() && $merchant->visibleCategories->count() > 1)
                        <nav class="menu__nav inline-flex align-content-stretch col-span-6 col-start-2 l-col-start-1">
                            <ul class="menu__cat-list">
                                @foreach ($merchant->visibleCategories as $category)
                                    @if (!$category->inventoriesAvailable->isEmpty())
                                        <li class="menu__cat-item">
                                            <a href="#{{$category->title}}" class="menu__cat-link">{{ $category->title }}</a>
                                        </li>
                                    @endif
                                @endforeach
                            </ul>
                        </nav>
                    @endif
                    <basket></basket>
                </div>
            </div>
        </header>
        <div class="menu__listing padding-bottom-140 sm-padding-bottom-60">
            <div class="row">
                <div class="content align-items-start">
                    @if (!$merchant->visibleCategories->isEmpty())
                        <div class="inventory col-span-6 col-start-2 l-col-start-1 m-col-span-7 sm-col-span-6 padding-top-100">
                            @foreach ($merchant->visibleCategories as $category)
                                @if (!$category->inventoriesAvailable->isEmpty())
                                    <div class="inventory__categories inline-flex flex-col width-full" id="{{$category->title}}">
                                        <header class="inventory__category-name">
                                            <h2 class="header-three inventory__category__title">{{$category->title}}</h2>
                                        </header>
                                        {{--@TODO add if sub category--}}
                                        <div class="inventory__sub-categories width-full">
                                            <header class="inventory__category-name">
                                                <h3 class="header-three inventory__category__title">Sub category</h3>
                                            </header>
                                            <div class="inventory__group width-full">
                                                @foreach ($category->inventoriesAvailable as $inventory)
                                                    <inventory-item
                                                        :product="{{ json_encode($inventory) }}"
                                                    ></inventory-item>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @endif
                    <mini-cart
                        service-type="{{ Request::segment(2) === 'take-away' ? 'take-away' : 'table-order' }}"
                        :merchant="{{ json_encode($merchant) }}"
                    ></mini-cart>
                </div>
            </div>
            <div class="button button-solid--carnation hidden menu__view" id="menu__view">
                <span class="button__icon button__icon--left">@svg('aweder-logo-small')</span>
                <span class="button__content">View order</span>
                <span class="separator separator--small"></span>
                <span class="button__price">£12.00</span>
            </div>
        </div>
    </section>
    <item-options
        :merchant="{{ json_encode($merchant) }}"
    ></item-options>

    <order-type
        v-if="{{ Request::segment(2) === 'take-away' ? 'true' : 'false' }}"
        :merchant="{{ json_encode($merchant) }}"
    ></order-type>

    <select-table
        v-if="{{ Request::segment(2) === 'table-order' ? 'true' : 'false' }}"
        :merchant="{{ json_encode($merchant) }}"
    ></select-table>
@endsection
