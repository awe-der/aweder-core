@extends('global.app')
@section('content')
    <section class="checkout">
        <div class="row">
            <div class="content">
                <header class="checkout__header col-span-4 col-start-5 l-col-span-6 l-col-start-4 sm-col-start-1">
                    <h1 class="header-two color-carnation">Order details</h1>
                </header>
                <div class="checkout__merchant col-span-4 col-start-5 l-col-span-6 l-col-start-4 sm-col-start-1">
                    <div class="checkout__merchant-image">
                        <img src="{{ $merchant->getTemporaryLogoLink() }}" alt="{{ $merchant->name }}">
                    </div>
                    <div class="checkout__merchant-details">
                        <h2 class="body-xlarge">{{ $merchant->name }}</h2>
                        <p class="checkout__merchant-address">{{ $merchant->address }}</p>
                        <p class="checkout__merchant-phone"><a href="tel:{{ $merchant->contact_number }}">{{ $merchant->contact_number }}</a></p>
                    </div>
                </div>
                <div class="checkout__confirmation col-span-4 col-start-5 l-col-span-6 l-col-start-4 sm-col-start-1 panel background-white shadow-order">
                    <div class="checkout__delivery">
                        <span class="icon icon--time flex">@svg('time', 'fill-cloud-burst')</span>
                        <p> @if ($order->is_delivery || $order->is_collection)
                                @if ($order->is_delivery)Delivery,
                                @else
                                    Collection,
                                @endif
                                today at {{ $order->getFormattedDeliveryTime($order->customer_requested_time) }}
                            @else
                                Table Number
                            @endif
                        </p>
                    </div>
                    <form class="checkout--form width-full"
                          action="{{ route('store.menu.order-details.post', ['merchant' => $merchant->url_slug, 'order' => $order->url_slug]) }}"
                          method="POST">
                        @csrf
                        <div class="field field--small">
                            <label class="label label--float label--small"
                               for="customer_name">Name<sup>*</sup>
                            </label>
                            <input type="text"
                               name="customer_name"
                               id="customer_name"
                               tabindex="1"
                               value="{{ old('customer_name') }}"
                               placeholder="Name" class="text-input text-input--small" />
                            @error('customer_name')
                            <p class="field__error">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="field field--small">
                            <label class="label label--float label--small"
                               for="customer_email">Email<sup>*</sup>
                            </label>
                            <input type="email"
                               name="customer_email"
                               id="customer_email"
                               tabindex="1"
                               value="{{ old('customer_email') }}"
                               placeholder="Email"
                               class="text-input text-input--small" />
                            @error('customer_email')
                            <p class="field__error">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="field field--small">
                            <label class="label label--float label--small"
                               for="customer_address">Address<sup>*</sup>
                            </label>
                            <textarea name="customer_address"
                                  id="customer_address"
                                  class="textarea-input textarea-input--small"
                                  rows="1"
                                  placeholder="Address">
                                {{ old('customer_address') }}
                            </textarea>
                            @error('customer_address')
                            <p class="field__error">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="field field--small">
                            <label class="label label--float label--small"
                               for="customer_note">Notes for restaurant<sup>*</sup>
                            </label>
                            <textarea name="customer_note"
                                  id="customer_note"
                                  class="textarea-input textarea-input--small"
                                  placeholder="Notes for restaurant">{{ old('customer_note') }}</textarea>
                            @error('customer_note')
                            <p class="field__error">{{ $message }}</p>
                            @enderror
                        </div>
                        @if ($merchant->hasStripePaymentsIntegration())
                            <x-stripe-payment
                                :order="$order"
                                :merchant="$merchant"
                                :stripeMerchantAccountId="$stripeMerchantAccountId"
                                :stripeConnectAccountId="$stripeConnectAccountId"
                            />
                        @endif
                        @if (isset($order))
                            @if (!$order->items->isEmpty())
                                <div class="checkout__cart">
                                    <div class="cart__order">
                                        @foreach ($order->items as $item)
                                        <div class="cart__item">
                                            <div class="cart__line">
                                                <span class="increment__value">{{ $item->inventory->quantity }}</span>
                                                <p class="cart__title">{{ $item->inventory->title }}</p>
                                                <span class="cart__price text-right">£{{ $item->getFormattedUKPriceAttribute($item->inventory->price) }}</span>
                                            </div>
                                            <div class="cart__options">
                                                <div class="cart__option-item">
                                                    <p class="cart__subtitle">
                                                        <span class="icon icon-add">@svg('add', 'fill-cloud-burst')</span>
                                                        Curry sauce</p>
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                        @if($order->customer_note)
                                        <div class="cart__notes">
                                            <p><span class="font-gibson-med">Notes</span> {{$order->customer_note}}</p>
                                        </div>
                                        @endif
                                    </div>
                                    <div class="subtotal">
                                        <div class="subtotal__item">
                                            <p class="subtotal__title">Subtotal</p>
                                            <span class="cart__price text-right">
                                                £{{ $order->getFormattedUKPriceAttribute($order->total_cost) }}
                                            </span>
                                        </div>
                                        <div class="subtotal__item subtotal__item--light">
                                            <p class="subtotal__title">Delivery</p>
                                            <span class="cart__price text-right">
                                                £{{ $merchant->getFormattedUKPriceAttribute($merchant->delivery_cost) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="total">
                                        <div class="total__item">
                                            <p class="total__title">Total</p>
                                            <span class="cart__price text-right">
                                                £{{ $order->getFormattedUKPriceAttribute($order->total_cost, $merchant->delivery_cost) }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endif
                        <div class="field field--buttons margin-top-30">
                            <input type="hidden" name="order_no" value="{{ $order->url_slug }}"/>
                            <button type="submit"
                                @if ($intentSecret !== null) id="submit_button" data-secret="{{ $intentSecret }}" @endif
                                class="button button-solid--carnation">
                                <span class="button__content">Confirm order details</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection
