<template>
  <modal
    :is-active="isActive"
    class="modal--takeaway-type"
    size="small"
  >
    <header class="modal__header modal__header--no-border flex align-items-end">
      <h2 class="header">
        Select take away type and time
      </h2>
    </header>
    <div class="modal__content">
      <div class="modal__service-types">
        <button
          class="service-button"
          :class="{ 'service-button--active': type === 'delivery' }"
          @click="setType('delivery')"
        >
          <span
            class="service-button__icon icon icon--delivery"
          >
            <Delivery />
          </span>
          <span class="service-button__label">
            Delivery
          </span>
        </button>
        <button
          class="service-button"
          :class="{ 'service-button--active': type === 'collection' }"
          @click="setType('collection')"
        >
          <span
            class="service-button__icon icon icon--collection"
          >
            <Collection />
          </span>
          <span class="service-button__label">
            Collection
          </span>
        </button>
      </div>
      <div class="modal__service-time">
        <merchant-date-time-picker
          v-model="datetime"
          :merchant-hours="merchantHours"
        />
      </div>
    </div>
    <div
      class="modal__buttons modal__buttons--no-border flex flex-col"
    >
      <button
        class="button"
        :class="'button-solid--carnation'"
        @click="save()"
      >
        <span class="button__content">Save</span>
      </button>
      <a
        class="modal__addition-link"
        :href="tableOrderUrl"
      >
        Eating in? Click here.
      </a>
    </div>
  </modal>
</template>

<script>
import { mapState } from 'vuex';
import Modal from '@/js/components/shared/modal/Modal';
import MerchantDateTimePicker from '@/js/components/shared/MerchantDateTimePicker';
import Delivery from '@/js/components/svgs/Delivery';
import Collection from '@/js/components/svgs/Collection';

export default {
  components: {
    Modal,
    Delivery,
    Collection,
    MerchantDateTimePicker,
  },
  props: {
    merchant: {
      type: Object,
      default: () => {},
    },
  },
  data() {
    return {
      type: 'delivery',
      datetime: null,
      showCalendar: false,
    };
  },
  computed: {
    ...mapState({
      isActive: (state) => state.modals.orderType,
    }),
    merchantHours() {
      return this.merchant.opening_hours.map((hours) => ({
        ...hours,
        day_of_week: hours.day_of_week - 1,
      }));
    },
    tableOrderUrl() {
      return `/${this.merchant.url_slug}/table-order`;
    },
  },
  methods: {
    toggleCalendar() {
      this.showCalendar = !this.showCalendar;
    },
    setType(type) {
      this.type = type;
    },
    save() {
      this.$store.dispatch('cart/setOrderType', this.type);
      this.$store.dispatch('cart/setDatetime', this.datetime);
      this.$store.dispatch('modals/setOrderType', false);
    },
  },
};
</script>
