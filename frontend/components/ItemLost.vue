<template>
  <div>
    <v-dialog v-model="imgView" :width="imgPreviewWidth">
      <v-card>
        <v-toolbar class="rounded-md" color="background" dense flat dark>
          <span>Preview</span>
          <v-spacer></v-spacer>
          <v-icon dark class="pa-0" @click="imgView = false"
            >mdi mdi-close-box</v-icon
          >
        </v-toolbar>
        <v-container>
          <v-img :src="src" v-if="isImg" cover></v-img>
        </v-container>
        <v-card-actions> </v-card-actions>
      </v-card>
    </v-dialog>
    <v-dialog v-model="imgView1" width="500">
      <v-card>
        <v-toolbar class="rounded-md" color="background" dense flat dark>
          <span>Item Image</span>
          <v-spacer></v-spacer>
          <v-icon dark class="pa-0" @click="imgView1 = false"
            >mdi mdi-close-box</v-icon
          >
        </v-toolbar>
        <v-container>
          <v-img :src="src1" cover></v-img>
        </v-container>
        <v-card-actions> </v-card-actions>
      </v-card>
    </v-dialog>
    <v-dialog v-model="imgView2" width="500">
      <v-card>
        <v-toolbar class="rounded-md" color="background" dense flat dark>
          <span>Courier Receipt</span>
          <v-spacer></v-spacer>
          <v-icon dark class="pa-0" @click="imgView2 = false"
            >mdi mdi-close-box</v-icon
          >
        </v-toolbar>
        <v-container>
          <v-img :src="src2" cover></v-img>
        </v-container>
        <v-card-actions> </v-card-actions>
      </v-card>
    </v-dialog>
    <v-dialog v-model="imgView3" width="500">
      <v-card>
        <v-toolbar class="rounded-md" color="background" dense flat dark>
          <span>Preview</span>
          <v-spacer></v-spacer>
          <v-icon dark class="pa-0" @click="imgView3 = false"
            >mdi mdi-close-box</v-icon
          >
        </v-toolbar>
        <v-container>
          <v-img :src="src3" cover></v-img>
        </v-container>
        <v-card-actions> </v-card-actions>
      </v-card>
    </v-dialog>
    <v-row class="mb-2 mt=4">
      <v-col md="4" cols="12">
        <v-img :src="getFoundImage()"></v-img>

        <v-row class="mt-3" justify="space-around">
          <v-row justify="center">
            <v-col cols="auto">
              <v-btn small dark @click="showFoundImage()" color="primary">
                View Found Item
              </v-btn>
            </v-col>
            <v-col cols="auto">
              <v-btn small dark @click="showRetunedImage()" color="primary">
                View Courier Receipt
              </v-btn>
            </v-col>

            <v-col cols="auto">
              <v-btn
                target="_blank"
                @click="showIDCard()"
                color="primary"
                elevation="2"
                small
                >View ID Card</v-btn
              >
            </v-col>
          </v-row>
        </v-row>
      </v-col>
      <v-col md="4" cols="12">
        <v-row>
          <v-col md="6">
            <div class="text-box" style="float: left">
              <h6>Resv.No</h6>
              <p>
                {{
                  bookingData && bookingData.reservation_no
                    ? bookingData.reservation_no
                    : "---"
                }}
              </p>
            </div>
          </v-col>
          <v-col md="6">
            <div class="text-box" style="float: left">
              <h6>Room Number</h6>
              <p>
                {{
                  bookingData && bookingData.rooms ? bookingData.rooms : "---"
                }}
              </p>
            </div>
          </v-col>
          <v-col md="6">
            <div class="text-box" style="float: left">
              <h6>Geust</h6>
              <p>
                {{
                  bookingData && bookingData.customer
                    ? bookingData.customer.full_name
                    : "---"
                }}
              </p>
            </div>
          </v-col>
          <v-col md="6">
            <div class="text-box" style="float: left">
              <h6>Type</h6>
              <p>
                {{ bookingData && bookingData.type ? bookingData.type : "---" }}
              </p>
            </div>
          </v-col>

          <v-col md="6">
            <div class="text-box" style="float: left">
              <h6>Phone Number</h6>
              <p>
                {{
                  bookingData && bookingData.customer
                    ? bookingData.customer.contact_no
                    : "---"
                }}
              </p>
            </div>
          </v-col>
          <v-col md="6">
            <div class="text-box" style="float: left">
              <h6>Whatsapp Number</h6>
              <p>
                {{
                  bookingData && bookingData.customer
                    ? bookingData.customer.contact_no
                    : "---"
                }}
              </p>
            </div>
          </v-col>
          <v-col md="6">
            <div class="text-box" style="float: left">
              <h6>Check in Date</h6>
              <p>
                {{
                  bookingData && bookingData.check_in
                    ? bookingData.check_in
                    : "---"
                }}
              </p>
            </div>
          </v-col>
          <v-col md="6">
            <div class="text-box" style="float: left">
              <h6>Check Out Date</h6>
              <p>
                {{
                  bookingData && bookingData.check_out
                    ? bookingData.check_out
                    : "---"
                }}
              </p>
            </div>
          </v-col>

          <v-col md="12">
            <div class="text-box" style="float: left">
              <h6>Address</h6>
              <p>
                {{
                  bookingData &&
                  bookingData.customer &&
                  bookingData.customer.address
                    ? bookingData.customer.address
                    : "---"
                }}
              </p>
            </div>
          </v-col>
        </v-row>
      </v-col>
      <v-col md="4" cols="12">
        <v-row>
          <v-col md="6">
            <div class="text-box" style="float: left">
              <h6>Found Date</h6>
              <p>{{ found_date }}</p>
            </div>
          </v-col>
          <v-col md="6">
            <div class="text-box" style="float: left">
              <h6>Found Time</h6>
              <p>{{ found_time }}</p>
            </div>
          </v-col>
          <v-col md="6">
            <div class="text-box" style="float: left">
              <h6>Found By</h6>
              <p>
                {{
                  editedItem && editedItem.found_person_name
                    ? editedItem.found_person_name
                    : "---"
                }}
              </p>
            </div>
          </v-col>
          <v-col md="6">
            <div class="text-box" style="float: left">
              <h6>Found Location</h6>
              <p>
                {{
                  editedItem && editedItem.found_location
                    ? editedItem.found_location
                    : "---"
                }}
              </p>
            </div>
          </v-col>
          <v-col md="6">
            <div class="text-box" style="float: left">
              <h6>Returned Date</h6>
              <p>{{ returned_date }}</p>
            </div>
          </v-col>
          <v-col md="6">
            <div class="text-box" style="float: left">
              <h6>Returned Time</h6>
              <p>{{ returned_time }}</p>
            </div>
          </v-col>

          <v-col md="12">
            <div class="text-box" style="float: left; height: 150px">
              <h6>Notes</h6>
              <p>
                {{
                  editedItem && editedItem.returned_notes
                    ? editedItem.returned_notes
                    : "---"
                }}
              </p>
            </div>
          </v-col>
        </v-row>
      </v-col>
    </v-row>
  </div>
</template>
<script>
// import ImagePreview from "../components/images/ImagePreview.vue";
export default {
  components: {
    //    ImagePreview,
  },
  props: ["lost_item_id"],

  data: () => ({
    data: [],
    bookingData: { customer: {} },
    editedIndex: -1,

    editedItem: {
      item_name: "",
      missing_datetime: "",
      missing_remarks: "",
      missing_notes: "",
      found_datetime: "",
      found_person_name: "",
      found_location: "",
      found_remarks: "",
      found_notes: "",
      returned_datetime: "",
      returned_person_name: "",
      returned_remarks: "",
      returned_notes: "",
      approved_person_name: "",
      status: "",
    },

    defaultItem: {
      item_name: "",
      missing_datetime: "",
      missing_remarks: "",
      missing_notes: "",
      found_datetime: "",
      found_person_name: "",
      found_location: "",
      found_remarks: "",
      found_notes: "",
      returned_datetime: "",
      returned_person_name: "",
      returned_remarks: "",
      returned_notes: "",
      approved_person_name: "",
      status: "",
    },
    endpoint: "lost_and_found_items",
    missing: {},
    found: {},
    missing_date: "",
    missing_time: "",
    found_date: "",
    found_time: "",
    returned_date: "",
    returned_time: "",
    bookingId: "",
    previewImage: null,
    previewRetunedImage: null,
    imgView: false,
    imgView1: false,
    imgView2: false,
    imgView3: false,
    imgPreviewWidth: 300,
    documentObj: {
      fileExtension: null,
      file: null,
    },
    isPdf: false,
    isImg: false,
    src: "",
    src1: "",
    src2: "",
  }),
  custom_options: {},

  watch: {
    lost_item_id() {
      this.getDataFromApi();
    },
  },
  mounted() {
    this.getDataFromApi();
  },
  created() {
    this.getDataFromApi();
  },
  methods: {
    getDataFromApi() {
      this.clearForm();

      let lostItemId = this.lost_item_id;
      this.loading = true;
      this.bookingData = { customer: {} };
      let options = {
        params: {
          company_id: this.$auth.user.company.id,
          lostItemId: lostItemId,
        },
      };

      this.$axios
        .get(`${this.endpoint}/${lostItemId}`, options)
        .then(({ data }) => {
          this.loading = false;
          this.disableNextProcess = false;
          if (data == "") {
            this.clearForm();
            return false;
          } else {
            this.bookingData = data.booking;
            this.editedIndex = data.id;
            this.bookingId = data.booking.id;
            // if (this.bookingData.booking_status > 1)
            //     this.disableNextProcess = false;
            // else
            //     this.disableNextProcess = true;
            this.loadLostItemDetails(data);
          }
        });
    },
    clearForm() {
      this.response = "";
      //this.bookingId = '';
      this.editedIndex = -1;
      this.bookingData = { customer: {} };
      this.editedItem = this.defaultItem;

      this.missing_date = "";
      this.missing_time = "";
      this.found_time = "";
      this.found_date = "";

      this.returned_time = "";
      this.returned_date = "";
    },
    loadLostItemDetails(missingItemData) {
      if (missingItemData) {
        this.editedItem = missingItemData;
        this.editedIndex = missingItemData.id;

        this.missing_date = this.editedItem.missing_datetime
          ? this.editedItem.missing_datetime.split(" ")[0]
          : "";
        this.missing_time = this.editedItem.missing_datetime
          ? this.editedItem.missing_datetime.split(" ")[1]
          : "";
        this.found_date = this.editedItem.found_datetime
          ? this.editedItem.found_datetime.split(" ")[0]
          : "";
        this.found_time = this.editedItem.found_datetime
          ? this.editedItem.found_datetime.split(" ")[1]
          : "";
        this.returned_date = this.editedItem.returned_datetime
          ? this.editedItem.returned_datetime.split(" ")[0]
          : "";
        this.returned_time = this.editedItem.returned_datetime
          ? this.editedItem.returned_datetime.split(" ")[1]
          : "";
      } else {
        this.editedIndex = -1;
        this.editedItem = this.defaultItem;
      }
    },
    getFoundImage() {
      let returnFile = "";
      if (!this.editedItem.found_image) {
        returnFile = "../no-image-display.png";
      } else returnFile = this.editedItem.found_image; // + "?t=" + Math.random() * 100;

      return returnFile;
    },
    showFoundImage() {
      this.imgPreviewWidth = 600;
      let returnFile = "";
      if (!this.editedItem.found_image) {
        returnFile = "/no-image-display.png";
      } else returnFile = this.editedItem.found_image; // + "?t=" + Math.random() * 100;
      //this.preview(returnFile);
      this.imgView1 = true;
      this.src1 = returnFile;
    },
    showRetunedImage() {
      this.imgPreviewWidth = 300;
      let returnFile = "";
      if (!this.editedItem.recovered_image) {
        returnFile = "/no-image-display.png";
      } else returnFile = this.editedItem.recovered_image; //+ "?t=" + Math.random() * 100;
      //this.preview(returnFile);
      this.imgView2 = true;
      this.src2 = returnFile;
    },
    showIDCard() {
      this.imgPreviewWidth = 400;
      let returnFile = "";
      if (!this.editedItem.document) {
        returnFile = "/no-image-display.png";
      }

      returnFile = this.editedItem.document; //+ "?t=" + Math.random() * 100;;

      //this.preview(returnFile);
      this.imgView3 = true;
      this.src3 = returnFile;
    },
    preview(file) {
      if (!file) return null;
      const fileExtension = file.split(".").pop().toLowerCase();
      fileExtension == "pdf" ? (this.isPdf = true) : (this.isImg = true);
      this.documentObj = {
        fileExtension: fileExtension,
        file: file,
      };

      if (this.documentObj.fileExtension == "pdf") {
        this.isPdf = true;
        this.isImg = false;
      } else {
        this.isPdf = false;
        this.isImg = true;
      }
      this.src = this.documentObj.file + "?t=" + Math.random(0, 9999);
      this.imgView = true;
    },
  },
};
</script>

<style scoped>
.no-bg {
  background-color: white !important;
}

.guest-avatar {
  max-width: 200px !important;
  height: 200px !important;
  float: left;
  margin: 0 auto;
  border-radius: 50%;
}

.text-box {
  border: 1px solid rgb(215, 211, 211);
  padding: 10px 0px 0px 10px;
  margin: 10px 20px;
  position: relative;
  border-radius: 5px;
  width: 100%;
}

.text-box-amt {
  border: 0px solid rgb(215, 211, 211);
  padding: 0px 0px 0px 0px;
  margin: 0px 00px;
  position: relative;
  border-radius: 5px;
  width: 100%;
}

.amt-border {
  border-bottom: 1px solid;
}

.amt-border-full {
  border-bottom: 1px solid;
  border-top: 1px solid;
}

.text-box p {
  margin: 5px;
}

h6 {
  position: absolute;
  top: -12px;
  left: 20px;
  background-color: white;
  padding: 0 10px;
  color: rgb(154, 152, 152);
  margin: 0;
  font-size: 15px;
}

table {
  font-family: arial, sans-serif;
  border-collapse: collapse;
  width: 100%;
}

td,
th {
  text-align: left;
  padding: 8px;
  /* border: 1px solid black !important; */
}

tr:nth-child(even) {
  background-color: white;
}

.custom-text-box {
  border-radius: 2px !important;
  border: 1px solid #dbdddf !important;
}

input[type="text"]:focus.custom-text-box {
  border: 2px solid #5fafa3 !important;
}

select.custom-text-box {
  border: 2px solid #5fafa3 !important;
}

select:focus {
  outline: none !important;
  border-color: #5fafa3;
  box-shadow: 0 0 0px #5fafa3;
}

.table-header-text {
  font-size: 12px;
}
</style>
