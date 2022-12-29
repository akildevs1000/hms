<template>
  <div>
    <link href="matrix/dist/css/style.min.css" rel="stylesheet" />
    <div class="page-wrapper">
      <div class="container-fluid">
        <div class="row">
          <!-- Column -->
          <div class="col-md-6 col-lg-2 col-xlg-3 py-0">
            <div class="card card-hover">
              <div class="box bg-cyan text-center">
                <h1 class="font-light text-white">
                  <i class="fa fa-users"></i>
                  <h5>73</h5>
                </h1>
                <h6 class="text-white">Total Customers</h6>
              </div>
            </div>
          </div>
          <!-- Column -->
          <div class="col-md-6 col-lg-4 col-xlg-3 py-0">
            <div class="card card-hover">
              <div class="box bg-success text-center">
                <h1 class="font-light text-white">
                  <i class="fas fa-door-open"></i>
                  <h5>16</h5>
                </h1>
                <h6 class="text-white">Available</h6>
              </div>
            </div>
          </div>
          <!-- Column -->
          <div class="col-md-6 col-lg-2 col-xlg-3 py-0">
            <div class="card card-hover">
              <div class="box bg-warning text-center">
                <h1 class="font-light text-white">
                  <i class="fas fa-door-closed"></i>
                  <h5>6</h5>
                </h1>
                <h6 class="text-white">Booked Room</h6>
              </div>
            </div>
          </div>
          <!-- Column -->
          <div class="col-md-6 col-lg-2 col-xlg-3 py-0">
            <div class="card card-hover">
              <div class="box bg-danger text-center">
                <h1 class="font-light text-white">
                  <!-- <i class="fas fa-door-closed"></i> -->
                  <i class="fas fa-money-bill"></i>
                  <h5>3</h5>
                </h1>
                <h6 class="text-white">Advance Paid Booking</h6>
              </div>
            </div>
          </div>
          <!-- Column -->
          <div class="col-md-6 col-lg-2 col-xlg-3 py-0">
            <div class="card card-hover">
              <div class="box bg-info text-center">
                <h1 class="font-light text-white">
                  <i class="fas fa-door-closed"></i>
                  <h5>3</h5>
                </h1>
                <h6 class="text-white">Only Booking</h6>
              </div>
            </div>
          </div>
          <!-- Column -->
          <!-- Column -->
          <div class="col-md-6 col-lg-4 col-xlg-3 py-0">
            <div class="card card-hover">
              <div class="box bg-danger text-center">
                <h1 class="font-light text-white">
                  <i
                    class="fas fa-plane-arrival"
                    style="-webkit-transform: scaleX(-1);
                            transform: scaleX(-1);"
                  ></i>
                  <h5>6</h5>
                </h1>
                <h6 class="text-white">Expect Check In</h6>
              </div>
            </div>
          </div>
          <!-- Column -->
          <div class="col-md-6 col-lg-2 col-xlg-3 py-0">
            <div class="card card-hover">
              <div class="box bg-info text-center">
                <h1 class="font-light text-white">
                  <i class="fas fa-plane-departure"></i>
                  <h5>2</h5>
                </h1>
                <h6 class="text-white">Expect Checked Out</h6>
              </div>
            </div>
          </div>
          <!-- Column -->
          <div class="col-md-6 col-lg-2 col-xlg-3 py-0">
            <div class="card card-hover">
              <div class="box bg-cyan text-center">
                <h1 class="font-light text-white">
                  <svg
                    viewBox="0 0 576 512"
                    fill="#ffff"
                    width="50px"
                    height="40px"
                    style="-webkit-transform: scaleX(-1);
                            transform: scaleX(-1);"
                  >
                    <path
                      d="M432 96c26.5 0 48-21.5 48-48s-21.5-48-48-48s-48 21.5-48 48s21.5 48 48 48zM347.7 200.5c1-.4 1.9-.8 2.9-1.2l-16.9 63.5c-5.6 21.1-.1 43.6 14.7 59.7l70.7 77.1 22 88.1c4.3 17.1 21.7 27.6 38.8 23.3s27.6-21.7 23.3-38.8l-23-92.1c-1.9-7.8-5.8-14.9-11.2-20.8l-49.5-54 19.3-65.5 9.6 23c4.4 10.6 12.5 19.3 22.8 24.5l26.7 13.3c15.8 7.9 35 1.5 42.9-14.3s1.5-35-14.3-42.9L505 232.7l-15.3-36.8C472.5 154.8 432.3 128 387.7 128c-22.8 0-45.3 4.8-66.1 14l-8 3.5c-32.9 14.6-58.1 42.4-69.4 76.5l-2.6 7.8c-5.6 16.8 3.5 34.9 20.2 40.5s34.9-3.5 40.5-20.2l2.6-7.8c5.7-17.1 18.3-30.9 34.7-38.2l8-3.5zm-30 135.1l-25 62.4-59.4 59.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L340.3 441c4.6-4.6 8.2-10.1 10.6-16.1l14.5-36.2-40.7-44.4c-2.5-2.7-4.8-5.6-7-8.6zM256 274.1c-7.7-4.4-17.4-1.8-21.9 5.9l-32 55.4L147.7 304c-15.3-8.8-34.9-3.6-43.7 11.7L40 426.6c-8.8 15.3-3.6 34.9 11.7 43.7l55.4 32c15.3 8.8 34.9 3.6 43.7-11.7l64-110.9c1.5-2.6 2.6-5.2 3.3-8L261.9 296c4.4-7.7 1.8-17.4-5.9-21.9z"
                    />
                  </svg>
                  <h5>3</h5>
                </h1>
                <h6 class="text-white">Check In</h6>
              </div>
            </div>
          </div>
          <!-- Column -->
          <div class="col-md-6 col-lg-2 col-xlg-3 py-0">
            <div class="card card-hover">
              <div class="box bg-success text-center">
                <h1 class="font-light text-white">
                  <svg
                    viewBox="0 0 576 512"
                    fill="#ffff"
                    width="50px"
                    height="40px"
                  >
                    <path
                      d="M432 96c26.5 0 48-21.5 48-48s-21.5-48-48-48s-48 21.5-48 48s21.5 48 48 48zM347.7 200.5c1-.4 1.9-.8 2.9-1.2l-16.9 63.5c-5.6 21.1-.1 43.6 14.7 59.7l70.7 77.1 22 88.1c4.3 17.1 21.7 27.6 38.8 23.3s27.6-21.7 23.3-38.8l-23-92.1c-1.9-7.8-5.8-14.9-11.2-20.8l-49.5-54 19.3-65.5 9.6 23c4.4 10.6 12.5 19.3 22.8 24.5l26.7 13.3c15.8 7.9 35 1.5 42.9-14.3s1.5-35-14.3-42.9L505 232.7l-15.3-36.8C472.5 154.8 432.3 128 387.7 128c-22.8 0-45.3 4.8-66.1 14l-8 3.5c-32.9 14.6-58.1 42.4-69.4 76.5l-2.6 7.8c-5.6 16.8 3.5 34.9 20.2 40.5s34.9-3.5 40.5-20.2l2.6-7.8c5.7-17.1 18.3-30.9 34.7-38.2l8-3.5zm-30 135.1l-25 62.4-59.4 59.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L340.3 441c4.6-4.6 8.2-10.1 10.6-16.1l14.5-36.2-40.7-44.4c-2.5-2.7-4.8-5.6-7-8.6zM256 274.1c-7.7-4.4-17.4-1.8-21.9 5.9l-32 55.4L147.7 304c-15.3-8.8-34.9-3.6-43.7 11.7L40 426.6c-8.8 15.3-3.6 34.9 11.7 43.7l55.4 32c15.3 8.8 34.9 3.6 43.7-11.7l64-110.9c1.5-2.6 2.6-5.2 3.3-8L261.9 296c4.4-7.7 1.8-17.4-5.9-21.9z"
                    />
                  </svg>
                  <h5>4</h5>
                </h1>
                <h6 class="text-white">Check Out</h6>
              </div>
            </div>
          </div>
          <!-- Column -->
          <div class="col-md-6 col-lg-2 col-xlg-3 py-0">
            <div class="card card-hover">
              <div class="box bg-warning text-center">
                <h1 class="font-light text-white">
                  <i class="fa fa-user"></i>
                  <h5>4</h5>
                </h1>
                <h6 class="text-white">Agent</h6>
              </div>
            </div>
          </div>
          <!-- Column -->
        </div>
        <div class="row">
          <div class="col-md-12">
            <div class="card elevation-0">
              <div class="card-body">
                <div class="d-md-flex align-items-center">
                  <div>
                    <h4 class="card-title">Rooms</h4>
                    <!-- <h5 class="card-subtitle">Overview of Latest Month</h5> -->
                  </div>
                </div>
                <div class="row">
                  <!-- column -->
                  <div class="col-lg-9 pt-0">
                    <v-row>
                      <!-- {{ rooms.notAvailableRooms }} -->
                      <v-col
                        :class="noAvailableRoom.id"
                        lg="1"
                        md="4"
                        sm="12"
                        cols="12"
                        v-for="(noAvailableRoom, i) in notAvailableRooms"
                        :key="i"
                      >
                        <v-card
                          @contextmenu="show"
                          :elevation="0"
                          @mouseover="
                            mouseOver(
                              noAvailableRoom.booked_room.id,
                              noAvailableRoom.booked_room.booking.booking_status
                            )
                          "
                          @dblclick="dblclick"
                          class="ma-0 px-md-1 py-md-2"
                          dark
                          :style="
                            `background-image:${noAvailableRoom.booked_room.background}`
                          "
                          ><div class="text-center">
                            {{ caps(noAvailableRoom.room_type.name) }}
                          </div>
                          <div class="text-center">
                            {{ noAvailableRoom.room_no }}
                          </div>
                        </v-card>
                      </v-col>
                    </v-row>
                    <v-row>
                      <v-col
                        :class="room.id"
                        lg="1"
                        md="4"
                        sm="12"
                        cols="12"
                        v-for="(room, index) in availableRooms"
                        :key="index"
                      >
                        <v-card
                          :elevation="0"
                          class="ma-0 px-md-1 py-md-2"
                          :class="getRelaventColor(room.status)"
                          dark
                          ><div class="text-center">
                            {{ caps(room.room_type.name) }}
                          </div>
                          <div class="text-center">
                            {{ room.room_no }}
                          </div>
                        </v-card>
                      </v-col>
                    </v-row>
                  </div>
                  <div class="col-lg-3 pt-0">
                    <div class="row">
                      <div class="col-6">
                        <div class="bg-dark p-10 text-white text-center">
                          <i class="mdi mdi-account fs-3 mb-1 font-16"></i>
                          <h5 class="mb-0 mt-1">2540</h5>
                          <small class="font-light">Total Users</small>
                        </div>
                      </div>
                      <div class="col-6">
                        <div class="bg-dark p-10 text-white text-center">
                          <i class="mdi mdi-plus fs-3 font-16"></i>
                          <h5 class="mb-0 mt-1">120</h5>
                          <small class="font-light">New Users</small>
                        </div>
                      </div>
                      <div class="col-6 mt-3">
                        <div class="bg-dark p-10 text-white text-center">
                          <i class="mdi mdi-cart fs-3 mb-1 font-16"></i>
                          <h5 class="mb-0 mt-1">656</h5>
                          <small class="font-light">Total Shop</small>
                        </div>
                      </div>
                      <div class="col-6 mt-3">
                        <div class="bg-dark p-10 text-white text-center">
                          <i class="mdi mdi-tag fs-3 mb-1 font-16"></i>
                          <h5 class="mb-0 mt-1">9540</h5>
                          <small class="font-light">Total Orders</small>
                        </div>
                      </div>
                      <div class="col-6 mt-3">
                        <div class="bg-dark p-10 text-white text-center">
                          <i class="mdi mdi-table fs-3 mb-1 font-16"></i>
                          <h5 class="mb-0 mt-1">100</h5>
                          <small class="font-light">Pending Orders</small>
                        </div>
                      </div>
                      <div class="col-6 mt-3">
                        <div class="bg-dark p-10 text-white text-center">
                          <i class="mdi mdi-web fs-3 mb-1 font-16"></i>
                          <h5 class="mb-0 mt-1">8540</h5>
                          <small class="font-light">Online Orders</small>
                        </div>
                      </div>
                    </div>
                  </div>
                  <!-- column -->
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import ReservationList from "../components/reservation/ReservationList.vue";
export default {
  components: { ReservationList },
  data() {
    return {
      temp: "",
      loading: false,
      snackbar: false,
      response: "",
      isDirty: true,
      payingAdvance: false,

      checkInDialog: false,
      checkOutDialog: false,
      postingDialog: false,
      viewPostingDialog: false,
      cancelDialog: false,

      formTitle: "",
      selectedItem: 0,
      showMenu: false,
      bookingStatus: "",
      eventStatus: "",
      x: 0,
      y: 0,

      elevations: [6, 12, 18],
      first_login_auth: 1,
      loading: true,

      logs: [],

      total_items: [],
      items_by_cities: [],
      test_headers: [
        {
          text: "Customer",
          align: "left",
          sortable: false,
          value: "company_name"
        },
        {
          text: "Order Total",
          align: "left",
          sortable: false,
          value: "order_total"
        }
      ],
      orders: "",
      products: "",
      customers: "",
      daily_orders: "",
      weekly_orders: "",
      monthly_orders: "",
      evenIid: "",
      eventStatus: "",
      rooms: [],
      postings: [],
      notAvailableRooms: [],
      availableRooms: [],
      confirmedBooking: "",
      waitingBooking: "",
      reason: "",
      new_payment: 0,
      new_advance: 0,
      items: [],
      checkData: {},
      customerId: "",
      bookingId: "",
      document: null,
      posting: {
        item: "",
        qty: "",
        amount: 0,
        bill_no: "",
        amount_with_tax: 0,
        tax: 0,
        sgst: 0,
        cgst: 0,
        tax_type: -1
      },
      isDbCLick: false,
      members: {
        adult: 0,
        child: 0,
        baby: 0
      },
      expectCheckIn: "",
      expectCheckOut: "",
      headers: [
        { text: "#" },
        { text: "Bill Number" },
        { text: "Room No" },
        { text: "Room Type" },
        { text: "Customer" },
        { text: "Item" },
        { text: "QTY" },
        { text: "Amount" },
        { text: "Date" }
      ]
    };
  },
  watch: {
    checkInDialog() {
      this.formTitle = "Check In";
      this.get_data();
    },

    postingDialog() {
      this.formTitle = "Posting";
      this.get_data();
    },

    checkOutDialog() {
      this.formTitle = "Check Out";
      this.get_data();
    },

    viewPostingDialog() {
      this.formTitle = "View Post";
      this.get_posting();
    },

    payingAdvance() {
      this.formTitle = "Advance Payment";
      this.get_data();
    }
  },
  created() {
    this.room_list();
    this.first_login_auth = this.$auth.user.first_login;
  },

  methods: {
    caps(str) {
      if (str == "" || str == null) {
        return "---";
      } else {
        let res = str.toString();
        return res.replace(/\b\w/g, c => c.toUpperCase());
      }
    },

    mouseOver(bookedRoomId, bookingStatus) {
      this.evenIid = bookedRoomId;
      this.bookingStatus = bookingStatus;
      console.log(this.evenIid);
    },

    show(e) {
      e.preventDefault();
      this.get_data();
      this.x = e.clientX;
      this.y = e.clientY;
      this.$nextTick(() => {
        this.showMenu = true;
      });
    },

    get_remaining(val) {
      let total = this.checkData.remaining_price;
      let advance_price = val;
    },

    get_posting() {
      let id = this.evenIid;
      let payload = {
        params: {
          company_id: this.$auth.user.company.id
        }
      };
      this.$axios.get(`posting/${id}`, payload).then(({ data }) => {
        this.postings = data;
      });
    },

    get_amount_with_tax(clause) {
      let per = clause == "Food" ? 5 : 12;
      let res = this.getPercentage(this.posting.amount, per);
      let gst = parseInt(res) / 2;
      this.posting.sgst = gst;
      this.posting.cgst = gst;
      this.posting.tax = res;
      this.posting.amount_with_tax =
        parseInt(res) + parseInt(this.posting.amount);
    },

    getPercentage(amount, clause) {
      return (amount / 100) * clause;
    },

    room_list() {
      let payload = {
        params: {
          company_id: this.$auth.user.company.id,
          check_in: new Date().toJSON().slice(0, 10)
        }
      };
      this.$axios.get(`room_list_grid`, payload).then(({ data }) => {
        this.rooms = data;

        this.notAvailableRooms = data.notAvailableRooms;
        this.availableRooms = data.availableRooms;
        this.confirmedBooking = data.confirmedBooking;
        this.waitingBooking = data.waitingBooking;

        this.expectCheckIn = data.expectCheckIn;
        this.expectCheckOut = data.expectCheckOut;

        this.members = {
          ...data.members
        };

        console.log(this.notAvailableRooms.length);
      });
    },

    dblclick() {
      this.isDbCLick = true;
      this.get_data();
    },

    getRelaventColor(status) {
      switch (parseInt(status)) {
        case 1:
          return "booked";
        case 2:
          return "checkedIn";
        case 3:
          return "checkedOut";
        case 4:
          return "background";
        case 5:
          return "grey";
        default:
          return "available";
      }
    },

    viewBillingDialog() {
      let id = this.customerId;
      this.$router.push(`/customer/details/${id}`);
    },

    get_data(jsEvent = null) {
      let payload = {
        params: {
          id: this.evenIid
        }
      };
      this.$axios.get(`get_booking`, payload).then(({ data }) => {
        this.checkData = data;
        this.bookingId = data.id;
        this.checkData.full_payment = "";
        this.bookingStatus = data.booking_status;
        this.customerId = data.customer_id;
        console.log(this.checkData);
        if (this.isDbCLick) {
          this.get_event_by_db_click();
        }
      });
    },

    get_event_by_db_click() {
      this.$router.push(`/customer/details/${this.bookingId}`);
    },

    store_check_in(data) {
      if (
        // this.new_payment == "" ||
        // this.new_payment == 0 ||
        data.document ? "" : this.document == null
      ) {
        alert("Enter required fields");
        return;
      }
      this.loading = true;
      let bookingId = data.id;
      let payload = {
        new_payment: this.new_payment,
        booking_id: data.id,
        remaining_price: data.remaining_price,
        payment_mode_id: data.payment_mode_id
      };
      this.$axios
        .post("/check_in_room", payload)
        .then(({ data }) => {
          if (!data.status) {
            this.errors = data.errors;
          } else {
            this.succuss(data, true, false);
            data.document ? "" : this.store_document(bookingId);
          }
        })
        .catch(e => console.log(e));
    },

    store_posting() {
      let rule =
        Object.keys(this.posting.item).length == 0 ||
        Object.keys(this.posting.amount).length == 0 ||
        Object.keys(this.posting.qty).length == 0 ||
        Object.keys(this.posting.bill_no).length == 0 ||
        this.posting.tax_type == -1;

      if (rule) {
        alert("Please enter required fields");
        return;
      }
      this.loading = true;
      let per = this.posting.tax_type == "Food" ? 5 : 12;
      let payload = {
        ...this.posting,
        booked_room_id: this.evenIid,
        company_id: this.$auth.user.company.id,
        booking_id: this.checkData.id,
        room_id: this.checkData.room_id,
        room: this.checkData.room_no,
        tax_type: per
      };

      this.$axios
        .post("/posting", payload)
        .then(({ data }) => {
          if (!data.status) {
            this.errors = data.errors;
          } else {
            this.succuss(data, false, true);
          }
        })
        .catch(e => console.log(e));
    },

    store_advance(data) {
      if (this.new_advance == "") {
        alert("Enter advance amount");
        return;
      }
      // this.loading = true;
      let payload = {
        new_advance: this.new_advance,
        booking_id: data.id,
        remaining_price: data.remaining_price,
        payment_mode_id: data.payment_mode_id,
        company_id: this.$auth.user.company.id
      };
      this.$axios
        .post("/paying_advance", payload)
        .then(({ data }) => {
          if (!data.status) {
            this.errors = data.errors;
          } else {
            this.succuss(data, false, false, false, true);
          }
        })
        .catch(e => console.log(e));
    },

    setAvailable() {
      let payload = {
        cancel_by: this.$auth.user.id
      };
      this.$axios
        .post(`set_available/${this.evenIid}`, payload)
        .then(({ data }) => {
          if (!data.status) {
            this.snackbar = data.status;
            this.response = data.message;
            return;
          }
          this.room_list();
          this.cancelDialog = false;
          this.snackbar = data.status;
          this.response = data.message;
        })
        .catch(err => console.log(err));
    },

    setMaintenance() {
      let payload = {
        cancel_by: this.$auth.user.id
      };
      this.$axios
        .post(`set_maintenance/${this.evenIid}`, payload)
        .then(({ data }) => {
          if (!data.status) {
            this.snackbar = data.status;
            this.response = data.message;
            return;
          }
          this.room_list();
          this.cancelDialog = false;
          this.snackbar = data.status;
          this.response = data.message;
        })
        .catch(err => console.log(err));
    },

    cancelItem() {
      if (this.reason == "") {
        alert("Enter reason");
        return;
      }

      let payload = {
        reason: this.reason,
        cancel_by: this.$auth.user.id
      };
      this.$axios
        .post(`cancel_room/${this.evenIid}`, payload)
        .then(({ data }) => {
          if (!data.status) {
            this.snackbar = data.status;
            this.response = data.message;
            return;
          }
          this.room_list();
          this.cancelDialog = false;
          this.snackbar = data.status;
          this.response = data.message;
        })
        .catch(err => console.log(err));
    },

    store_check_out() {
      // if (this.checkData.full_payment == "") {
      //   alert("enter full payment");
      //   return true;
      // }

      this.loading = true;
      let payload = {
        booking_id: this.checkData.id,
        remaining_price: this.checkData.remaining_price,
        full_payment: this.checkData.full_payment,
        payment_mode_id: this.checkData.payment_mode_id
      };
      // return;
      this.$axios
        .post("/check_out_room", payload)
        .then(({ data }) => {
          if (!data.status) {
            this.errors = data.errors;
          } else {
            this.succuss(data, false, false, true);
            this.redirect_to_invoice(data.data);
          }
        })
        .catch(e => console.log(e));
    },
    setAvailable() {
      let payload = {
        cancel_by: this.$auth.user.id
      };
      this.$axios
        .post(`set_available/${this.evenIid}`, payload)
        .then(({ data }) => {
          if (!data.status) {
            this.snackbar = data.status;
            this.response = data.message;
            return;
          }
          this.room_list();
          this.cancelDialog = false;
          this.snackbar = data.status;
          this.response = data.message;
        })
        .catch(err => console.log(err));
    },
    preview(file) {
      let element = document.createElement("a");
      element.setAttribute("target", "_blank");
      element.setAttribute("href", file);
      document.body.appendChild(element);
      element.click();
      // document.body.removeChild(element);
    },
    succuss(
      data,
      check_in = false,
      posting = false,
      check_out = false,
      advance_payment = false
    ) {
      if (check_in) {
        this.checkData = {};
        this.checkInDialog = false;
      }
      if (check_out) {
        this.checkData = {};
        this.checkOutDialog = false;
      }
      if (posting) {
        this.posting = {};
        this.postingDialog = false;
      }

      if (advance_payment) {
        this.checkData = {};
        this.payingAdvance = false;
      }

      this.room_list();
      this.errors = [];
      this.loading = false;
      this.snackbar = true;
      this.response = data.message;

      console.log(this.snackbar);
      console.log(data.message);
    },

    close() {
      this.checkInDialog = false;
    }
  }
};
</script>
