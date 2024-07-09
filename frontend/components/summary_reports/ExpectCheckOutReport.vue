<template>
  <div>
    <div class="d-flex justify-center">
      <v-btn
        small
        class="pt-4 pb-4 mr-1 elevation-0"
        color="#ECF0F4"
        @click="process('expect_checkout_report_print')"
      >
        Print
        <v-icon right>mdi-printer</v-icon>
      </v-btn>
      <v-btn
        small
        class="pt-4 pb-4 elevation-0"
        color="#ECF0F4"
        @click="process('expect_checkout_report_download')"
      >
        Download
        <v-icon right>mdi-file</v-icon>
      </v-btn>
    </div>

    <table class="mt-4">
      <tr style="background-color: white; color: black" class="my-0 py-0">
        <td class="my-0 py-0">Room No</td>
        <td class="my-0 py-0">Type</td>
        <td class="my-0 py-0">Guest</td>
        <td class="my-0 py-0">Check In</td>
        <td class="my-0 py-0">Check Out</td>
      </tr>
      <tr
        v-for="(item, index) in data"
        :key="index"
        style="background-color: white"
        class="my-0 py-0"
      >
        <td class="my-1 py-1">{{ item.room_no || "---" }}</td>
        <td class="my-1 py-1">{{ item.room_type.name || "---" }}</td>
        <td class="my-1 py-1">{{ item.title || "---" }}</td>
        <td class="my-1 py-1">{{ item.check_in || "---" }}</td>
        <td class="my-1 py-1">{{ item.check_out || "---" }}</td>
      </tr>
    </table>
  </div>
</template>
<script>
export default {
  props: ["data"],
  data() {
    return {
      snackbar: false,
      response: "",
      preloader: false,
      loading: false,
      errors: [],
    };
  },

  created() {
    this.preloader = false;
  },

  methods: {
    process(type) {
      let comId = this.$auth.user.company.id;
      let date = new Date().toJSON().slice(0, 10);
      let url =
        process.env.BACKEND_URL + `${type}?company_id=${comId}&date=${date}`;
      console.log(url);
      let element = document.createElement("a");
      element.setAttribute("target", "_blank");
      element.setAttribute("href", `${url}`);
      document.body.appendChild(element);
      element.click();
    },
  },
};
</script>

<style scoped src="@/assets/css/checkout.css"></style>
