<template>
  <div v-if="can('company_access')">
    <div v-if="!preloader">
      <div class="text-center ma-2">
        <v-snackbar
          v-model="snackbar"
          top="top"
          color="secondary"
          elevation="24"
        >
          {{ response }}
        </v-snackbar>
      </div>
      <v-row>
        <v-col>
          <v-card>
            <v-tabs class="pt-3" color="primary">
              <v-tab> Profile </v-tab>
              <v-tab> License </v-tab>
              <v-tab> Contact </v-tab>
              <v-tab> Document </v-tab>
              <v-tab> Password </v-tab>
              <v-tab> Currency </v-tab>

              <v-tab> Tax Slabs </v-tab>

              <v-tab> Whatsapp </v-tab>

              <v-tab> Night Audit Email </v-tab>
              <v-tab> Verfication </v-tab>

              <v-tab-item>
                <v-card flat>
                  <v-card-text>
                    <v-row>
                      <v-col cols="4">
                        <v-text-field
                          readonly
                          label="Company Code"
                          dense
                          outlined
                          hide-details
                          v-model="company_payload.company_code"
                        >
                        </v-text-field>
                      </v-col>
                      <v-col cols="4">
                        <v-text-field
                          label="Company Name"
                          dense
                          outlined
                          hide-details
                          v-model="company_payload.name"
                        >
                        </v-text-field>
                        <span
                          v-if="errors && errors.name"
                          class="error--text mt-2"
                          >{{ errors.name[0] }}</span
                        >
                      </v-col>
                      <v-col cols="4">
                        <v-text-field
                          label="Company Email"
                          dense
                          outlined
                          hide-details
                          v-model="user_payload.email"
                        >
                        </v-text-field>
                        <span
                          v-if="errors && errors.email"
                          class="error--text mt-2"
                          >{{ errors.email[0] }}</span
                        >
                      </v-col>

                      <v-col cols="4">
                        <v-text-field
                          label="Mol ID"
                          dense
                          outlined
                          hide-details
                          v-model="company_payload.mol_id"
                        >
                        </v-text-field>
                        <span
                          v-if="errors && errors.mol_id"
                          class="error--text mt-2"
                          >{{ errors.mol_id[0] }}</span
                        >
                      </v-col>

                      <v-col cols="4">
                        <v-text-field
                          label="P.O Box"
                          dense
                          outlined
                          hide-details
                          v-model="company_payload.p_o_box_no"
                        >
                        </v-text-field>
                        <span
                          v-if="errors && errors.p_o_box_no"
                          class="error--text mt-2"
                          >{{ errors.p_o_box_no[0] }}</span
                        >
                      </v-col>

                      <v-col cols="4">
                        <v-text-field
                          label="Currency"
                          dense
                          outlined
                          hide-details
                          v-model="company_payload.currency"
                        >
                        </v-text-field>
                        <span
                          v-if="errors && errors.currency"
                          class="error--text mt-2"
                          >{{ errors.currency[0] }}</span
                        >
                      </v-col>

                      <v-col cols="4">
                        <v-text-field
                          readonly
                          label="Member From"
                          dense
                          outlined
                          hide-details
                          v-model="company_payload.member_from"
                        >
                        </v-text-field>
                      </v-col>

                      <v-col cols="4">
                        <v-text-field
                          readonly
                          label="Expiry Date"
                          dense
                          outlined
                          hide-details
                          v-model="company_payload.expiry"
                        >
                        </v-text-field>
                      </v-col>
                      <v-col cols="4"></v-col>
                      <v-col cols="4">
                        <v-text-field
                          readonly
                          label="Max Branches"
                          dense
                          outlined
                          hide-details
                          v-model="company_payload.max_branches"
                        >
                        </v-text-field>
                      </v-col>

                      <v-col cols="4">
                        <v-text-field
                          readonly
                          label="Max customers"
                          dense
                          outlined
                          hide-details
                          v-model="company_payload.max_employee"
                        >
                        </v-text-field>
                      </v-col>

                      <v-col cols="4">
                        <v-text-field
                          readonly
                          label="Max Devices"
                          dense
                          outlined
                          hide-details
                          v-model="company_payload.max_devices"
                        >
                        </v-text-field>
                      </v-col>
                      <v-col cols="6">
                        <v-card max-width="300" class="pa-1" outlined>
                          <div class="text-center">
                            <v-avatar tile size="250">
                              <img
                                @click="onpick_attachment"
                                :src="
                                  previewImage ||
                                  company_payload.logo ||
                                  '/no-profile-image.jpg'
                                "
                              />
                            </v-avatar>
                          </div>
                          <br />
                          <v-btn
                            color="primary"
                            block
                            @click="onpick_attachment"
                            >{{
                              !upload.name ? "Upload Logo" : "Logo Uploaded"
                            }}
                            <v-icon right dark>mdi-cloud-upload</v-icon>
                          </v-btn>
                        </v-card>

                        <input
                          required
                          type="file"
                          @change="attachment"
                          style="display: none"
                          accept="image/*"
                          ref="attachment_input"
                        />

                        <span
                          v-if="errors && errors.logo"
                          class="error--text mt-2"
                          >{{ errors.logo[0] }}</span
                        >
                      </v-col>

                      <v-col cols="12">
                        <div class="text-right">
                          <v-btn
                            v-if="can('company_edit')"
                            small
                            :loading="loading"
                            color="primary"
                            @click="update_company"
                          >
                            Submit
                          </v-btn>
                        </div>
                      </v-col>
                    </v-row>
                  </v-card-text>
                </v-card>
              </v-tab-item>

              <v-tab-item>
                <v-card flat>
                  <v-card-text>
                    <v-row>
                      <v-col cols="6">
                        <v-text-field
                          label="License"
                          dense
                          outlined
                          hide-details
                          v-model="company_trade_license.license_no"
                        >
                        </v-text-field>
                        <span
                          v-if="errors && errors.license_no"
                          class="error--text mt-2"
                          >{{ errors.license_no[0] }}</span
                        >
                      </v-col>
                      <v-col cols="6">
                        <v-select
                          :items="[
                            {
                              id: `commercial_licenses`,
                              text: `Commercial licenses`,
                            },
                            {
                              id: `industrial_license`,
                              text: `Industrial License`,
                            },
                            {
                              id: `professional_license`,
                              text: `Professional license`,
                            },
                          ]"
                          label="License Type"
                          dense
                          outlined
                          hide-details
                          v-model="company_trade_license.license_type"
                        >
                        </v-select>
                        <span
                          v-if="errors && errors.license_type"
                          class="error--text mt-2"
                          >{{ errors.license_type[0] }}</span
                        >
                      </v-col>

                      <v-col cols="6">
                        <v-text-field
                          label="Emirate"
                          dense
                          outlined
                          hide-details
                          v-model="company_trade_license.emirate"
                        >
                        </v-text-field>
                        <span
                          v-if="errors && errors.emirate"
                          class="error--text mt-2"
                          >{{ errors.emirate[0] }}</span
                        >
                      </v-col>

                      <v-col cols="6">
                        <v-text-field
                          label="Manager"
                          dense
                          outlined
                          hide-details
                          v-model="company_trade_license.manager"
                        >
                        </v-text-field>
                        <span
                          v-if="errors && errors.manager"
                          class="error--text mt-2"
                          >{{ errors.manager[0] }}</span
                        >
                      </v-col>

                      <v-col cols="6">
                        <v-menu
                          ref="issue_date_menu_ref"
                          v-model="issue_date_menu"
                          :close-on-content-click="false"
                          :nudge-right="40"
                          transition="scale-transition"
                          offset-y
                          min-width="290px"
                        >
                          <template v-slot:activator="{ on, attrs }">
                            <v-text-field
                              hide-details
                              dense
                              outlined
                              v-model="company_trade_license.issue_date"
                              label="Issue Date"
                              append-icon="mdi-calendar"
                              readonly
                              v-bind="attrs"
                              v-on="on"
                            ></v-text-field>
                          </template>
                          <v-date-picker
                            no-title
                            v-model="company_trade_license.issue_date"
                            @input="issue_date_menu = false"
                          ></v-date-picker>
                        </v-menu>
                        <span
                          v-if="errors && errors.issue_date"
                          class="error--text mt-2"
                          >{{ errors.issue_date[0] }}</span
                        >
                      </v-col>

                      <v-col cols="6">
                        <v-menu
                          ref="expiry_date_ref"
                          v-model="expiry_date_menu"
                          :close-on-content-click="false"
                          :nudge-right="40"
                          transition="scale-transition"
                          offset-y
                          min-width="290px"
                        >
                          <template v-slot:activator="{ on, attrs }">
                            <v-text-field
                              hide-details
                              dense
                              outlined
                              v-model="company_trade_license.expiry_date"
                              label="Expiry Date"
                              append-icon="mdi-calendar"
                              readonly
                              v-bind="attrs"
                              v-on="on"
                            ></v-text-field>
                          </template>
                          <v-date-picker
                            no-title
                            v-model="company_trade_license.expiry_date"
                            @input="expiry_date_menu = false"
                          ></v-date-picker>
                        </v-menu>
                        <span
                          v-if="errors && errors.expiry_date"
                          class="error--text mt-2"
                          >{{ errors.expiry_date[0] }}</span
                        >
                      </v-col>

                      <v-col cols="6">
                        <v-text-field
                          label="Makeem No"
                          dense
                          outlined
                          hide-details
                          v-model="company_trade_license.makeem_no"
                        >
                        </v-text-field>
                        <span
                          v-if="errors && errors.makeem_no"
                          class="error--text mt-2"
                          >{{ errors.makeem_no[0] }}</span
                        >
                      </v-col>
                      <v-col cols="12">
                        <div class="text-right">
                          <v-btn
                            v-if="can('company_edit')"
                            small
                            :loading="loading"
                            color="primary"
                            @click="update_license"
                          >
                            Submit
                          </v-btn>
                        </div>
                      </v-col>
                    </v-row>
                  </v-card-text>
                </v-card>
              </v-tab-item>

              <v-tab-item>
                <v-card flat>
                  <v-card-text>
                    <v-row>
                      <v-col cols="6">
                        <v-text-field
                          label="Contact Person Name"
                          dense
                          outlined
                          hide-details
                          v-model="contact_payload.name"
                        >
                        </v-text-field>
                        <span
                          v-if="errors && errors.name"
                          class="error--text mt-2"
                          >{{ errors.name[0] }}</span
                        >
                      </v-col>

                      <v-col cols="6">
                        <v-text-field
                          label="Contact Person Number"
                          dense
                          outlined
                          hide-details
                          v-model="contact_payload.number"
                        >
                        </v-text-field>
                        <span
                          v-if="errors && errors.number"
                          class="error--text mt-2"
                          >{{ errors.number[0] }}</span
                        >
                      </v-col>

                      <v-col cols="6">
                        <v-text-field
                          label="Contact Person Position"
                          dense
                          outlined
                          hide-details
                          v-model="contact_payload.position"
                        >
                        </v-text-field>
                        <span
                          v-if="errors && errors.position"
                          class="error--text mt-2"
                          >{{ errors.position[0] }}</span
                        >
                      </v-col>

                      <v-col cols="6">
                        <v-text-field
                          label="Contact Person Whatsapp"
                          dense
                          outlined
                          hide-details
                          v-model="contact_payload.whatsapp"
                        >
                        </v-text-field>
                        <span
                          v-if="errors && errors.whatsapp"
                          class="error--text mt-2"
                          >{{ errors.whatsapp[0] }}</span
                        >
                      </v-col>
                      <v-col cols="12">
                        <div class="text-right">
                          <v-btn
                            v-if="can('company_edit')"
                            small
                            :loading="loading"
                            color="primary"
                            @click="update_contact"
                          >
                            Submit
                          </v-btn>
                        </div>
                      </v-col>
                    </v-row>
                  </v-card-text>
                </v-card>
              </v-tab-item>

              <v-tab-item>
                <CompanyDocument />
              </v-tab-item>

              <!-- <v-tab-item>
                <v-card flat>
                  <v-card-text>
                    <v-row>
                      <v-col cols="6">
                      <v-text-field
                        label="Lat"
                        dense
                        outlined
                        hide-details
                        v-model="geographic_payload.lat"
                      >
                      </v-text-field>
                      <span
                        v-if="errors && errors.lat"
                        class="error--text mt-2"
                        >{{ errors.lat[0] }}</span
                      >
                    </v-col>
                    <v-col cols="6">
                      <v-text-field
                        label="Lon"
                        dense
                        outlined
                        hide-details
                        v-model="geographic_payload.lon"
                      >
                      </v-text-field>
                      <span
                        v-if="errors && errors.lon"
                        class="error--text mt-2"
                        >{{ errors.lon[0] }}</span
                      >
                    </v-col>
                    <v-col cols="12">
                      <v-textarea
                        rows="3"
                        label="Location"
                        dense
                        outlined
                        hide-details
                        v-model="geographic_payload.location"
                      >
                      </v-textarea>
                      <span
                        v-if="errors && errors.location"
                        class="error--text mt-2"
                        >{{ errors.location[0] }}</span
                      >
                    </v-col>
                    <v-col cols="12">
                      <div class="text-right">
                        <v-btn
                          v-if="can('company_edit')"
                          small
                          :loading="loading"
                          color="primary"
                          @click="update_geographic"
                        >
                          Submit
                        </v-btn>
                      </div>
                    </v-col>
                    </v-row>
                  </v-card-text>
                </v-card>
              </v-tab-item> -->

              <v-tab-item>
                <v-card flat>
                  <v-card-text>
                    <v-row>
                      <v-col cols="6">
                        <v-text-field
                          label="Password"
                          outlined
                          dense
                          hide-details
                          type="password"
                          v-model="user_payload.password"
                        >
                        </v-text-field>
                        <span
                          v-if="errors && errors.password"
                          class="error--text mt-2"
                          >{{ errors.password[0] }}</span
                        >
                      </v-col>
                      <v-col cols="6">
                        <v-text-field
                          label="Confirm Password"
                          outlined
                          dense
                          hide-details
                          type="password"
                          v-model="user_payload.password_confirmation"
                        >
                        </v-text-field>
                        <span
                          v-if="errors && errors.password_confirmation"
                          class="error--text mt-2"
                          >{{ errors.password_confirmation[0] }}</span
                        >
                      </v-col>
                    </v-row>
                    <v-row>
                      <v-col cols="12">
                        <div class="text-right">
                          <v-btn
                            v-if="can('company_edit')"
                            small
                            :loading="loading"
                            color="primary"
                            @click="update_user"
                          >
                            Submit
                          </v-btn>
                        </div>
                      </v-col>
                    </v-row>
                  </v-card-text>
                </v-card>
              </v-tab-item>

              <v-tab-item>
                <v-card flat>
                  <v-card-text>
                    <v-row>
                      <v-col cols="4">
                        <v-row>
                          <v-col cols="12">
                            <v-select
                              hide-details
                              outlined
                              dense
                              small
                              v-model="company_payload.currency"
                              :items="[
                                { text: 'INR ₹', value: '₹' },
                                { text: 'US $', value: '$' },
                              ]"
                            >
                            </v-select>

                            <span
                              v-if="errors && errors.currency"
                              class="error--text mt-2"
                              >{{ errors.p_o_box_no[0] }}</span
                            >
                          </v-col>
                          <v-col cols="12">
                            <v-btn
                              v-if="can('company_edit')"
                              small
                              :loading="loading"
                              color="primary"
                              @click="update_currency"
                            >
                              Submit
                            </v-btn>
                          </v-col>
                        </v-row>
                      </v-col>
                    </v-row>
                  </v-card-text>
                </v-card>
              </v-tab-item>

              <v-tab-item>
                <TaxSlabs />
              </v-tab-item>
              <v-tab-item>
                <WhatsappInstance />
              </v-tab-item>
              <v-tab-item>
                <EmailNotifications />
              </v-tab-item>
              <v-tab-item>
                <v-card flat>
                  <v-card-text>
                    <v-avatar tile size="300">
                      <v-img :src="qrCodeImage"></v-img>
                    </v-avatar>
                  </v-card-text>
                </v-card>
              </v-tab-item>
            </v-tabs>
          </v-card>
        </v-col>
      </v-row>
    </div>
    <Preloader v-else />
  </div>
  <NoAccess v-else />
</template>

<script>
export default {
  data: () => ({
    issue_date_menu_ref: false,
    expiry_date_menu_ref: false,

    issue_date_menu: false,
    expiry_date_menu: false,

    vertical: false,
    id: "",
    loading: false,
    preloader: true,
    upload: {
      name: "",
    },

    company_payload: {
      name: "",
      logo: "",
      member_from: "",
      expiry: "",
      max_branches: "",
      max_employee: "",
      max_devices: "",
      mol_id: "",
      p_o_box_no: "",
    },

    company_trade_license: {
      license_no: "",
      license_type: "",
      emirate: "",
      makeem_no: "",
      manager: "",
      issue_date: "",
      expiry_date: "",
    },

    contact_payload: {
      name: "",
      number: "",
      position: "",
      whatsapp: "",
    },
    user_payload: {
      password: "",
      password_confirmation: "",
    },
    geographic_payload: {
      lat: "",
      lon: "",
      location: "",
    },
    e1: 1,
    errors: [],
    previewImage: null,
    data: {},
    response: "",
    snackbar: false,
    qrCodeImage: null,
  }),
  async created() {
    this.getDataFromApi();
    this.generateQRCode(
      `https://verify.myhotel2cloud.com/` + this.$auth.user.company_id,
      300
    );
  },
  methods: {
    async generateQRCode(url, width) {
      console.log(url);

      try {
        this.qrCodeImage = await this.$qrcode.generate(url, {
          width,
        });
        if (this.qrCodeImage) return this.qrCodeImage;
      } catch (error) {
        console.error("Error generating QR code:", error);
      }
    },
    update_currency() {
      this.$axios
        .post(`/company/${this.id}/update-currency`, {
          currency: this.company_payload.currency,
        })
        .then(({ data }) => {
          this.loading = false;

          if (!data.status) {
            this.errors = data.errors;
          }

          this.snackbar = true;
          this.response = data.message;
        })
        .catch((e) => console.log(e));
    },
    can(per) {
      let u = this.$auth.user;
      return (
        (u && u.permissions.some((e) => e == per || per == "/")) || u.is_master
      );
    },
    getDataFromApi() {
      this.id = this.$auth.user?.company?.id; //this.$route.params.id;
      this.$axios.get(`company/${this.id}`).then(({ data }) => {
        let r = data.record;
        this.company_payload = r;
        this.contact_payload = r.contact
          ? r.contact
          : { name: "", number: "", position: "", whatsapp: "" };
        this.user_payload = r.user;

        if (r.trade_license) {
          this.company_trade_license = r.trade_license;
        }

        let mf = this.formatted_date(r.member_from);
        let exp = this.formatted_date(r.expiry);
        this.company_payload.member_from = mf;
        this.company_payload.expiry = exp;
        this.company_payload.currency = r.currency;

        this.geographic_payload = {
          lat: this.company_payload.lat,
          lon: this.company_payload.lon,
          location: this.company_payload.location,
        };
        this.preloader = false;
      });
    },

    formatted_date(v) {
      let [year, month, date] = v.split("/");
      return `${year}-${month}-${date}`;
    },
    onpick_attachment() {
      this.$refs.attachment_input.click();
    },

    attachment(e) {
      this.upload.name = e.target.files[0] || "";

      let input = this.$refs.attachment_input;
      let file = input.files;
      if (file && file[0]) {
        let reader = new FileReader();
        reader.onload = (e) => {
          this.previewImage = e.target.result;
        };
        reader.readAsDataURL(file[0]);
        this.$emit("input", file[0]);
      }
    },

    update_company() {
      let payload = new FormData();

      payload.append("name", this.company_payload.name);
      if (this.upload.name) {
        payload.append("logo", this.upload.name);
      }
      payload.append("location", this.company_payload.location);
      payload.append("member_from", this.company_payload.member_from);
      payload.append("expiry", this.company_payload.expiry);
      payload.append("max_employee", this.company_payload.max_employee);
      payload.append("max_branches", this.company_payload.max_branches);
      payload.append("max_devices", this.company_payload.max_devices);

      payload.append("mol_id", this.company_payload.mol_id);
      payload.append("p_o_box_no", this.company_payload.p_o_box_no);
      payload.append("currency", this.company_payload.currency);

      this.start_process(`/company/${this.id}/update`, payload, `Company`);
    },
    update_contact() {
      this.start_process(
        `/company/${this.id}/update/contact`,
        this.contact_payload,
        `Contact`
      );
    },

    update_license() {
      this.start_process(
        `/company/${this.id}/trade-license`,
        this.company_trade_license,
        `Trade License`
      );
    },
    update_geographic() {
      this.start_process(
        `/company/${this.id}/update/geographic`,
        this.geographic_payload,
        `Geographic Info`
      );
    },
    update_user() {
      this.start_process(
        `/company/${this.id}/update/user`,
        this.user_payload,
        `User`
      );
    },
    start_process(url, payload, model) {
      this.loading = true;

      this.$axios
        .post(url, payload)
        .then(({ data }) => {
          this.loading = false;

          if (!data.status) {
            this.errors = data.errors;
          } else {
            this.snackbar = true;
            this.response = model + " updated successfully";
          }
        })
        .catch((e) => console.log(e));
    },
  },
};
</script>
