<template>
  <v-dialog v-model="searchDialog" width="350">
    <template v-slot:activator="{ on, attrs }">
      <div style="width: 130px" class="ma-2">
        <v-text-field
          v-bind="attrs"
          v-on="on"
          readonly
          placeholder="Search..."
          outlined
          dense
          color="primary"
          small
        >
          <template v-slot:append>
            <v-icon right>mdi-magnify</v-icon>
          </template>
        </v-text-field>
      </div>
    </template>
    <v-card>
      <v-alert class="primary" dense dark>
        <v-row>
          <v-col>
            Customer
          </v-col>
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
              type="text"
              v-model="contact_no"
              :hide-details="true"
            >
              <template v-slot:append>
                <v-icon
                  :loading="checkLoader"
                  color="primary"
                  right
                  @click="get_customer"
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
        .get(`get_customer/${this.contact_no}`, payload)
        .then(({ data }) => {
          if (!data.status) {
            this.checkLoader = false;
            alert("Customer not found");
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
