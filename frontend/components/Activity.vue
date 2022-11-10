<template>
  <div>
    <v-toolbar dense class="primary" dark>
      <div>
        Application Activity
      </div>
      <v-spacer />
      <v-tooltip left>
        <template v-slot:activator="{ on, attrs }">
          <v-icon dark v-bind="attrs" v-on="on"
            >mdi-eye</v-icon
          >
        </template>
        <span>See All</span>
      </v-tooltip>
    </v-toolbar>
    <v-card>
      <v-card-text class="py-0">
        <v-timeline align-top dense>
          <v-timeline-item
            v-for="(item, index) in data"
            :key="index"
            color="primary"
            small
          >
            <v-row class="pt-1">
              <v-col>
                <strong>{{ item.action }}</strong>
                <div class="text-caption">
                  By
                  <strong class="primary--text">{{
                    item.employee && item.employee.first_name
                  }}</strong>
                  at {{ item.date_time }}
                </div>
              </v-col>
            </v-row>
          </v-timeline-item>
        </v-timeline>
      </v-card-text>
    </v-card>
  </div>
</template>
<script>
export default {
  data: () => ({
    data: []
  }),
  custom_options: {},

  watch: {
    dialog(val) {
      val || this.close();
      this.errors = [];
      this.search = "";
    },
    options: {
      handler() {
        this.getDataFromApi();
      },
      deep: true
    }
  },
  created() {
    this.loading = true;

    let custom_options = {
      params: {
        per_page: 1000
        // company_id: this.$auth.user.company.id
      }
    };
    this.$axios.get(`activity`, custom_options).then(({ data }) => {
      this.data = data.data;
      this.loading = false;
    });
  }
};
</script>
