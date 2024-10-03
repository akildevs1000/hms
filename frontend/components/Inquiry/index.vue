<template>
  <div v-if="can(`inquiry_access`)">
    <div class="text-center ma-2">
      <v-snackbar v-model="snackbar" top="top" color="secondary" elevation="24">
        {{ response }}
      </v-snackbar>
    </div>

    <v-dialog v-model="inquiryDialog" max-width="800">
      <AssetsIconClose left="790" @click="close" />
      <v-card>
        <AssetsHeadDialog>
          <template #label>{{ formTitle }} Inquiry</template>
          <template v-if="formTitle == 'New'" #search
            ><SearchInquiry @foundCustomer="handleFoundCustomer"
          /></template>
        </AssetsHeadDialog>
        <v-card-text>
          <v-row>
            <v-col md="2" cols="12">
              <v-avatar tile size="120">
                <v-card>
                  <img
                    class="zoom-on-hover"
                    style="z-index: 1; width: 100%"
                    :src="showImage"
                  />
                </v-card>
              </v-avatar>
            </v-col>
            <v-col md="10" cols="12">
              <v-row>
                <v-col md="3" cols="12" sm="12">
                  <v-select
                    v-model="inquiry.title"
                    :items="titleItems"
                    label="Tittle"
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
                <v-col md="3" cols="12" sm="12">
                  <v-text-field
                    label="First Name *"
                    dense
                    outlined
                    type="text"
                    v-model="inquiry.first_name"
                    :hide-details="errors && !errors.first_name"
                    :error="errors && errors.first_name"
                    :error-messages="
                      errors && errors.first_name ? errors.first_name[0] : ''
                    "
                  ></v-text-field>
                </v-col>
                <v-col md="3" cols="12" sm="12">
                  <v-text-field
                    label="Last Name"
                    dense
                    :hide-details="true"
                    outlined
                    type="text"
                    v-model="inquiry.last_name"
                  ></v-text-field>
                </v-col>
                <v-col md="3" dense>
                  <v-select
                    label="Type"
                    v-model="inquiry.inquiry_type"
                    :items="['Room', 'Hall']"
                    dense
                    item-text="name"
                    item-value="id"
                    outlined
                    :hide-details="errors && !errors.inquiry_type"
                    :error-messages="
                      errors && errors.inquiry_type
                        ? errors.inquiry_type[0]
                        : ''
                    "
                  ></v-select>
                </v-col>
                <v-col md="4" cols="12" sm="12">
                  <v-text-field
                    dense
                    label="Contact No *"
                    outlined
                    type="number"
                    v-model="inquiry.contact_no"
                    :hide-details="errors && !errors.contact_no"
                    :error="errors && errors.contact_no"
                    :error-messages="
                      errors && errors.contact_no ? errors.contact_no[0] : ''
                    "
                    @keyup="mergeContact()"
                  ></v-text-field>
                </v-col>
                <v-col md="4" cols="12" sm="12">
                  <v-text-field
                    dense
                    label="Whatsapp No"
                    outlined
                    type="number"
                    v-model="inquiry.whatsapp"
                    :hide-details="errors && !errors.whatsapp"
                    :error="errors && errors.whatsapp"
                    :error-messages="
                      errors && errors.whatsapp ? errors.whatsapp[0] : ''
                    "
                  ></v-text-field>
                </v-col>
                <v-col md="4" cols="12" sm="12">
                  <v-text-field
                    dense
                    label="Email *"
                    outlined
                    type="email"
                    v-model="inquiry.email"
                    :hide-details="errors && !errors.email"
                    :error="errors && errors.email"
                    :error-messages="
                      errors && errors.email ? errors.email[0] : ''
                    "
                  ></v-text-field>
                </v-col>
                <v-col md="6" cols="12" sm="12">
                  <v-select
                    v-model="inquiry.rooms_type"
                    :items="roomTypes"
                    label="Room Type"
                    item-text="name"
                    item-value="name"
                    :hide-details="errors && !errors.rooms_type"
                    :error="errors && errors.rooms_type"
                    :error-messages="
                      errors && errors.rooms_type ? errors.rooms_type[0] : ''
                    "
                    dense
                    outlined
                  ></v-select>
                </v-col>
                <v-col md="6">
                  <v-select
                    label="Purpose"
                    v-model="inquiry.purpose"
                    :items="purposes"
                    dense
                    :hide-details="true"
                    outlined
                  ></v-select>
                </v-col>
                <v-col md="3" cols="12" sm="12">
                  <v-menu
                    v-model="check_in_menu"
                    :close-on-content-click="false"
                    :nudge-right="40"
                    transition="scale-transition"
                    offset-y
                    min-width="auto"
                  >
                    <template v-slot:activator="{ on, attrs }">
                      <v-text-field
                        v-model="inquiry.check_in"
                        label="CheckIn"
                        v-on="on"
                        v-bind="attrs"
                        :hide-details="true"
                        dense
                        outlined
                      ></v-text-field>
                    </template>
                    <v-date-picker
                      no-title
                      v-model="inquiry.check_in"
                      @input="check_in_menu = false"
                    ></v-date-picker>
                  </v-menu>
                </v-col>
                <v-col md="3" cols="12" sm="12">
                  <v-menu
                    v-model="check_out_menu"
                    :close-on-content-click="false"
                    :nudge-right="40"
                    transition="scale-transition"
                    offset-y
                    min-width="auto"
                  >
                    <template v-slot:activator="{ on, attrs }">
                      <v-text-field
                        v-model="inquiry.check_out"
                        label="CheckOut"
                        v-on="on"
                        v-bind="attrs"
                        :hide-details="true"
                        dense
                        outlined
                      ></v-text-field>
                    </template>
                    <v-date-picker
                      no-title
                      v-model="inquiry.check_out"
                      @input="check_out_menu = false"
                    ></v-date-picker>
                  </v-menu>
                </v-col>
                <v-col md="3" cols="12" sm="12">
                  <v-select
                    v-model="inquiry.number_of_rooms"
                    :items="[1, 2, 3, 4, 5, 6, 7, 8]"
                    label="Number of Rooms"
                    :hide-details="true"
                    dense
                    outlined
                  ></v-select>
                </v-col>
                <v-col md="3" cols="12" sm="12">
                  <v-select
                    v-model="inquiry.days"
                    :items="[1, 2, 3, 4, 5, 6, 7, 8, 9, 10]"
                    label="Number of Days"
                    :hide-details="true"
                    dense
                    outlined
                  ></v-select>
                </v-col>
                <v-col md="6" cols="12" sm="12">
                  <v-textarea
                    rows="3"
                    label="Reception Remark"
                    v-model="inquiry.remark"
                    outlined
                    :hide-details="true"
                  ></v-textarea>
                </v-col>
                <v-col md="6" cols="12" sm="12">
                  <v-textarea
                    rows="3"
                    label="Customer Request"
                    v-model="inquiry.customer_request"
                    outlined
                    :hide-details="true"
                  ></v-textarea>
                </v-col>
              </v-row>
            </v-col>
          </v-row>

          <v-row>
            <v-col cols="12" class="text-right">
              <AssetsButtonCancel @close="close" />
              &nbsp;
              <AssetsButtonSubmit @click="submit" />
            </v-col>
          </v-row>
        </v-card-text>
      </v-card>
    </v-dialog>

    <v-data-table
      dense
      :headers="headers"
      :items="data"
      :loading="loading"
      :options.sync="options"
      :footer-props="{
        itemsPerPageOptions: [10, 50, 100],
      }"
      class="px-2"
    >
      <template v-slot:top>
        <v-toolbar flat dense class="mb-5">
          {{ Model }}
          <v-icon color="primary white--text" right @click="getDataFromApi()"
            >mdi-reload</v-icon
          >
          <v-spacer></v-spacer>

          <v-btn
            v-if="can(`inquiry_create`)"
            @click="inquiryDialog = true"
            small
            class="primary"
          >
            <v-icon color="white" small> mdi-plus </v-icon> {{ Model }}
          </v-btn>
        </v-toolbar>
      </template>

      <template v-slot:item.first_name="{ item }">
        {{ item.title }} {{ item.first_name }}
        <br />
        {{ item.email }}
        <br />
        {{ item.contact_no }}
      </template>

      <template v-slot:item.quotation="{ item }">
        <span
          style="cursor: pointer"
          v-if="item?.quotation?.book_date"
          @click="openExternalWinodw(item)"
          class="primary--text"
          >{{ item?.quotation?.ref_no }}</span
        >
        <span v-else>---</span>
      </template>

      <template v-slot:item.options="{ item }">
        <v-menu bottom left>
          <template v-slot:activator="{ on, attrs }">
            <v-btn icon v-bind="attrs" v-on="on">
              <v-icon>mdi-dots-vertical</v-icon>
            </v-btn>
          </template>

          <v-list width="140" dense>
            <v-list-item @click="editItem(item)">
              <v-list-item-title style="cursor: pointer">
                <v-icon color="secondary" small> mdi-pencil </v-icon>
                Edit
              </v-list-item-title>
            </v-list-item>
            <v-list-item v-if="item.inquiry_type == 'Room'">
              <InquiryQuotationRoomCreate
                :item="item"
                :model="`Convert to Quotation`"
                :endpoint="`quotation`"
                @response="getDataFromApi"
              />
            </v-list-item>

            <v-list-item v-else-if="item.inquiry_type == 'Hall'">
              <InquiryQuotationHallCreate
                :item="item"
                :model="`Convert to Quotation`"
                :endpoint="`quotation`"
                @response="getDataFromApi"
              />
            </v-list-item>
          </v-list>
        </v-menu>
      </template>
    </v-data-table>
  </div>
  <NoAccess v-else />
</template>
<script>
export default {
  data: () => ({
    pagination: {
      current: 1,
      total: 0,
      per_page: 10,
    },
    Model: "Inquiry",
    options: {},
    endpoint: "inquiry",
    search: "",
    snackbar: false,
    dialog: false,
    inquiryDialog: false,
    ids: [],
    loading: false,
    isDiff: false,
    total: 0,

    editedIndex: -1,

    titleItems: [
      { id: 1, name: "Mr" },
      { id: 2, name: "Mrs" },
      { id: 3, name: "Miss" },
      { id: 4, name: "Ms" },
      { id: 5, name: "Dr" },
    ],
    inquiry: {},

    check_in_menu: false,
    check_out_menu: false,
    // dob: null,

    upload: {
      name: "",
    },
    headers: [
      {
        text: "#",
        value: "id",
      },
      {
        value: "first_name",
        text: "First Name",
      },
      {
        value: "check_in",
        text: "C/In",
      },
      {
        value: "check_out",
        text: "C/Out",
      },
      {
        value: "days",
        text: "Days",
      },
      {
        value: "rooms_type",
        text: "Room Type",
      },
      {
        value: "number_of_rooms",
        text: "N/Rooms",
      },
      {
        value: "quotation",
        text: "Quotation",
      },
      {
        value: "inquiry_type",
        text: "Type",
      },
      {
        value: "options",
        text: "Action",
      },
    ],
    previewImage: null,
    purposes: [
      "Tour",
      "Business",
      "Hospital",
      "Holiday",
      "Party/Functions",
      "Friend Visit",
      "Marriage",
    ],

    response: "",
    data: [],
    countryList: [],
    roomTypes: [],
    errors: [],
  }),

  computed: {
    formTitle() {
      return this.editedIndex === -1 ? "New" : "Edit";
    },
    showImage() {
      if (!this.inquiry.image && !this.previewImage) {
        return "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRudDbHeW2OobhX8E9fAY-ctpUAHeTNWfaqJA&usqp=CAU";
      } else if (this.previewImage) {
        return this.previewImage;
      }
      return this.inquiry.image;
    },
  },

  watch: {
    inquiryDialog(val) {
      val || this.close();
      this.errors = [];
      this.search = "";
    },
  },

  created() {
    this.loading = true;
    this.getDataFromApi();
    this.get_room_types();
  },

  methods: {
    openExternalWinodw({ inquiry_type, quotation: { id } }) {
      let type = inquiry_type.toLowerCase();
      let url = `${process.env.BACKEND_URL}quotation-${type}/${id}`;
      let element = document.createElement("a");
      element.setAttribute("target", "_blank");
      element.setAttribute("href", url);
      document.body.appendChild(element);
      element.click();
    },
    handleFoundCustomer(e) {
      this.inquiry = {
        ...this.inquiry,
        ...e,
      };
      console.log("🚀 ~ handleFoundCustomer ~ this.inquiry:", this.inquiry);
    },
    can(per) {
      let u = this.$auth.user;
      return (
        (u && u.permissions.some((e) => e == per || per == "/")) || u.is_master
      );
    },

    caps(str) {
      if (str == "" || str == null) {
        return "---";
      } else {
        let res = str.toString();
        return res.replace(/\b\w/g, (c) => c.toUpperCase());
      }
    },

    onPageChange() {
      this.getDataFromApi();
    },

    onpick_attachment() {
      this.$refs.attachment_input.click();
    },

    attachment(e) {
      this.inquiry.image = e.target.files[0] || "";

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

    mergeContact() {
      // setTimeout(() => {
      if (!this.isDiff) {
        this.inquiry.whatsapp = this.inquiry.contact_no;
      }
      // }, 10000);
    },

    async getDataFromApi(url = this.endpoint) {
      this.loading = true;
      let { data } = await this.$axios.get(url, {
        params: {
          company_id: this.$auth.user.company.id,
          search: this.search,
        },
      });
      this.loading = false;
      this.data = data.data;
    },

    searchIt(e) {
      if (e.length > 2) {
        this.getDataFromApi();
      }
    },

    editItem(item) {
      this.editedIndex = this.data.indexOf(item);
      this.inquiry = Object.assign({}, item);
      this.inquiryDialog = true;
    },

    deleteItem(item) {
      confirm(
        "Are you sure you wish to delete , to mitigate any inconvenience in future."
      ) &&
        this.$axios
          .delete(this.endpoint + "/" + item.id)
          .then(({ data }) => {
            const index = this.data.indexOf(item);
            this.data.splice(index, 1);
            this.snackbar = data.status;
            this.response = data.message;
          })
          .catch((err) => console.log(err));
    },

    close() {
      this.inquiry = {};
      this.editedIndex = -1;
      this.inquiryDialog = false;
      setTimeout(() => {
        this.inquiry = Object.assign({}, this.inquiry);
        this.editedIndex = -1;
      }, 300);
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

    submit() {
      let payload = this.mapper(this.inquiry);

      if (this.editedIndex > -1) {
        this.$axios
          .post(this.endpoint + "/" + this.inquiry.id, payload)
          .then(({ data }) => {
            if (!data.status) {
              this.errors = data.errors;
            } else {
              this.getDataFromApi();
              this.$swal("Success!", data.message, "success");
              this.close();
              this.errors = [];
              this.search = "";
            }
          })
          .catch((err) => console.log(err));
      } else {
        this.$axios
          .post(this.endpoint, payload)
          .then(({ data }) => {
            console.log(data);

            if (!data.status) {
              this.errors = data.errors;
            } else {
              this.getDataFromApi();
              this.$swal("Success!", data.message, "success");
              this.close();
              this.errors = [];
              this.search = "";
            }
          })
          .catch((res) => console.log(res));
      }
    },
  },
};
</script>
