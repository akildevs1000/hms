<template>
  <div>
    <v-dialog v-model="imgView" max-width="80%">
      <v-card>
        <v-toolbar class="rounded-md" color="background" dense flat dark>
          <span>Preview</span>
          <v-spacer></v-spacer>
          <v-icon dark class="pa-0" @click="imgView = false"
            >mdi mdi-close-box</v-icon
          >
        </v-toolbar>
        <v-container>
          <ImagePreview :docObj="documentObj"></ImagePreview>
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
              </v-col>
              <v-col md="10" cols="12">
                <v-row>
                  <v-col md="6" cols="12" sm="12">
                    <v-text-field
                      label="First Name *"
                      dense
                      outlined
                      type="text"
                      v-model="user.first_name"
                      :hide-details="errors && !errors.first_name"
                      :error="errors && errors.first_name"
                      :error-messages="
                        errors && errors.first_name ? errors.first_name[0] : ''
                      "
                    ></v-text-field>
                  </v-col>
                  <v-col md="6" cols="12" sm="12">
                    <v-text-field
                      label="Last Name"
                      dense
                      :hide-details="true"
                      outlined
                      type="text"
                      v-model="user.last_name"
                    ></v-text-field>
                  </v-col>
                  <v-col md="6" cols="12" sm="12">
                    <v-text-field
                      dense
                      label="Email *"
                      outlined
                      type="email"
                      v-model="user.email"
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
                      label="Password *"
                      outlined
                      type="password"
                      v-model="user.password"
                      :hide-details="errors && !errors.password"
                      :error="errors && errors.password"
                      :error-messages="
                        errors && errors.password ? errors.password[0] : ''
                      "
                    ></v-text-field>
                  </v-col>
                  <v-col md="12" cols="12" sm="12">
                    <v-select
                      v-model="user.role"
                      :items="[
                        { id: 1, name: 'Admin' },
                        { id: 2, name: 'User' },
                      ]"
                      label="Roles"
                      item-text="name"
                      item-value="id"
                      :hide-details="errors && !errors.role"
                      :error="errors && errors.role"
                      :error-messages="
                        errors && errors.role ? errors.role[0] : ''
                      "
                      dense
                      outlined
                    ></v-select>
                  </v-col>
                </v-row>
              </v-col>
            </v-row>

            <v-row>
              <v-col cols="12" class="text-right">
                <v-btn small @click="store_user" color="primary">Submit</v-btn>
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
  props: ["user_id"],
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
      user: {
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
    user_id() {
      this.fetch_single_user();
    },
  },

  created() {
    this.get_id_cards();
    this.get_countries();
    // this.fetch_single_user();
    this.preloader = false;
    // console.log("created");
  },

  mounted() {
    // this.fetch_single_user();
    console.log("mounted" + this.user_id);
  },

  computed: {
    showImage() {
      if (!this.user.image && !this.previewImage) {
        return "/no-profile-image.jpg";
      } else if (this.previewImage) {
        return this.previewImage;
      }
      return this.user.image;
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
      this.user.image = e.target.files[0] || "";

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

    fetch_single_user() {
      console.log("id" + this.user_id);

      this.$axios.get(`user/${this.user_id}`).then(({ data }) => {
        // console.log(data.data);
        this.user = {
          ...data.data,
        };
        this.user.id_card_type_id = parseInt(data.data.id_card_type_id);
        // this.user.id_card_type_id = 2;

        // console.log(this.user);

        // if (this.user.id_card_type_id) {
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

      for (let x in this.user) {
        if (arr.includes(x)) {
          payload.append(x, this.user[x]);
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

    store_user() {
      let payload = this.mapper(this.user);
      this.$axios
        .post("/store_user", payload)
        .then(({ data }) => {
          this.loading = false;
          if (!data.status) {
            this.subLoad = false;
            this.errors = data.errors;
          } else {
            this.errors = [];
            this.alert("Success!", "successfully added user", "success");
            this.closeDialog();
          }
        })
        .catch((e) => console.log(e));
    },

    mapper(obj) {
      let payload = new FormData();
      for (let x in obj) {
        if (obj[x]) {
          console.log(x);
          payload.append(x, obj[x]);
        }
      }
      payload.append("company_id", this.$auth.user.company.id);
      return payload;
    },

    closeDialog() {
      this.$emit("close-dialog");
    },

    get_user() {
      this.errors = [];
      this.checkLoader = true;
      let contact_no = this.user.contact_no || false;
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

      this.$axios.get(`get_user/${contact_no}`, payload).then(({ data }) => {
        if (!data.status) {
          this.checkLoader = false;
          // this.user = {};
          this.user.contact_no = contact_no;
          this.user.whatsapp = contact_no;
          alert("user not found");
          return;
        }

        this.user = {
          ...data.data,
          user_id: data.data.id,
        };
        this.user.id_card_type_id = parseInt(this.user.id_card_type_id);

        this.searchDialog = false;
        this.checkLoader = false;
      });
    },

    preview() {
      let file = this.user.document ?? null;
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
        this.user.whatsapp = this.user.contact_no;
      }
    },

    newWhatsapp() {
      if (!this.isDiff) {
        this.user.whatsapp = this.user.contact_no;
      } else {
        this.user.whatsapp = "";
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

<style src="@/assets/css/check.css"></style>
