<template>
  <v-dialog v-model="searchDialog" width="400">
    <template v-slot:activator="{ on, attrs }">
      <v-btn small color="primary" v-bind="attrs" v-on="on">
        Search
        <v-icon right dark>mdi-magnify</v-icon>
      </v-btn>
    </template>
    <v-card>
      <v-toolbar flat dense class="blue white--text">
        Customer <v-spacer></v-spacer
        ><v-icon @click="searchDialog = false" color="white"
          >mdi-close</v-icon
        ></v-toolbar
      >
      <v-card-text>
        <v-text-field
          class="mt-4"
          label="Search By Mobile Number"
          dense
          outlined
          type="text"
          v-model="contact_no"
          hide-details
        ></v-text-field>
      </v-card-text>
      <v-divider></v-divider>
      <v-card-actions>
       <v-container class="text-right">
        <v-btn color="primary" @click="get_customer" :loading="checkLoader">
          Search
          <v-icon right dark>mdi-magnify</v-icon>
        </v-btn>
       </v-container>
      </v-card-actions>
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
        .get(`get_customer/${this.contact_no}`, payload)
        .then(({ data }) => {
          if (!data.status) {
            this.checkLoader = false;
            alert("Customer not found");
            return;
          }

          this.$emit(`foundCustomer`, data.data);

          this.searchDialog = false;
          this.checkLoader = false;
        });
    },
  },
};
</script>
