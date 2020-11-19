<template>
  <div
    class="cart panel panel--radius-bottom background-off-white col-span-3 col-start-9
    l-col-span-4 l-col-start-8 m-col-span-5 sm-col-span-6 sm-col-start-1"
    :class="{ 'cart--empty': !quantity }"
  >
    <div
      v-if="serviceType === 'take-away'"
      class="cart-service flex align-items-center"
    >
      <span class="icon icon--time">
        <Timer width="20" />
      </span>
      <span class="cart-service__content">
        {{ orderType | capitalize }}, {{ datetime | moment('Do MMM, HH:mm') }}
      </span>
      <span
        class="cart-service__button"
        @click="changeOrderType()"
      >
        Change
      </span>
    </div>
    <div v-if="quantity">
      <div
        v-if="serviceType === 'table-order'"
        class="cart-service flex align-items-center"
      >
        <div class="cart-service__content">
          Table: {{ table }}
        </div>
        <span
          class="cart-service__button"
          @click="changeTableNumber()"
        >
          Change
        </span>
      </div>
      <div class="cart__order">
        <div
          v-for="(item, index) in products"
          :key="index"
          class="cart__item"
        >
          <div class="cart__line">
            <p class="cart__title">
              {{ item.product.title }}
            </p>
            <div class="increment increment--small">
              <span
                class="increment__type increment__type--down"
                @click="decrementProduct(item)"
              >
                <Minus />
              </span>
              <input
                type="text"
                class="increment__value"
                :value="item.quantity"
              >
              <span
                class="increment__type increment__type--up"
                @click="incrementProduct(item)"
              >
                <Add />
              </span>
            </div>
            <span class="cart__price text-right">{{ item.variant.price| currency }}</span>
          </div>
          <div
            v-if="item.variant.price"
            class="cart__options cart__options--variant"
          >
            <div
              class="cart__option-item"
            >
              <p class="cart__subtitle">
                <span class="icon icon-add"><Add /></span>
                {{ item.variant.name }}
              </p>
            </div>
          </div>
          <div
            v-for="group in item.options"
            v-show="group.items.length"
            :key="group.group"
            class="cart__options cart__options--option"
          >
            <div
              v-for="option in group.items"
              :key="option.id"
              class="cart__option-item"
            >
              <p class="cart__subtitle">
                <span class="icon icon-add"><Add /></span>
                {{ option.name }}
              </p>
            </div>
          </div>
        </div>
      </div>
      <div class="subtotal">
        <div class="subtotal__item">
          <p class="subtotal__title">
            Subtotal
          </p>
          <span class="cart__price text-right">{{ subtotal | currency }}</span>
        </div>
        <div
          ref="delivery"
          class="subtotal__item subtotal__item--light"
        >
          <p class="subtotal__title">
            Delivery
          </p>
          <span
            v-if="isDelivery"
            class="cart__price text-right"
          >
            {{ merchant.delivery_cost | currency }}
          </span>
          <span
            v-else
            class="cart__price text-right"
          >
            £--.--
          </span>
        </div>
      </div>
      <div class="total">
        <div class="total__item">
          <p class="total__title">
            Total
          </p>
          <span class="cart__price text-right">{{ total | currency }}</span>
        </div>
      </div>
    </div>
    <div class="cart__buttons">
      <a
        :href="checkoutUrl"
        class="button"
        :class="quantity ? 'button-solid--carnation' : 'button-outline--silver'"
      >
        <span class="button__content">Place order</span>
      </a>
    </div>
  </div>
</template>

<script>
import Timer from '@/js/components/svgs/Timer';
import Add from '@/js/components/svgs/Add';
import Minus from '@/js/components/svgs/Minus';
import orderApi from '@/js/api/order/order';
import { mapState, mapGetters } from 'vuex';

export default {
  components: {
    Add,
    Minus,
    Timer,
  },
  props: {
    merchant: {
      type: Object,
      default: () => {},
    },
    serviceType: {
      type: String,
      default: 'take-away',
    },
  },
  computed: {
    ...mapState({
      products: (state) => state.cart.products,
      order: (state) => state.cart.order,
      orderType: (state) => state.cart.orderType,
      datetime: (state) => state.cart.datetime,
      table: (state) => state.cart.table,
    }),
    ...mapGetters({
      subtotal: 'cart/subtotal',
      quantity: 'cart/quantity',
    }),
    total() {
      return this.subtotal + this.extraCharges;
    },
    extraCharges() {
      if (this.isDelivery && this.deliveryCost) {
        return this.deliveryCost;
      }
      return 0;
    },
    isDelivery() {
      return this.orderType === 'delivery';
    },
    deliveryCost() {
      return this.merchant.delivery_cost;
    },
    checkoutUrl() {
      return this.order ? `/${this.merchant.url_slug}/table-order/${this.order}/order-details` : '';
    },
  },
  methods: {
    incrementProduct(item) {
      this.updateProductQuantity(
        item.id,
        (item.quantity + 1).toString(),
      );
    },
    decrementProduct(item) {
      if (item.quantity <= 1) {
        this.deleteProduct(item.id);
      } else {
        this.updateProductQuantity(
          item.id,
          (item.quantity - 1).toString(),
        );
      }
    },
    async updateProductQuantity(id, quantity) {
      const res = await orderApi.updateItem(this.order, id, {
        quantity,
        merchant: this.merchant.url_slug,
      });
      if (res.status === 200) {
        this.$store.dispatch('cart/updateProduct', res.data);
      }
    },
    async deleteProduct(id) {
      const res = await orderApi.deleteItem(this.order, id, { merchant: this.merchant.url_slug });
      if (res.status === 200) {
        this.$store.dispatch('cart/removeFromCart', id);
      }
    },
    changeOrderType() {
      this.$store.dispatch('modals/setOrderType', true);
    },
    changeTableNumber() {
      this.$store.dispatch('modals/setSelectTable', true);
    },
  },
};
</script>
