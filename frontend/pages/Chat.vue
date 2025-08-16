<template>
  <ReceptionConsole
    v-if="company_id"
    :hotelId="company_id"
    :staffName="ReceiptionName"
  />
</template>
<script>
import ReceptionConsole from "../components/Chat/ReceptionConsole.vue";
export default {
  components: { ReceptionConsole },

  data() {
    return {
      ReceiptionName: "",

      company_id: null,
    };
  },

  created() {
    this.ReceiptionName = this.$auth.user.name;
    this.company_id = this.$auth.user.company_id;
  },
};
</script>
