<template>
  <v-app>
    <v-container>
      <nuxt />
    </v-container>
  </v-app> 
</template>
<script>
export default {
  data: () => ({
    drawer: false,
    group: null,
    clipped: false,
    isMobile: "",
  }),

  watch: {
    group() {
      this.drawer = false
    },
  },

  mounted() {
    this.checkDeviceType();
    window.addEventListener('resize', this.checkDeviceType);
  },
  beforeDestroy() {
    window.removeEventListener('resize', this.checkDeviceType);
  },
  methods: {
    checkDeviceType() {
      this.isMobile = window.innerWidth <= 768; // Adjust the breakpoint as needed
    },
  },
}
</script>

