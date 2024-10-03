<template>
  <div class="text-center">
    <v-dialog v-model="dialog" width="650">
      <AssetsIconClose left="640" @click="close" />
      <template v-slot:activator="{ on, attrs }">
        <v-btn
          small
          color="primary"
          class="white--text"
          dark
          v-bind="attrs"
          v-on="on"
        >
          <v-icon color="white" small> mdi-plus </v-icon> New
        </v-btn>
      </template>

      <v-card>
        <v-alert flat class="grey lighten-3" dense>
          <span>Create {{ model }}</span>
        </v-alert>

        <v-card-text>
          <v-row>
              <v-col cols="6">
                <v-autocomplete
                  clearable
                  label="Select Vendor Category"
                  dense
                  outlined
                  v-model="payload.vendor_category_id"
                  :items="vendor_categories"
                  item-value="id"
                  item-text="name"
                  :hide-details="true"
                ></v-autocomplete>
              </v-col>
              <v-col cols="6">
                <v-autocomplete
                  clearable
                  label="Select Vendor Type"
                  dense
                  outlined
                  v-model="payload.type"
                  :items="[`Personal`, `Company`]"
                  :hide-details="true"
                ></v-autocomplete>
              </v-col>
              <v-col cols="4">
                <v-autocomplete
                  clearable
                  label="Title"
                  dense
                  outlined
                  v-model="payload.title"
                  :items="['Mr', 'Mrs', 'Ms', 'Dr', 'Prof']"
                  :hide-details="true"
                ></v-autocomplete>
              </v-col>
              <v-col cols="4">
                <v-text-field
                  outlined
                  dense
                  hide-details
                  v-model="payload.first_name"
                  label="First Name"
                ></v-text-field>
              </v-col>
              <v-col cols="4">
                <v-text-field
                  outlined
                  dense
                  hide-details
                  v-model="payload.last_name"
                  label="Last Name"
                ></v-text-field>
              </v-col>
              <v-col cols="6">
                <v-text-field
                  outlined
                  dense
                  hide-details
                  v-model="payload.company_name"
                  label="Company Name"
                ></v-text-field>
              </v-col>
              <v-col cols="6">
                <v-text-field
                  outlined
                  dense
                  hide-details
                  v-model="payload.tax_number"
                  label="Tax Number"
                ></v-text-field>
              </v-col>
              <v-col cols="4">
                <v-text-field
                  outlined
                  dense
                  hide-details
                  v-model="payload.email"
                  label="Email"
                ></v-text-field>
              </v-col>
              <v-col cols="4">
                <v-text-field
                  outlined
                  dense
                  hide-details
                  v-model="payload.work_phone"
                  label="Work Phone"
                ></v-text-field>
              </v-col>
              <v-col cols="4">
                <v-text-field
                  outlined
                  dense
                  hide-details
                  v-model="payload.mobile"
                  label="Mobile"
                ></v-text-field>
              </v-col>

              <v-col cols="12">
                <FullAddress @location="handleFullAddress" />
                <!-- <v-text-field
                  outlined
                  dense
                  hide-details
                  v-model="payload.address"
                  label="Address"
                ></v-text-field> -->
              </v-col>

              <v-col cols="12" v-if="errorResponse">
                <span class="red--text">{{ errorResponse }}</span>
              </v-col>
              <v-col cols="12" class="text-right">
                <AssetsButton
                  :options="{
                    label: `Cancel`,
                    color: `red`,
                  }"
                  @click="close"
                />
                &nbsp;
                <AssetsButton
                  :options="{
                    label: `Submit`,
                    color: `green`,
                  }"
                  @click="submit"
                />
              </v-col>
            </v-row>
        </v-card-text>
      </v-card>
    </v-dialog>
  </div>
</template>
<script>
export default {
  props: ["endpoint", "model"],

  data() {
    return {
      payload: {
        title: "",
        first_name: "",
        last_name: "",
        company_name: "",
        vendor_display_name: "----------",
        email: "",
        work_phone: "",
        mobile: "",
        tax_number: "",
        address: "",

        country: "",
        state: "",
        city: "",
        zip_code: "",
      },
      dialog: false,
      loading: false,
      successResponse: null,
      errorResponse: null,
      vendor_categories: [],
    };
  },
  created() {
    this.getVendorCategory();
  },
  methods: {
    handleFullAddress(e) {
      this.payload = {
        ...this.payload,
        ...e,
      };
    },
    close() {
      this.dialog = false;
      this.loading = false;
      this.errorResponse = null;

      this.payload = {
        title: "",
        first_name: "",
        last_name: "",
        company_name: "",
        vendor_display_name: "----------",
        email: "",
        work_phone: "",
        mobile: "",
        tax_number: "",
        address: "",
      };
    },
    async getVendorCategory() {
      let { data } = await this.$axios.get(`vendor-category-list`);

      this.vendor_categories = data;
    },
    async submit() {
      this.loading = true;
      try {
        this.payload.company_id = this.$auth.user.company_id;
        await this.$axios.post(this.endpoint, this.payload);
        this.close();
        this.$emit("response", "payload has been inserted");
      } catch (error) {
        this.errorResponse = error?.response?.data?.message || "Unknown error";
        this.loading = false;
      }
    },
  },
};
</script>
