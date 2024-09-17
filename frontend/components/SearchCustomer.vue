<template>
  <v-dialog v-model="searchDialog" width="350">
    <template v-slot:activator="{ on, attrs }">
      <div style="width: 50px" class="ma-2 text-center">
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
      </div>
    </template>
    <v-card>
      <!-- <v-alert class="primary" dense dark>
        <v-row>
          <v-col> Customer </v-col>
          <v-col>
            <div class="text-right">
              <v-icon @click="searchDialog = false">mdi-close</v-icon>
            </div>
          </v-col>
        </v-row>
      </v-alert> -->

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
            <v-icon color="primary" :loading="checkLoader" @click="get_customer"
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
