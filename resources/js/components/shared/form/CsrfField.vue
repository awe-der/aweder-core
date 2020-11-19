<template>
  <input
    type="hidden"
    name="_token"
    :value="csrf"
  >
</template>
<script>
export default {
  data() {
    return {
      csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
    };
  },
};
</script>
