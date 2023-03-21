<template>
  <div>
    <div class="d-flex justify-center">
      <v-btn
        small
        class="pt-4 pb-4 mr-1 elevation-0"
        color="#ECF0F4"
        @click="process('food_print')"
      >
        Print
        <v-icon right>mdi-printer</v-icon>
      </v-btn>
      <v-btn
        small
        class="pt-4 pb-4 elevation-0"
        color="#ECF0F4"
        @click="process('food_download')"
      >
        Download
        <v-icon right>mdi-file</v-icon>
      </v-btn>
    </div>
    <table v-for="(item, index) in data" :key="index" class="mt-4">
      <tr style="background-color: white; color: black" class="my-0 py-0">
        <th class="my-0 py-0">Room No - {{ item.room_no || "---" }}</th>
        <th class="my-0 py-0">Adult</th>
        <th class="my-0 py-0">Child</th>
        <th class="my-0 py-0">Baby</th>
      </tr>
      <tr style="background-color: white" class="my-0 py-0">
        <td class="my-0 py-0">
          {{ (item && item.breakfast && item.breakfast.title) || "Breakfast" }}
        </td>
        <td class="my-0 py-0">
          {{ (item && item.breakfast && item.breakfast.no_of_adult) || "---" }}
        </td>
        <td class="my-0 py-0">
          {{ (item && item.breakfast && item.breakfast.no_of_child) || "---" }}
        </td>
        <td class="my-0 py-0">
          {{ (item && item.breakfast && item.breakfast.no_of_baby) || "---" }}
        </td>
      </tr>
      <tr style="background-color: white" class="my-0 py-0">
        <td class="my-0 py-0">
          {{ (item && item.lunch && item.lunch.title) || "Lunch" }}
        </td>
        <td class="my-0 py-0">
          {{ (item && item.lunch && item.lunch.no_of_adult) || "---" }}
        </td>
        <td class="my-0 py-0">
          {{ (item && item.lunch && item.lunch.no_of_child) || "---" }}
        </td>
        <td class="my-0 py-0">
          {{ (item && item.lunch && item.lunch.no_of_baby) || "---" }}
        </td>
      </tr>
      <tr style="background-color: white" class="my-0 py-0">
        <td class="my-0 py-0">
          {{ (item && item.dinner && item.dinner.title) || "Dinner" }}
        </td>
        <td class="my-0 py-0">
          {{ (item && item.dinner && item.dinner.no_of_adult) || "---" }}
        </td>
        <td class="my-0 py-0">
          {{ (item && item.dinner && item.dinner.no_of_child) || "---" }}
        </td>
        <td class="my-0 py-0">
          {{ (item && item.dinner && item.dinner.no_of_baby) || "---" }}
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
      this.$axios.get(`food/`, payload).then(({ data }) => {
        this.data = data;
      });
    },
  },
};
</script>

<style scoped src="@/assets/custom/checkout.css"></style>
