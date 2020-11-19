<template>
  <div class="inventory__editable-add">
    <a
      class="inventory__add-category"
      @click="showCreateModal"
    >
      Add category
      <span class="icon icon--add"><Add /></span>
    </a>
    <create-category
      :is-active="isActive"
      :merchant="merchant"
      @close="hideCreateModal()"
    />
  </div>
</template>

<script>
import CreateCategory from '@/js/components/shared/modal/slots/CreateCategory';
import Add from '@/js/components/svgs/Add';

export default {
  name: 'AddCategory',
  components: {
    CreateCategory,
    Add,
  },
  props: {
    merchant: {
      type: String,
      default: '',
    },
  },
  data() {
    return {
      isActive: false,
    };
  },
  methods: {
    showCreateModal() {
      this.isActive = true;
    },
    hideCreateModal() {
      this.isActive = false;
    },
  },
};
</script>
