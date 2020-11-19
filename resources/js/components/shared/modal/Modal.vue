<template>
  <div
    v-if="isActive"
    class="modal row"
  >
    <div
      class="modal__mask"
      @click="close()"
    />
    <div class="content justify-content-center align-items-center">
      <div
        class="modal__box m-col-span-8
         m-col-start-3 sm-col-span-6 sm-col-start-1"
        :class="{
          'col-span-6 col-start-4': size === 'medium',
          'col-span-4 col-start-5 l-col-span-6 l-col-start-4': size === 'small',
        }"
      >
        <slot />
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    isActive: {
      type: Boolean,
      default: false,
    },
    size: {
      type: String,
      default: 'medium',
    },
  },
  methods: {
    close() {
      this.$emit('close');
    },
  },
};
</script>
