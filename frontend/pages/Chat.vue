<template>
  <ReceptionConsole :hotelId="3" :staffName="ReceiptionName" />
</template>
<script>
import ReceptionConsole from "../components/Chat/ReceptionConsole.vue";
export default {
  components: { ReceptionConsole },

  data() {
    return {
      ReceiptionName: "",
    };
  },

  created() {
    this.ReceiptionName = this.$auth.user.name;
  },
};
</script>
