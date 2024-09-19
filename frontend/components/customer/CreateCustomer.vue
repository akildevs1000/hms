<template>
  <div>
    <v-dialog v-model="imgView" max-width="80%">
      <v-card>
        <v-toolbar class="rounded-md" color="background" dense flat dark>
          <span>Preview</span>
          <v-spacer></v-spacer>
          <v-icon dark class="pa-0" @click="imgView = false"
            >mdi-close</v-icon
          >
        </v-toolbar>
        <v-container>
          <ImagePreview :docObj="documentObj"></ImagePreview>
        </v-container>
        <v-card-actions> </v-card-actions>
      </v-card>
    </v-dialog>
    <v-dialog v-model="documentDialog" max-width="30%">
      <v-card>
        <v-toolbar class="rounded-md" color="background" dense flat dark>
          <span>Add Document</span>
          <v-spacer></v-spacer>
          <v-icon dark class="pa-0" @click="documentDialog = false"
            >mdi-close</v-icon
          >
        </v-toolbar>
        <v-container class="pa-5">
          <v-row>
            <v-col md="12" sm="12" cols="12" dense>
              <v-select
                v-model="customer.id_card_type_id"
                :items="idCards"
                dense
                label="ID Card Type"
                outlined
                item-text="name"
                item-value="id"
                :hide-details="errors && !errors.id_card_type_id"
                :error="errors && errors.id_card_type_id"
                :error-messages="
                  errors && errors.id_card_type_id
                    ? errors.id_card_type_id[0]
                    : ''
                "
              ></v-select>
            </v-col>
            <v-col md="12" cols="12" sm="12">
              <v-text-field
                dense
                label="ID Card"
                outlined
                type="text"
                v-model="customer.id_card_no"
                :hide-details="errors && !errors.id_card_no"
                :error="errors && errors.id_card_no"
                :error-messages="
                  errors && errors.id_card_no ? errors.id_card_no[0] : ''
                "
              ></v-text-field>
            </v-col>
            <v-col md="12" cols="12" sm="12">
              <v-menu
                v-model="customer.passport_expiration_menu"
                :close-on-content-click="false"
                :nudge-right="40"
                transition="scale-transition"
                offset-y
                min-width="auto"
              >
                <template v-slot:activator="{ on, attrs }">
                  <v-text-field
                    v-model="customer.passport_expiration"
                    label="Expired"
                    v-on="on"
                    v-bind="attrs"
                    dense
                    outlined
                    :hide-details="errors && !errors.passport_expiration"
                    :error="errors && errors.passport_expiration"
                    :error-messages="
                      errors && errors.passport_expiration
                        ? errors.passport_expiration[0]
                        : ''
                    "
                  ></v-text-field>
                </template>
                <v-date-picker
                  v-model="customer.passport_expiration"
                  @input="customer.passport_expiration_menu = false"
                ></v-date-picker>
              </v-menu>
            </v-col>
            <v-col md="12">
              <v-file-input
                v-model="customer.document"
                color="primary"
                counter
                placeholder="Select your files"
                outlined
                :show-size="1000"
              >
                <template v-slot:selection="{ index, text }">
                  <v-chip v-if="index < 2" color="primary" dark label small>
                    {{ text }}
                  </v-chip>

                  <span
                    v-else-if="index === 2"
                    class="text-overline grey--text text--darken-3 mx-2"
                  >
                    +{{ customer.document.length - 2 }} File(s)
                  </span>
                </template>
              </v-file-input>
              <small class="red--text" v-if="errors && errors.document">
                {{ errors && errors.document ? errors.document[0] : "" }}
              </small>
            </v-col>
            <v-divider></v-divider>
            <v-col md="12">
              <v-btn
                small
                dark
                class="primary pt-4 pb-4"
                @click="document_validate"
              >
                Submit
                <v-icon right dark>mdi-file</v-icon>
              </v-btn>
            </v-col>
          </v-row>
        </v-container>
        <v-card-actions> </v-card-actions>
      </v-card>
    </v-dialog>
    <v-row class="m-0 p-0 mt-0">
      <v-col md="12">
        <v-card flat>
          <v-card-text>
            <v-row>
              <v-col md="2" cols="12">
                <v-img
                  @click="onpick_attachment"
                  style="
                    width: 150px;
                    height: 150px;
                    margin: 0 auto;
                    border-radius: 50%;
                  "
                  :src="showImage"
                ></v-img>
                <input
                  required
                  type="file"
                  @change="attachment"
                  style="display: none"
                  accept="image/*"
                  ref="attachment_input"
                />
                <span v-if="errors && errors.image" class="red--text mt-2">
                  {{ errors.image[0] }}</span
                >
                <div class="mt-2 ml-4" v-if="customer.document">
                  <v-btn
                    small
                    dark
                    class="primary lg-pt-4 lg-pb-4 doc-btn"
                    @click="preview()"
                  >
                    Preview
                    <v-icon right dark>mdi-file</v-icon>
                  </v-btn>
                </div>
                <div class="mt-2 ml-2" v-else>
                  <v-btn
                    small
                    dark
                    class="primary pt-4 pb-4"
                    @click="add_document()"
                  >
                    <small>Document</small>
                    <v-icon right dark>mdi-plus</v-icon>
                  </v-btn>
                </div>
              </v-col>
              <v-col md="10" cols="12">
                <v-row>
                  <!-- <v-col md="5" dense>
                        <v-text-field
                          label="Type"
                          v-model="BookingData.customer_type"
                          dense
                          outlined
                          disabled
                          :hide-details="true"
                        ></v-text-field>
                      </v-col> -->
                  <v-col md="2" cols="12" sm="12">
                    <v-select
                      v-model="customer.title"
                      :items="titleItems"
                      label="Tittle *"
                      dense
                      item-text="name"
                      item-value="name"
                      :hide-details="errors && !errors.title"
                      :error="errors && errors.title"
                      :error-messages="
                        errors && errors.title ? errors.title[0] : ''
                      "
                      outlined
                    ></v-select>
                  </v-col>
                  <v-col md="5" cols="12" sm="12">
                    <v-text-field
                      label="First Name *"
                      dense
                      outlined
                      type="text"
                      v-model="customer.first_name"
                      :hide-details="errors && !errors.first_name"
                      :error="errors && errors.first_name"
                      :error-messages="
                        errors && errors.first_name ? errors.first_name[0] : ''
                      "
                    ></v-text-field>
                  </v-col>
                  <v-col md="5" cols="12" sm="12">
                    <v-text-field
                      label="Last Name"
                      dense
                      :hide-details="true"
                      outlined
                      type="text"
                      v-model="customer.last_name"
                    ></v-text-field>
                  </v-col>
                  <v-col md="6" cols="12" sm="12">
                    <v-text-field
                      dense
                      label="Contact No *"
                      outlined
                      type="number"
                      v-model="customer.contact_no"
                      :hide-details="errors && !errors.contact_no"
                      :error="errors && errors.contact_no"
                      :error-messages="
                        errors && errors.contact_no ? errors.contact_no[0] : ''
                      "
                      @keyup="mergeContact"
                    ></v-text-field>
                  </v-col>
                  <v-col md="6" cols="12" sm="12">
                    <v-text-field
                      dense
                      label="Whatsapp No"
                      outlined
                      type="number"
                      v-model="customer.whatsapp"
                      :hide-details="errors && !errors.whatsapp"
                      :error="errors && errors.whatsapp"
                      :error-messages="
                        errors && errors.whatsapp ? errors.whatsapp[0] : ''
                      "
                    ></v-text-field>
                  </v-col>

                  <v-col md="6" cols="12" sm="12">
                    <v-text-field
                      dense
                      label="Email *"
                      outlined
                      type="email"
                      v-model="customer.email"
                      :hide-details="errors && !errors.email"
                      :error="errors && errors.email"
                      :error-messages="
                        errors && errors.email ? errors.email[0] : ''
                      "
                    ></v-text-field>
                  </v-col>
                  <v-col md="6" cols="12" sm="12">
                    <v-text-field
                      dense
                      label="Car Number"
                      outlined
                      :hide-details="true"
                      type="text"
                      v-model="customer.car_no"
                    ></v-text-field>
                  </v-col>
                </v-row>
              </v-col>
            </v-row>
            <v-row>
              <v-col md="4" cols="12" sm="12">
                <v-select
                  v-model="customer.nationality"
                  :items="countryList"
                  label="Nationality"
                  item-text="name"
                  item-value="name"
                  :hide-details="errors && !errors.nationality"
                  :error="errors && errors.nationality"
                  :error-messages="
                    errors && errors.nationality ? errors.nationality[0] : ''
                  "
                  dense
                  outlined
                ></v-select>
              </v-col>
              <v-col md="4" cols="12" sm="12">
                <v-menu
                  v-model="dob_menu"
                  :close-on-content-click="false"
                  :nudge-right="40"
                  transition="scale-transition"
                  offset-y
                  min-width="auto"
                >
                  <template v-slot:activator="{ on, attrs }">
                    <v-text-field
                      v-model="customer.dob"
                      label="DOB"
                      v-on="on"
                      v-bind="attrs"
                      :hide-details="true"
                      dense
                      outlined
                    ></v-text-field>
                  </template>
                  <v-date-picker
                    v-model="customer.dob"
                    @input="dob_menu = false"
                  ></v-date-picker>
                </v-menu>
              </v-col>
              <v-col md="4" cols="12" sm="12">
                <v-text-field
                  dense
                  outlined
                  label="GST"
                  type="text"
                  v-model="customer.gst_number"
                  :hide-details="true"
                ></v-text-field>
              </v-col>
            </v-row>

            <v-row>
              <v-col md="12" cols="12" sm="12">
                <v-textarea
                  rows="3"
                  label="Address"
                  v-model="customer.address"
                  outlined
                  :hide-details="true"
                ></v-textarea>
              </v-col>
            </v-row>
            <v-row>
              <v-col cols="12" class="text-left">
                <v-btn small @click="store_customer" color="primary"
                  >Submit</v-btn
                >
              </v-col>
            </v-row>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
  </div>
</template>
<script>
import ImagePreview from "../../components/images/ImagePreview.vue";
export default {
  components: {
    ImagePreview,
  },
  inject: {
    theme: {
      default: { isDark: false },
    },
  },
  props: ["customer_id"],
  data() {
    return {
      // ----------------------
      vertical: false,
      activeTab: 0,
      activeSummaryTab: 0,
      documentDialog: false,
      // ------------------

      wantNewDoc: false,
      row: null,
      calIn: {},
      calOut: {},
      searchDialog: false,
      RoomDrawer: null,
      items: [
        { title: "Home", icon: "mdi-view-dashboard" },
        { title: "About", icon: "mdi-forum" },
      ],
      val: 1,
      Model: "Reservation",
      // ---------booked data from parent-------------
      document: "",
      new_payment: 0,
      imgView: 0,
      isImg: false,
      isPdf: false,
      // ----------------------
      documentObj: {
        fileExtension: null,
        file: null,
      },
      snackbar: false,
      checkLoader: false,
      response: "",
      preloader: false,
      loading: false,
      types: ["Online", "Walking", "Travel Agency", "Complimentary"],
      search: {
        mobile: "0752388923",
      },
      sources: [],
      agentList: [],
      idCards: [],
      upload: {
        name: "",
      },
      member_numbers: [1, 2, 3, 4],
      isOnline: false,
      isAgent: false,
      isDiff: false,
      search_available_room: "",

      reservation: {},
      countryList: [],
      foodPriceList: [],
      person_type_arr: [],

      titleItems: [
        { id: 1, name: "Mr" },
        { id: 2, name: "Mrs" },
        { id: 3, name: "Miss" },
        { id: 4, name: "Ms" },
        { id: 5, name: "Dr" },
      ],

      dob_menu: false,
      customer: {
        // passport_expiration_menu: false,
        // passport_expiration: new Date(
        //   Date.now() - new Date().getTimezoneOffset() * 60000
        // )
        //   .toISOString()
        //   .substr(0, 10),
      },

      id_card_type_id: 0,
      errors: [],

      imgPath: "",
      image: "",

      upload: {
        name: "",
      },

      previewImage: null,
    };
  },

  watch: {
    customer_id() {
      this.fetch_single_customer();
    },
  },

  created() {
    this.get_id_cards();
    this.get_countries();
    // this.fetch_single_customer();
    this.preloader = false;
    // console.log("created");
  },

  mounted() {
    // this.fetch_single_customer();
    console.log("mounted" + this.customer_id);
  },

  computed: {
    showImage() {
      if (!this.customer.image && !this.previewImage) {
        return "/no-profile-image.jpg";
      } else if (this.previewImage) {
        return this.previewImage;
      }
      return this.customer.image;
    },
  },
  methods: {
    nextTab() {
      // if (this.activeTab) {
      this.activeTab += 1;
      // }
    },

    prevTab() {
      if (this.activeTab > 0) {
        this.activeTab -= 1;
      }
    },

    onpick_attachment() {
      this.$refs.attachment_input.click();
    },

    attachment(e) {
      this.customer.image = e.target.files[0] || "";

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

    add_document() {
      this.documentDialog = true;
    },

    get_id_cards() {
      let payload = {
        params: {
          company_id: this.$auth.user.company.id,
        },
      };
      this.$axios.get(`get_id_cards`, payload).then(({ data }) => {
        this.idCards = data;
      });
    },

    fetch_single_customer() {
      console.log("id" + this.customer_id);

      this.$axios.get(`customer/${this.customer_id}`).then(({ data }) => {
        // console.log(data.data);
        this.customer = {
          ...data.data,
        };
        this.customer.id_card_type_id = parseInt(data.data.id_card_type_id);
        // this.customer.id_card_type_id = 2;

        // console.log(this.customer);

        // if (this.customer.id_card_type_id) {
        this.get_id_cards();
        // }
      });
    },

    document_validate() {
      let payload = new FormData();

      let arr = [
        "document",
        "id_card_type_id",
        "id_card_no",
        "passport_expiration",
      ];

      for (let x in this.customer) {
        if (arr.includes(x)) {
          payload.append(x, this.customer[x]);
        }
      }

      console.log(payload);
      this.$axios
        .post("/document_validate", payload)
        .then(({ data }) => {
          this.loading = false;
          if (!data.status) {
            this.subLoad = false;
            this.errors = data.errors;
          } else {
            this.errors = [];
            this.documentDialog = false;
          }
        })
        .catch((e) => console.log(e));
    },

    store_customer() {
      let payload = this.mapper(this.customer);
      console.log(payload);
      this.$axios
        .post("/store_customer", payload)
        .then(({ data }) => {
          this.loading = false;
          if (!data.status) {
            this.subLoad = false;
            this.errors = data.errors;
          } else {
            this.errors = [];
            this.alert("Success!", "successfully added customer", "success");
            this.closeDialog();
          }
        })
        .catch((e) => console.log(e));
    },

    mapper(obj) {
      let payload = new FormData();
      for (let x in obj) {
        if (obj[x]) {
          payload.append(x, obj[x]);
        }
      }
      payload.append("company_id", this.$auth.user.company.id);
      return payload;
    },

    closeDialog() {
      this.$emit("close-dialog");
    },

    get_customer() {
      this.errors = [];
      this.checkLoader = true;
      let contact_no = this.customer.contact_no || false;
      if (contact_no == undefined || contact_no == "") {
        // alert("Enter contact number");
        this.checkLoader = false;
        return;
      }
      let payload = {
        params: {
          company_id: this.$auth.user.company.id,
        },
      };

      this.$axios
        .get(`get_customer/${contact_no}`, payload)
        .then(({ data }) => {
          if (!data.status) {
            this.checkLoader = false;
            // this.customer = {};
            this.customer.contact_no = contact_no;
            this.customer.whatsapp = contact_no;
            alert("Customer not found");
            return;
          }

          this.customer = {
            ...data.data,
            customer_id: data.data.id,
          };
          this.customer.id_card_type_id = parseInt(
            this.customer.id_card_type_id
          );

          this.searchDialog = false;
          this.checkLoader = false;
        });
    },

    preview() {
      let file = this.customer.document ?? null;
      const fileExtension = file.split(".").pop().toLowerCase();
      fileExtension == "pdf" ? (this.isPdf = true) : (this.isImg = true);
      this.documentObj = {
        fileExtension: fileExtension,
        file: file,
      };
      this.imgView = true;
    },

    mergeContact() {
      if (!this.isDiff) {
        this.customer.whatsapp = this.customer.contact_no;
      }
    },

    newWhatsapp() {
      if (!this.isDiff) {
        this.customer.whatsapp = this.customer.contact_no;
      } else {
        this.customer.whatsapp = "";
      }
    },

    convert_decimal(n) {
      if (n === +n && n !== (n | 0)) {
        return n.toFixed(2);
      } else {
        return n + ".00";
      }
    },

    get_countries() {
      this.$axios.get(`get_countries`).then(({ data }) => {
        this.countryList = data;
      });
    },

    get_room_types() {
      let payload = {
        params: {
          company_id: this.$auth.user.company.id,
        },
      };
      this.$axios.get(`room_type`, payload).then(({ data }) => {
        this.roomTypes = data;
      });
    },

    capsTitle(val) {
      if (!val) return "---";
      let res = val;
      let upper = res.toUpperCase();
      return upper;
    },

    can(per) {
      let u = this.$auth.user;
      return (
        (u && u.permissions.some((e) => e.name == per || per == "/")) ||
        u.is_master
      );
    },

    alert(title = "Success!", message = "hello", type = "error") {
      this.$swal(title, message, type);
    },
  },
};
</script>