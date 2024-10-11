<template>
  <v-dialog v-model="searchDialog" width="350">
    <template v-slot:activator="{ on, attrs }">
      <v-hover v-slot:default="{ hover, props }">
          <span v-bind="props">
            <v-icon
              class="zoom-on-hover"
              v-bind="attrs"
              v-on="on"
              :outlined="!hover"
              :color="hover ? `` : `primary`"
              :style="{ color: hover ? '#6946dd' : '' }"
              >mdi-magnify</v-icon
            >
          </span>
        </v-hover>
    </template>
    <v-card>
      <v-container>
        <v-text-field
          class="ma-0 pa-0"
          label="Search By Mobile Number"
          dense
          outlined
          v-model="contact_no"
          hide-details
        >
          <template v-slot:append>
            <v-icon
              style="
                border-radius: 5px;
                padding: 7px;
                top: 0px;
                position: absolute;
                right: 1px;
                height: 31px;
              "
              class="primary white--text"
              :loading="checkLoader"
              @click="get_customer"
              >mdi-magnify</v-icon
            >
          </template>
        </v-text-field>
      </v-container>
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
            this.searchDialog = false;
            this.contact_no = null;
            return;
          }

          this.$emit(`foundCustomer`, {
            ...data.data,
            customer_id: data.data.id,
          });

          this.searchDialog = false;
          this.checkLoader = false;
        });
    },
  },
};
</script>
