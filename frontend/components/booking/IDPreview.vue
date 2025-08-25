<template>
  <v-dialog v-model="dialog" width="900">
    <AssetsIconClose left="890" @click="close" />
    <template v-slot:activator="{ on, attrs }">
      <v-btn block dark x-small color="blue" v-bind="attrs" v-on="on">
        ID <v-icon x-small class="ml-1">mdi-camera-outline</v-icon>
      </v-btn>
    </template>

    <v-card>
      <v-alert dense flat class="grey lighten-3"> Picture and ID </v-alert>

      <v-card-text>
        <div
          style="
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 15px;
          "
          :style="`${validCode ? '' : 'min-height:30vh'}`"
        >
          <div>
            <v-text-field
              v-model="code"
              outlined
              dense
              label="Enter Code"
              hide-details
            ></v-text-field>
          </div>
          <v-btn
            small
            :loading="reloadLoading"
            class="primary"
            @click="getData"
          >
            <v-icon>mdi-reload</v-icon>
          </v-btn>
        </div>
        <v-container v-if="validCode">
          <v-row>
            <v-col cols="6">
              <v-img :src="customer.captured_photo"></v-img>
            </v-col>
            <v-col cols="6">
              <v-card outlined style="min-height: 400px">
                <v-img :src="customer.sign"></v-img>
              </v-card>
            </v-col>
            <v-col cols="6">
              <v-img :src="customer.id_frontend_side"></v-img>
            </v-col>
            <v-col cols="6">
              <v-img :src="customer.id_backend_side"></v-img>
            </v-col>
          </v-row>
          <v-row>
            <v-col cols="12">
              <v-btn
                :loading="confirmLoading"
                class="primary"
                block
                @click="confirm"
              >
                Confirm
              </v-btn>
            </v-col>
          </v-row>
        </v-container>
      </v-card-text>
    </v-card>
  </v-dialog>
</template>
<script>
export default {
  data() {
    return {
      validCode: false,
      reloadLoading: false,
      confirmLoading: false,
      endpoint: process.env.BACKEND_URL,
      //   endpoint: "https://hms-backend.test/api",

      dialog: false,
      customer: null,
      url: null,
      code: null,
    };
  },
  async created() {},
  computed: {
    isValid() {
      let customer = this.customer;
      if (!customer) {
        return false;
      }
      return (
        customer.captured_photo &&
        customer.sign &&
        customer.id_frontend_side &&
        customer.id_backend_side
      );
    },
  },
  methods: {
    async getData() {
      this.reloadLoading = true;

      try {
        this.url = `${this.endpoint}/get-verify-info/${this.code}`;

        let { data } = await this.$axios.get(this.url);

        if (data && data.id) {
          this.customer = {
            captured_photo: data.captured_photo_url,
            sign: data.sign_url,
            id_frontend_side: data.id_frontend_side_url,
            id_backend_side: data.id_backend_side_url,
          };
          this.reloadLoading = false;
          this.validCode = true;
          return;
        }

        alert("Record not found");
        this.reloadLoading = false;
        this.customer = {
          captured_photo: null,
          sign: null,
          id_frontend_side: null,
          id_backend_side: null,
        };
        this.reloadLoading = false;
        this.validCode = false;
      } catch (error) {
        alert("Record not found");
        this.customer = {
          captured_photo: null,
          sign: null,
          id_frontend_side: null,
          id_backend_side: null,
        };
        this.reloadLoading = false;
        this.validCode = false;
        console.log(error);
      }
    },
    async confirm() {
      console.log("🚀 ~ confirm ~ this.customer:", this.customer);
      this.$emit(`getCustomerDocs`, this.customer);
      this.confirmLoading = false;
      this.dialog = false;
    },
    close() {
      this.customer = {
        captured_photo: null,
        sign: null,
        id_frontend_side: null,
        id_backend_side: null,
      };
      this.reloadLoading = false;
      this.validCode = false;

      this.$emit(`getCustomerDocs`, null);
      this.confirmLoading = false;
      this.dialog = false;
    },
  },
};
</script>
