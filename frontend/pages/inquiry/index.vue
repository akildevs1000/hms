<template>
  <div v-if="can(`inquiry_access`)">
    <div class="text-center ma-2">
      <v-snackbar v-model="snackbar" top="top" color="secondary" elevation="24">
        {{ response }}
      </v-snackbar>
    </div>
    <v-row class="mt-5 mb-5">
      <v-col cols="6">
        <h3>{{ Model }}</h3>
        <div>Dashboard / {{ Model }}</div>
      </v-col>
    </v-row>

    <v-dialog v-model="inquiryDialog" max-width="60%">
      <v-card>
        <v-toolbar class="rounded-md" color="background" dense flat dark>
          <span>{{ formTitle }} Inquiry</span>
          <v-spacer></v-spacer>
          <v-icon dark class="pa-0" @click="close">mdi mdi-close-box</v-icon>
        </v-toolbar>
        <v-container>
          <v-row class="m-0 p-0 mt-0">
            <v-col md="12">
              <v-card flat>
                <v-card-text>
                  <v-row>
                    <v-col md="2" cols="12">
                      <v-img @click="onpick_attachment" style="
                          width: 150px;
                          height: 150px;
                          margin: 0 auto;
                          border-radius: 50%;
                        " :src="showImage"></v-img>
                      <input required type="file" @change="attachment" style="display: none" accept="image/*"
                        ref="attachment_input" />
                      <span v-if="errors && errors.image" class="red--text mt-2">
                        {{ errors.image[0] }}</span>
                    </v-col>
                    <v-col md="10" cols="12">
                      <v-row>
                        <v-col md="2" class="mt-0">
                          <!-- <v-btn color="primary" @click="searchDialog = true"> -->
                          <v-btn color="primary">
                            Search
                            <v-icon right dark>mdi mdi-magnify</v-icon>
                          </v-btn>
                        </v-col>
                        <v-col md="5" dense> </v-col>
                        <v-col md="5" dense>
                          <v-select label="Type" v-model="inquiry.customer_type"
                            :items="['Company', 'Regular', 'Corporate']" dense item-text="name" item-value="id" outlined
                            :hide-details="true"></v-select>
                        </v-col>
                        <v-col md="2" cols="12" sm="12">
                          <v-select v-model="inquiry.title" :items="titleItems" label="Tittle *" dense item-text="name"
                            item-value="name" :hide-details="errors && !errors.title" :error="errors && errors.title"
                            :error-messages="errors && errors.title ? errors.title[0] : ''
                              " outlined></v-select>
                        </v-col>
                        <v-col md="5" cols="12" sm="12">
                          <v-text-field label="First Name *" dense outlined type="text" v-model="inquiry.first_name"
                            :hide-details="errors && !errors.first_name" :error="errors && errors.first_name"
                            :error-messages="errors && errors.first_name
                              ? errors.first_name[0]
                              : ''
                              "></v-text-field>
                        </v-col>
                        <v-col md="5" cols="12" sm="12">
                          <v-text-field label="Last Name" dense :hide-details="true" outlined type="text"
                            v-model="inquiry.last_name"></v-text-field>
                        </v-col>
                        <v-col md="4" cols="12" sm="12">
                          <v-text-field dense label="Contact No *" outlined type="number" v-model="inquiry.contact_no"
                            :hide-details="errors && !errors.contact_no" :error="errors && errors.contact_no"
                            :error-messages="errors && errors.contact_no
                              ? errors.contact_no[0]
                              : ''
                              " @keyup="mergeContact()"></v-text-field>
                        </v-col>
                        <v-col md="4" cols="12" sm="12">
                          <v-text-field dense label="Whatsapp No" outlined type="number" v-model="inquiry.whatsapp"
                            :hide-details="errors && !errors.whatsapp" :error="errors && errors.whatsapp" :error-messages="errors && errors.whatsapp
                              ? errors.whatsapp[0]
                              : ''
                              "></v-text-field>
                        </v-col>
                        <v-col md="4" cols="12" sm="12">
                          <v-text-field dense label="Email *" outlined type="email" v-model="inquiry.email"
                            :hide-details="errors && !errors.email" :error="errors && errors.email" :error-messages="errors && errors.email ? errors.email[0] : ''
                              "></v-text-field>
                        </v-col>
                      </v-row>
                    </v-col>
                  </v-row>
                  <v-row>
                    <v-col md="4" cols="12" sm="12">
                      <v-select v-model="inquiry.rooms_type" :items="roomTypes" label="Room Type" item-text="name"
                        item-value="name" :hide-details="errors && !errors.rooms_type"
                        :error="errors && errors.rooms_type" :error-messages="errors && errors.rooms_type
                          ? errors.rooms_type[0]
                          : ''
                          " dense outlined></v-select>
                    </v-col>
                    <v-col md="4" cols="12" sm="12">
                      <v-text-field dense outlined label="City" type="text" v-model="inquiry.city"
                        :hide-details="true"></v-text-field>
                    </v-col>
                    <v-col md="4">
                      <v-select label="Purpose" v-model="inquiry.purpose" :items="purposes" dense :hide-details="true"
                        outlined></v-select>
                    </v-col>
                    <v-col md="3" cols="12" sm="12">
                      <v-menu v-model="check_in_menu" :close-on-content-click="false" :nudge-right="40"
                        transition="scale-transition" offset-y min-width="auto">
                        <template v-slot:activator="{ on, attrs }">
                          <v-text-field v-model="inquiry.check_in" label="CheckIn" v-on="on" v-bind="attrs"
                            :hide-details="true" dense outlined></v-text-field>
                        </template>
                        <v-date-picker v-model="inquiry.check_in" @input="check_in_menu = false"></v-date-picker>
                      </v-menu>
                    </v-col>
                    <v-col md="3" cols="12" sm="12">
                      <v-menu v-model="check_out_menu" :close-on-content-click="false" :nudge-right="40"
                        transition="scale-transition" offset-y min-width="auto">
                        <template v-slot:activator="{ on, attrs }">
                          <v-text-field v-model="inquiry.check_out" label="CheckOut" v-on="on" v-bind="attrs"
                            :hide-details="true" dense outlined></v-text-field>
                        </template>
                        <v-date-picker v-model="inquiry.check_out" @input="check_out_menu = false"></v-date-picker>
                      </v-menu>
                    </v-col>
                    <v-col md="3" cols="12" sm="12">
                      <v-select v-model="inquiry.number_of_rooms" :items="[1, 2, 3, 4, 5, 6, 7, 8]"
                        label="Number of Rooms" :hide-details="true" dense outlined></v-select>
                    </v-col>
                    <v-col md="3" cols="12" sm="12">
                      <v-select v-model="inquiry.days" :items="[1, 2, 3, 4, 5, 6, 7, 8, 9, 10]" label="Number of Days"
                        :hide-details="true" dense outlined></v-select>
                    </v-col>
                  </v-row>

                  <v-row>
                    <v-col md="6" cols="12" sm="12">
                      <v-textarea rows="3" label="Reception Remark" v-model="inquiry.remark" outlined
                        :hide-details="true"></v-textarea>
                    </v-col>
                    <v-col md="6" cols="12" sm="12">
                      <v-textarea rows="3" label="Customer Request" v-model="inquiry.customer_request" outlined
                        :hide-details="true"></v-textarea>
                    </v-col>
                  </v-row>
                  <v-row>
                    <v-col cols="12" class="text-left">
                      <v-btn small color="primary" @click="save">Submit</v-btn>
                    </v-col>
                  </v-row>
                </v-card-text>
              </v-card>
            </v-col>
          </v-row>
        </v-container>
        <v-card-actions> </v-card-actions>
      </v-card>
    </v-dialog>

    <v-row class="mt-0 pt-0">
      <v-col xs="12" sm="12" md="2" cols="12">
        <v-text-field class="" label="Search..." dense outlined flat append-icon="mdi-magnify" @input="searchIt"
          v-model="search" hide-details></v-text-field>
      </v-col>
    </v-row>
    <div>
      <v-card class="mb-5 rounded-md mt-3" elevation="0">
        <v-toolbar class="rounded-md" color="background" dense flat dark>
          <span> {{ Model }} List </span>
          <v-spacer></v-spacer>
          <v-btn v-if="can(`inquiry_create`)" class="float-right py-3" @click="inquiryDialog = true" x-small
            color="primary">
            <v-icon color="white" small class="py-5">mdi-plus</v-icon>
            Add
          </v-btn>
        </v-toolbar>
        <table>
          <tr style="font-size: 13px">
            <th v-for="(item, index) in headers" :key="index">
              {{ item.text }}
            </th>
          </tr>
          <v-progress-linear v-if="loading" :active="loading" :indeterminate="loading" absolute
            color="primary"></v-progress-linear>
          <tr v-for="(item, index) in data" :key="index" style="font-size: 13px">
            <td>
              <b>{{ ++index }}</b>
            </td>
            <td>{{ item.title || "" }} {{ item.first_name || "---" }}</td>
            <td>{{ item.customer_type || "---" }}</td>
            <td>{{ item.contact_no || "---" }}</td>
            <td>{{ item.whatsapp || "---" }}</td>
            <td>{{ item.email || "---" }}</td>
            <td>{{ item.check_in || "---" }}</td>
            <td>{{ item.check_out || "---" }}</td>
            <td>{{ item.days || "---" }}</td>
            <td>{{ item.rooms_type || "---" }}</td>
            <td>{{ item.number_of_rooms || "---" }}</td>
            <td>
              <v-menu bottom left v-if="can(`inquiry_edit`)">
                <template v-slot:activator="{ on, attrs }">
                  <v-btn dark-2 icon v-bind="attrs" v-on="on">
                    <v-icon>mdi-dots-vertical</v-icon>
                  </v-btn>
                </template>
                <v-list width="120" dense>
                  <v-list-item @click="editItem(item)">
                    <v-list-item-title style="cursor: pointer">
                      <v-icon color="secondary" small> mdi-pencil </v-icon>
                      Edit
                    </v-list-item-title>
                  </v-list-item>
                </v-list>
              </v-menu>
            </td>
          </tr>
        </table>
      </v-card>
      <div>
        <v-row>
          <v-col md="12" class="float-right">
            <div class="float-right">
              <v-pagination v-model="pagination.current" :length="pagination.total" @input="onPageChange"
                :total-visible="12"></v-pagination>
            </div>
          </v-col>
        </v-row>
      </div>
    </div>
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
      },
      {
        text: "First Name",
      },
      {
        text: "Customer Type",
      },
      {
        text: "Contact No",
      },
      {
        text: "Whatsapp",
      },
      {
        text: "Email",
      },
      {
        text: "C/In",
      },
      {
        text: "C/Out",
      },
      {
        text: "Days",
      },
      {
        text: "Room Type",
      },
      {
        text: "N/Rooms",
      },
      {
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
        return "/no-profile-image.jpg";
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

    getDataFromApi(url = this.endpoint) {
      this.loading = true;
      let page = this.pagination.current;
      let options = {
        params: {
          per_page: this.pagination.per_page,
          company_id: this.$auth.user.company.id,
          search: this.search,
        },
      };

      this.$axios.get(`${url}?page=${page}`, options).then(({ data }) => {
        this.data = data.data;
        this.pagination.current = data.current_page;
        this.pagination.total = data.last_page;
        this.loading = false;
      });
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

    save() {
      let payload = this.mapper(this.inquiry);

      if (this.editedIndex > -1) {
        this.$axios
          .post(this.endpoint + "/" + this.inquiry.id, payload)
          .then(({ data }) => {
            if (!data.status) {
              this.errors = data.errors;
            } else {
              this.getDataFromApi();
              this.snackbar = data.status;
              this.response = data.message;
              this.close();
            }
          })
          .catch((err) => console.log(err));
      } else {
        this.$axios
          .post(this.endpoint, payload)
          .then(({ data }) => {
            if (!data.status) {
              this.errors = data.errors;
            } else {
              this.getDataFromApi();
              this.snackbar = data.status;
              this.response = data.message;
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
<style scoped>
table {
  font-family: arial, sans-serif;
  border-collapse: collapse;
  width: 100%;
}

td,
th {
  text-align: left;
  padding: 5px;
}

tr:nth-child(even) {
  background-color: #e9e9e9;
}

input[type="text"]:focus.custom-text-box {
  border: 2px solid #5fafa3 !important;
}
</style>
