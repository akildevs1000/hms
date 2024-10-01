<template>
  <v-dialog v-model="dialog" width="700">
    <template v-slot:activator="{ on, attrs }">
      <div v-bind="attrs" v-on="on">
        <v-icon color="blue" small> mdi-pencil </v-icon>
        <span v-if="model">Edit</span>
      </div>
    </template>

    <v-card>
      <v-toolbar flat class="grey lighten-3" dense>
        <span>Edit {{ model }}</span> <v-spacer></v-spacer>
        <AssetsButtonClose @close="close"
      /></v-toolbar>

      <v-card-text class="py-5">
        <v-container>
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
              <v-text-field
                outlined
                dense
                hide-details
                v-model="payload.company_name"
                label="Company Name"
              ></v-text-field>
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
                v-model="payload.vendor_display_name"
                label="Vendor Display Name"
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
                  color: `red`,
                  label: `cancel`,
                }"
                @click="close"
              />
              <AssetsButton
                :options="{
                  color: `green`,
                  label: `Submit`,
                }"
                @click="submit"
              />
            </v-col>
          </v-row>
        </v-container>
      </v-card-text>
    </v-card>
  </v-dialog>
</template>
<script>
export default {
  props: ["item", "endpoint", "model"],
  data() {
    return {
      payload: {
        name: "",
        contact_person_name: "",
        number: "",
        email: "",
        location: "",
      },
      dialog: false,
      loading: false,
      successResponse: null,
      errorResponse: null,
      vendor_categories: [],
    };
  },
  created() {
    this.payload = this.item;
    this.getVendorCategory();
  },
  methods: {
    handleFullAddress(e) {
      this.payload = {
        ...this.payload,
        ...e,
      };
    },
    async getVendorCategory() {
      let { data } = await this.$axios.get(`vendor-category-list`);

      this.vendor_categories = data;
    },
    close() {
      this.dialog = false;
      this.loading = false;
      this.errorResponse = null;
    },
    async submit() {
      this.loading = true;
      try {
        await this.$axios.put(
          `${this.endpoint}/${this.payload.id}`,
          this.payload
        );
        this.close();
        this.$emit("response", "Record has been inserted");
        this.$emit("updated_vendor", this.payload);
      } catch (error) {
        this.errorResponse = error?.response?.data?.message || "Unknown error";
        this.loading = false;
      }
    },
  },
};
</script>
