<template>
  <modal
    :is-active="isActive"
    class="modal--table-service"
    size="small"
  >
    <header class="modal__header modal__header--no-border flex align-items-end">
      <h2 class="header">
        Table service
      </h2>
    </header>
    <validation-observer v-slot="{ invalid }">
      <div class="modal__content">
        <validation-provider
          v-slot="v"
          rules="required"
        >
          <div
            class="field"
            :class="{ 'field--error': v.errors.length }"
          >
            <label
              class="label label--float"
              for="table"
            >
              Please enter your table number
            </label>
            <input
              id="table"
              ref="table"
              v-model="table"
              type="text"
              class="text-input"
            >
            <p
              v-if="v.errors.length"
              class="field__error"
            >
              Please enter a valid table number
            </p>
          </div>
        </validation-provider>
      </div>
      <div class="modal__buttons modal__buttons--no-border flex flex-col">
        <button
          class="button button--wide"
          :class="invalid ? 'button-solid--silver' : 'button-solid--carnation'"
          :disabled="invalid"
          @click="save()"
        >
          <span class="button__content">Confirm</span>
        </button>
        <a
          class="modal__addition-link"
          :href="takeAwayUrl"
        >
          Looking to take away? Click here.
        </a>
      </div>
    </validation-observer>
  </modal>
</template>

<script>
import { mapState } from 'vuex';
import Modal from '@/js/components/shared/modal/Modal';

export default {
  components: {
    Modal,
  },
  props: {
    merchant: {
      type: Object,
      default: null,
    },
  },
  data() {
    return {
      table: '',
    };
  },
  computed: {
    errorMessage() {
      return this.validationMessage;
    },
    ...mapState({
      isActive: (state) => state.modals.selectTable,
    }),
    takeAwayUrl() {
      return `/${this.merchant.url_slug}/take-away`;
    },
  },
  mounted() {
    this.$refs.table.focus();
  },
  methods: {
    save() {
      this.$store.dispatch('cart/setTable', this.table);
      this.$store.dispatch('modals/setSelectTable', false);
    },
  },
};
</script>
