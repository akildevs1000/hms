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
          label="Search By Name or Mobile Number"
          dense
          outlined
          v-model="search"
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
                height: 40px;
              "
              class="primary white--text"
              :loading="checkLoader"
              @click="getData"
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
    search: null,
  }),
  created() {},
  methods: {
    getData() {
      this.checkLoader = true;
      if (this.search == undefined || this.search == "") {
        alert("Enter Vendor Name or Contact Number");
        this.checkLoader = false;
        return;
      }
      let payload = {
        params: {
          company_id: this.$auth.user.company.id,
          search: this.search,
        },
      };

      this.$axios.get(`vendor-search`, payload).then(({ data }) => {
        if (!data) {
          this.checkLoader = false;
          alert("Vendor not found");
          this.searchDialog = false;
          this.vendor = null;
          return;
        }

        this.$emit(`foundVendor`, data);

        this.searchDialog = false;
        this.checkLoader = false;
      });
    },
  },
};
</script>
