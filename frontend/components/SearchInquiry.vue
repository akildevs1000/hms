<template>
  <v-dialog v-model="searchDialog" width="350">
    <template v-slot:activator="{ on, attrs }">
      <v-icon  class="mt-1" color="primary" v-bind="attrs" v-on="on">mdi-magnify</v-icon>

    </template>
    <v-card>
      <v-alert class="primary" dense dark>
        <v-row>
          <v-col> Customer </v-col>
          <v-col>
            <div class="text-right">
              <v-icon @click="searchDialog = false">mdi-close</v-icon>
            </div>
          </v-col>
        </v-row>
      </v-alert>

      <v-card-text>
        <v-text-field
          label="Search By Mobile Number"
          dense
          outlined
          v-model="contact_no"
          :hide-details="true"
        >
          <template v-slot:append>
            <v-icon :loading="checkLoader" right @click="get_customer"
              >mdi-magnify</v-icon
            >
          </template>
        </v-text-field>
      </v-card-text>
    </v-card>
  </v-dialog>
</template>
<script>
export default {
  data: () => ({
    searchDialog: false,
    checkLoader: false,
    contact_no: null,
  }),
  created() {},
  methods: {
    get_customer() {
      this.checkLoader = true;
      if (this.contact_no == undefined || this.contact_no == "") {
        alert("Enter contact number");
        this.checkLoader = false;
        return;
      }
      let payload = {
        params: {
          company_id: this.$auth.user.company.id,
        },
      };

      this.$axios
        .get(`get_inquiry/${this.contact_no}`, payload)
        .then(({ data }) => {
          this.$emit(`foundCustomer`, data);
          this.searchDialog = false;
          this.checkLoader = false;
        })
        .catch(() => {
          alert("Customer not found");
          this.searchDialog = false;
          this.checkLoader = false;
        });
    },
  },
};
</script>
