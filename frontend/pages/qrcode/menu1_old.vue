<template>
  <div>
    <div class="bgimage1">
      <div class="banner-title">Hotel Information</div>
    </div>
    <div class="bgimage2">
      <div class="banner-title">Food/Restaurant Menu</div>
    </div>
    <div class="bgimage3">
      <div class="banner-title">Laundry</div>
    </div>
    <div class="bgimage4">
      <div class="banner-title">House Keeping</div>
    </div>
    <div class="bgimage5">
      <div class="banner-title">Room Supplies</div>
    </div>
    <div class="bgimage6">
      <div class="banner-title">Porter Service</div>
    </div>
    <div class="bgimage7">
      <div class="banner-title">Room Problems</div>
    </div>
    <div class="bgimage8">
      <div class="banner-title">Checkout</div>
    </div>
    <div class="bgimage9">
      <div class="banner-title">Phonebook</div>
    </div>
  </div>
</template>

<script>
export default {
  layout: "qrcode",
  data: () => ({
    id: "",
  }),
  auth: false,
  mounted() {},
  created() {
    this.id = this.$route.params.id;
  },
  methods: {},
};
</script>
