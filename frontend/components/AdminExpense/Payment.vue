<template>
  <v-dialog v-model="dialog" width="700">
    <template v-slot:activator="{ on, attrs }">
      <div v-bind="attrs" v-on="on">
        <v-icon color="blue" small> mdi-cash </v-icon>
        Payment
      </div>
    </template>

    <v-card>
      <v-toolbar flat class="blue white--text" dense>
        Payment <v-spacer></v-spacer
        ><v-icon @click="close" color="white">mdi-close</v-icon></v-toolbar
      >

      <v-card-text class="py-5">
        <v-container>
          <v-row>
            <v-col cols="12">
              <v-autocomplete
                outlined
                dense
                hide-details
                item-value="id"
                item-text="name"
                v-model="payload.vendor_id"
                :items="[
                  { name: `Vendor 1`, id: 1 },
                  { name: `Vendor 2`, id: 2 },
                  { name: `Vendor 3`, id: 3 },
                ]"
                placeholder="Vendors"
              ></v-autocomplete>
            </v-col>
            <v-col cols="6">
              <v-text-field
                outlined
                dense
                hide-details
                v-model="payload.payment_number"
                label="Payment #"
              ></v-text-field>
            </v-col>

            <v-col cols="6">
              <v-menu
                v-model="menu2"
                :close-on-content-click="false"
                transition="scale-transition"
                offset-y
                max-width="290px"
                min-width="auto"
              >
                <template v-slot:activator="{ on, attrs }">
                  <v-text-field
                    outlined
                    dense
                    hide-details
                    v-model="payload.payment_date"
                    label="Payment Date"
                    persistent-hint
                    readonly
                    v-bind="attrs"
                    v-on="on"
                  ></v-text-field>
                </template>
                <v-date-picker
                  v-model="payload.payment_date"
                  no-title
                  @input="menu2 = false"
                ></v-date-picker>
              </v-menu>
            </v-col>

            <v-col cols="6">
              <v-autocomplete
                outlined
                dense
                hide-details
                item-value="id"
                item-text="name"
                v-model="payload.payment_mode"
                :items="[`Cash`, `Card`, `Online`, `Bank`, `UPI`, `Cheque`]"
                placeholder="Payment Mode"
              ></v-autocomplete>
            </v-col>

            <v-col cols="6">
              <v-text-field
                outlined
                dense
                hide-details
                v-model="payload.payment_mode_ref"
                label="Payment Mode Ref #"
              ></v-text-field>
            </v-col>

            <v-col cols="12">
              <v-textarea
                rows="3"
                outlined
                dense
                hide-details
                v-model="payload.note"
                label="Note #"
              ></v-textarea>
            </v-col>

            <v-col cols="12" v-if="errorResponse">
              <span class="red--text">{{ errorResponse }}</span>
            </v-col>
            <v-col cols="12">
              <span class="primary--text">
                <UploadMultipleAttachments
                  @files-selected="handleMultipleFileSelection($event)"
                />
              </span>
            </v-col>
            <v-col cols="12" class="text-right">
              <v-btn small color="grey" class="white--text" dark @click="close">
                Close
              </v-btn>
              <v-btn
                :loading="loading"
                small
                color="blue"
                class="white--text"
                dark
                @click="submit"
              >
                Submit
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
  props: ["id", "endpoint"],
  data() {
    return {
      menu2: false,
      payload: {
        vendor_id: 1,
        payment_number: "234234",
        payment_date: new Date(
          Date.now() - new Date().getTimezoneOffset() * 60000
        )
          .toISOString()
          .substr(0, 10),
        payment_mode: `Cash`,
        payment_mode_ref: ``,
        attachments: [],
      },
      dialog: false,
      loading: false,
      successResponse: null,
      errorResponse: null,
    };
  },
  created() {},

  methods: {
    close() {
      this.dialog = false;
      this.loading = false;
      this.errorResponse = null;
    },
    handleMultipleFileSelection(e) {
      e.forEach((v, i) => {
        const attachmentExists = this.payload.attachments.some(
          (att) => att.name === v.name
        );
        if (!attachmentExists) {
          this.payload.attachments.push({
            name: v.name,
            attachment: v.preview,
          });
        }
      });
    },
    async submit() {
      this.loading = true;
      this.payload.admin_expense_id = this.id;
      try {
        await this.$axios.post(`${this.endpoint}`, this.payload);
        this.close();
        this.$emit("response", "Record has been inserted");
      } catch (error) {
        this.errorResponse = error?.response?.data?.message || "Unknown error";
        this.loading = false;
      }
    },
  },
};
</script>
