<template>
  <div class="service-time">
    <span class="icon icon--time flex">
      <Timer width="30" />
    </span>
    <flat-pickr
      ref="datePicker"
      v-model="datetime"
      :config="config"
      @on-change="dateChange"
    />
    <span
      class="service-time__button"
      @click="toggleCalendar"
    >
      Change
    </span>
  </div>
</template>

<script>
import { mapState } from 'vuex';
import Timer from '@/js/components/svgs/Timer';
import flatPickr from 'vue-flatpickr-component';

export default {
  components: {
    flatPickr,
    Timer,
  },
  props: {
    merchantHours: {
      type: Array,
      default: () => [],
    },
  },
  data() {
    return {
      datetime: null,
      config: {
        altInput: true,
        altFormat: 'J M, H:i',
        minDate: 'today',
        enableTime: true,
        time_24hr: true,
        enable: [
          (date) => this.availableDays.includes(date.getDay()),
        ],
      },
    };
  },
  computed: {
    ...mapState({
      stateDatetime: (state) => state.cart.datetime,
    }),
    availableDays() {
      return this.merchantHours.map((hours) => hours.day_of_week);
    },
    unavailableDays() {
      return [0, 1, 2, 3, 4, 5, 6].filter((day) => !this.availableDays.includes(day));
    },
    selectedDay() {
      return this.$moment(this.datetime).day();
    },
    currentMerchantHours() {
      return this.merchantHours.find((hours) => hours.day_of_week === this.selectedDay);
    },
  },
  mounted() {
    this.datetime = this.stateDatetime
      ? this.stateDatetime
      : this.$moment().format('YYYY-MM-DD HH:mm:ss');

    this.$emit('input', this.datetime);
  },
  methods: {
    dateChange(selectedDates, dateStr, instance) {
      instance.set('minTime', this.$moment(this.currentMerchantHours.open_time).format('HH:mm'));
      instance.set('maxTime', this.$moment(this.currentMerchantHours.close_time).format('HH:mm'));

      this.$emit('input', dateStr);
    },
    toggleCalendar() {
      this.$refs.datePicker.fp.toggle();
    },
  },
};
</script>
