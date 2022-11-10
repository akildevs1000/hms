<template>
  <div class="mt-15">
    <div class="text-center ma-2">
      <v-snackbar v-model="snackbar" small top="top" color="background">
        {{ response }}
      </v-snackbar>
    </div>
    <table>
      <tr>
        <th>Status</th>
        <td>
          <v-switch
            color="success"
            class="mt-0 ml-2"
            v-model="setting.status"
          ></v-switch>
        </td>
      </tr>

      <tr>
        <th>Mobile Application</th>
        <td>
          <v-switch
            color="success"
            class="mt-0 ml-2"
            v-model="setting.mobile_application"
          ></v-switch>
        </td>
      </tr>

      <tr>
        <th>Over Time</th>
        <td>
          <div class="text-overline mb-1">
            <v-switch
              color="success"
              class="mt-0 ml-2"
              v-model="setting.overtime"
            ></v-switch>
          </div>
        </td>
      </tr>
      <div class="w-100">
        <v-btn class="primary mt-1 w-25" @click="update_setting()">Save</v-btn>
      </div>
    </table>
  </div>
</template>

<script>
export default {
  props: ["setting"],
  data() {
    return {
      response: "",
      snackbar: false
    };
  },
  created() {},
  methods: {
    caps(str) {
      if (str == "" || str == null) {
        return "---";
      } else {
        let res = str.toString();
        return res.replace(/\b\w/g, c => c.toUpperCase());
      }
    },
    update_setting() {
      let payload = {
        company_id: this.$auth?.user?.company?.id,
        employee_id: this.setting.employee_id,
        status: this.setting.status,
        overtime: this.setting.overtime,
        mobile_application: this.setting.mobile_application
      };

      this.$axios
        .post(`employee/update/setting`, payload)
        .then(({ data }) => {
          this.loading = false;

          if (data != 1) {
            this.errors = data.errors;
          } else {
            this.errors = [];
            this.snackbar = true;
            this.response = "successfully updated ";
            console.log("success");
          }
        })
        .catch(e => console.log(e));
    }
  }
};
</script>

<style scoped>
table {
  font-family: arial, sans-serif;
  border-collapse: collapse;
  width: 100%;
}

td,
th {
  text-align: left;
  padding: 8px;
}

tr:nth-child(even) {
  background-color: #fbfdff;
}
</style>
