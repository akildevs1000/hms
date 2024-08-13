<template>
  <div>
    <div class="d-flex justify-center">
      <v-btn
        small
        class="pt-4 pb-4 mr-1 elevation-0"
        color="#ECF0F4"
        @click="process('foodplan-print')"
      >
        Print
        <v-icon right>mdi-printer</v-icon>
      </v-btn>
      <v-btn
        small
        class="pt-4 pb-4 elevation-0"
        color="#ECF0F4"
        @click="process('foodplan-download')"
      >
        Download
        <v-icon right>mdi-file</v-icon>
      </v-btn>
    </div>
    <table v-for="(item, index) in data" :key="index" class="mt-4">
      <tr style="background-color: white; color: black" class="my-0 py-0">
        <th class="my-0 py-0">Room No</th>
        <th class="my-0 py-0">Breakfast</th>
        <th class="my-0 py-0">Lunch</th>
        <th class="my-0 py-0">Dinner</th>
      </tr>
      <tr style="background-color: white" class="my-0 py-0">
        <td class="my-0 py-0">
          {{ item.room_no }}
        </td>
        <td class="my-0 py-0">
          {{ item.breakfast }}
        </td>
        <td class="my-0 py-0">
          {{ item.lunch }}
        </td>
        <td class="my-0 py-0">
          {{ item.dinner }}
        </td>
      </tr>
    </table>
  </div>
</template>
<script>
export default {
  data() {
    return {
      snackbar: false,
      response: "",
      preloader: false,
      loading: false,
      data: [],
      errors: [],
    };
  },

  created() {
    this.preloader = false;
  },

  mounted() {
    this.get_food_order_list();
  },

  computed: {},
  methods: {
    closeDialog(data) {
      this.$emit("close-dialog", data);
    },

    process(type) {
      let comId = this.$auth.user.company.id;
      let url = process.env.BACKEND_URL + `${type}?company_id=${comId}`;
      console.log(url);
      let element = document.createElement("a");
      element.setAttribute("target", "_blank");
      element.setAttribute("href", `${url}`);
      document.body.appendChild(element);
      element.click();
    },

    get_food_order_list() {
      let payload = {
        params: {
          company_id: this.$auth.user.company.id,
        },
      };
      this.$axios.get(`booked-foodplans`, payload).then(({ data }) => {
        this.data = data;
      });
    },
  },
};
</script>

<style scoped src="@/assets/css/checkout.css"></style>
