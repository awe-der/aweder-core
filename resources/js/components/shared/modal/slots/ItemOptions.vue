<template>
  <modal
    v-if="product"
    ref="item_options"
    :is-active="!!product"
    @close="close()"
  >
    <header class="modal__header flex">
      <h2 class="header">
        {{ product.title }}
      </h2>
    </header>
    <div class="modal__content">
      <div
        v-if="product.variants.length"
        class="order-options"
      >
        <h3 class="order-options__title body-large">
          Options
        </h3>
        <div
          v-for="variant in product.variants"
          :key="variant.id"
          class="field field--radio"
        >
          <input
            :id="variant.name"
            v-model="selectedVariant"
            :value="variant"
            type="radio"
            :name="variant.name"
            class="radio-input hidden"
          >
          <label
            class="radio radio--standard radio--icon radio--icon-small"
            :for="variant.name"
          >
            <span
              class="radio__icon radio__icon--medium"
            />
            <span class="radio__label checkbox__label--large">
              {{ variant.name }}
              <span class="separator separator--small" />
              {{ variant.price | currency }}
            </span>
          </label>
        </div>
      </div>
      <div
        v-if="product.option_groups.length && options.length"
        class="order-options"
      >
        <div
          v-for="(group, index) in product.option_groups"
          class="order-options__groups"
          :key="group.id"
        >
          <h3 class="order-options__title body-large">
            {{ group.name }}
          </h3>
          <div
            v-for="item in group.items"
            :key="item.id"
            class="field field--checkbox"
          >
            <input
              :id="item.name + item.id"
              v-model="options[index].items"
              :value="item"
              type="checkbox"
              :name="item.id"
              class="checkbox-input hidden"
            >
            <label
              :for="item.name + item.id"
              class="checkbox checkbox--standard"
            >
              <span class="checkbox__icon checkbox__icon--medium">
                <Tick />
              </span>
              <span class="checkbox__label checkbox__label--large flex align-items-center">
                {{ item.name }}
                <span class="separator separator--small" />
                {{ item.price_modified | currency }}
              </span>
            </label>
          </div>
        </div>
      </div>
    </div>
    <div class="modal__buttons flex modal__buttons--qty">
      <div class="increment increment--large">
        <button
          class="increment__type increment__type--down"
          @click="decrement"
        >
          <Minus />
        </button>
        <input
          type="text"
          class="increment__value"
          :value="quantity"
        >
        <button
          class="increment__type increment__type--up"
          @click="increment"
        >
          <Add />
        </button>
      </div>
      <button
        ref="add_item"
        class="button button-solid--carnation"
        @click="add()"
      >
        <span class="button__content">Add item</span>
      </button>
    </div>
  </modal>
</template>

<script>
import Modal from '@/js/components/shared/modal/Modal';
import orderApi from '@/js/api/order/order';
import Add from '@/js/components/svgs/Add';
import Minus from '@/js/components/svgs/Minus';
import Tick from '@/js/components/svgs/Tick';
import { mapState } from 'vuex';

export default {
  components: {
    Modal,
    Add,
    Minus,
    Tick,
  },
  props: {
    merchant: {
      type: Object,
      default: () => {},
    },
  },
  data() {
    return {
      quantity: 1,
      options: [],
      selectedVariant: null,
    };
  },
  computed: {
    ...mapState({
      product: (state) => state.activeProduct.product,
      order: (state) => state.cart.order,
      cartProducts: (state) => state.cart.products,
    }),
    selectedOptions() {
      const items = this.options.map((group) => group.items);
      return [].concat(...items).map((item) => item.id);
    },
  },
  watch: {
    product(value) {
      if (value) {
        this.reset();
      }
    },
  },
  methods: {
    async add() {
      if (this.order === null) {
        await this.createOrder();
      }
      await this.addItem();
      this.close();
    },
    close() {
      this.$store.dispatch('activeProduct/removeActiveProduct');
    },
    increment() {
      this.quantity += 1;
    },
    decrement() {
      this.quantity = this.quantity > 1
        ? this.quantity - 1
        : 1;
    },
    createOptionGroups() {
      this.product.option_groups.forEach((group) => {
        this.options.push({
          group: group.name,
          items: [],
        });
      });
    },
    reset() {
      this.quantity = 1;
      this.options = [];
      [this.selectedVariant] = this.product.variants;
      this.createOptionGroups();
    },
    async createOrder() {
      const res = await orderApi.create({ merchant: this.merchant.url_slug });
      if (res.status === 201) {
        this.$store.dispatch('cart/setOrder', res.data.url_slug);
        window.history.pushState(null, '', `?order=${res.data.url_slug}`);
      }
    },
    async addItem() {
      const res = await orderApi.addItem(this.order, {
        inventory_id: this.product.id,
        variant_id: this.selectedVariant.id,
        merchant: this.merchant.url_slug,
        inventory_options: this.selectedOptions,
      });

      if (res.status === 200) {
        this.$store.dispatch('cart/addToCart', {
          id: this.findMissingItemId(res.data.items),
          product: this.product,
          variant: this.selectedVariant,
          options: this.options,
          quantity: this.quantity,
        });
      }
    },
    findMissingItemId(items) {
      // since the api response includes all items,
      // we need to find the one just added that isn't in our local cart
      const cartIds = this.cartProducts.map((product) => product.id);
      return items.find((item) => !cartIds.includes(item.id)).id;
    },
  },
};
</script>
