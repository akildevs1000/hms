<template>
  <div v-if="can('master')">
    <v-row>
      <v-col cols="12">
        <v-row>
          <v-col
            xs="12"
            sm="12"
            md="3"
            cols="12"
            v-for="(i, index) in total_items"
            :key="index"
          >
            <v-card class="no_print">
              <v-list-item three-line>
                <v-list-item-content>
                  <div class="text-overline mb-4">
                    {{ i.title }}
                  </div>
                  <v-list-item-title class="text-h5 mb-1">
                    {{ i.value }}
                  </v-list-item-title>
                </v-list-item-content>

                <v-list-item-avatar size="60" color="primary">
                  <v-icon dark>{{ i.icon }}</v-icon>
                </v-list-item-avatar>
              </v-list-item>
            </v-card>
          </v-col>
        </v-row>
      </v-col>
    </v-row>
    <v-row>
      <v-col cols="12">
        <DataTable :title="title" :headers="headers" :endpoint="endpoint" />
      </v-col>
    </v-row>
  </div>
  <NoAccess v-else />
</template>

<script>
export default {
  layout({ $auth }) {
    let { user_type } = $auth.user;
    if (user_type == "master") {
      return "master";
    } else if (user_type == "employee") {
      return "employee";
    } else if (user_type == "master") {
      return "default";
    }
  },
  data() {
    return {
      tota4l_items: [],
      headers: [
        { text: "Company Code", value: "id" },
        { text: "Company Name", value: "name" },
        { text: "Contact Number", value: "contact.number" },
        { text: "Contact Name", value: "contact.name" },
        { text: "Max Devices", value: "max_devices" },
        { text: "Max Employees", value: "max_employee" },
        { text: "Location", value: "location" }
      ],
      data: [],
      title: `Lattest Companies`,
      endpoint: "company"
    };
  },
  created() {
    this.initialize();
  },
  methods: {
    can(per) {
      let u = this.$auth.user;
      return u && u.user_type == per;
    },

    getColor(calories) {
      if (calories > 400) return "red";
      else if (calories > 200) return "orange";
      else return "green";
    },
    async initialize() {
      this.total_items = [
        {
          title: "TOTAL COMPANIES",
          value: "254",
          icon: "mdi-apps"
        },
        {
          title: "TOTAL EMPLOYEES",
          value: "267",
          icon: "mdi-account"
        },
        {
          title: "TOTAL UNPAID",
          value: "4000",
          icon: "mdi-bank"
        },
        {
          title: "TOTAL PAID",
          value: "8000",
          icon: "mdi-bank"
        }
      ];
    }
  }
};
</script>
