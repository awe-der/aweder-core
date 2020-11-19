<template>
  <div
    class="field col-span-4 m-col-span-6 sm-col-span-6 row-start-2 sm-row-start-3"
    :class="{ 'field__error': exists === true }"
  >
    <label
      class="label label--float"
      for="url_slug"
    >
      Business's URL slug<sup>*</sup>
    </label>
    <input
      class="text-input"
      v-model="urlSlug"
      id="url_slug"
      type="text"
      name="url_slug"
      tabindex="5"
      placeholder="URL slug"
      @keyup="doesUrlExist"
    />
    <p
      v-if="validationError || exists"
      class="field__error field__error--slug"
    >
      {{ validationMessage }}
    </p>
    <p class="field__note">
      This will generate your url - for example - if you enter red-lion you will have https://aweder.net/red-lion
    </p>
  </div>
</template>
<script>
import slugChecker from '@/js/api/merchant/slug';

export default {
  name: 'UrlSlugChecker',
  props: {
    validationError: {
      type: Boolean,
      required: false,
      default: false,
    },
    validationMessage: {
      type: String,
      required: false,
      default: 'The slug is already taken.',
    },
    urlValue: {
      type: String,
      required: false,
      default: '',
    },
  },
  data() {
    return {
      slug: '',
      exists: false,
    };
  },
  computed: {
    errorMessage() {
      return this.validationMessage;
    },
    urlSlug: {
      get() {
        return this.slug;
      },
      set(newSlug) {
        if (newSlug.length === 0 && this.urlValue.length > 0) {
          this.slug = this.urlValue;
        } else {
          this.slug = newSlug;
        }
        return this.slug;
      },
    },
  },
  created() {
    if (this.urlValue && this.urlValue.length > 0) {
      this.urlSlug = this.urlValue;
    }

    if (this.validationError === true) {
      this.exist = true;
    }
  },
  methods: {
    doesUrlExist() {
      if (this.urlSlug.length >= 3) {
        slugChecker.checkIfBusinessSlugExists(this.urlSlug).then((response) => {
          this.exists = response.data.exists === true;
        }).catch((error) => {
        });
      }
    },
  },

};
</script>
