<template>
  <modal :is-active="isActive">
    <div class="modal__content content">
      <div class="col-span-6">
        <a
          ref="take_away"
          :href="takeAwayUrl"
          class="button service-type__button"
        >
          <span class="button__icon button__icon--left"><Delivery /></span>
          <span class="button_content body-large">Take away</span>
        </a>
      </div>
      <div class="col-span-6 sm-margin-top-20">
        <a
          ref="table_service"
          :href="tableServiceUrl"
          class="button service-type__button"
        >
          <span class="button__icon button__icon--left"><Table /></span>
          <span class="button_content body-large">Table service</span>
        </a>
      </div>
    </div>
  </modal>
</template>

<script>
import Modal from '@/js/components/shared/modal/Modal';
import Delivery from '@/js/components/svgs/Delivery';
import Table from '@/js/components/svgs/Table';

export default {
  components: {
    Modal,
    Delivery,
    Table,
  },
  props: {
    isActive: {
      type: Boolean,
      default: false,
    },
    takeAwayUrl: {
      type: String,
      default: '',
    },
    tableServiceUrl: {
      type: String,
      default: '',
    },
  },
  mounted() {
    this.$refs.take_away.focus();
  },
};
</script>
