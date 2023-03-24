<template>
  <div>
    <div class="d-flex justify-center">
      <v-btn
        small
        class="pt-4 pb-4 mr-1 elevation-0"
        color="#ECF0F4"
        @click="process('available_rooms_print')"
      >
        Print
        <v-icon right>mdi-printer</v-icon>
      </v-btn>
      <v-btn
        small
        class="pt-4 pb-4 elevation-0"
        color="#ECF0F4"
        @click="process('available_rooms_download')"
      >
        Download
        <v-icon right>mdi-file</v-icon>
      </v-btn>
    </div>

    <table class="mt-4">
      <tr style="background-color: white; color: black" class="my-0 py-0">
        <th class="my-0 py-0">#</th>
        <th class="my-0 py-0 text-center">Room No</th>
        <th class="my-0 py-0 text-center">Type</th>
        <th class="my-0 py-0 text-center">Status</th>
      </tr>
      <tr
        v-for="(item, index) in data"
        :key="index"
        style="background-color: white"
        class="my-0 py-0"
      >
        <td class="my-1 py-1">{{ ++index }}</td>
        <td class="my-1 py-1 text-center">{{ item.room_no || "---" }}</td>
        <td class="my-1 py-1 text-center">
          {{ (item && item.room_type && item.room_type.name) || "---" }}
        </td>
        <td class="my-1 py-1">
          <div
            :style="
              item.status == 1
                ? 'background-color: #d60078;width: 200px;height: 10px; margin: 0 auto;'
                : 'background-color: #32a15c;width: 200px;height: 10px; margin: 0 auto'
            "
          ></div>
        </td>
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

<style scoped src="@/assets/custom/checkout.css"></style>
