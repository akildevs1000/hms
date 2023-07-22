<template>
  <div>
    <div class="text-center ma-2">
      <v-snackbar v-model="snackbar" top="top" color="secondary" elevation="24">
        {{ response }}

      </v-snackbar>
    </div>


    this from venu branch

    <v-row>
      <v-col cols="12">
        <FullCalendar :options="calendarOptions" style="background: #fff" />
      </v-col>
    </v-row>
  </div>
</template>
<script>
import FullCalendar from "@fullcalendar/vue";
import dayGridPlugin from "@fullcalendar/daygrid";
import interactionPlugin from "@fullcalendar/interaction";
import resourceTimelinePlugin from "@fullcalendar/resource-timeline";

export default {
  components: {
    FullCalendar,
  },
  data() {
    return {
      dateConfirmationDialog: false,
      LoadingDialog: false,
      loading: false,
      snackbar: false,
      response: "",
      isDirty: true,
      createReservationDialog: false,
      payingAdvance: false,
      checkInDialog: false,
      checkOutDialog: false,
      postingDialog: false,
      viewPostingDialog: false,
      cancelDialog: false,
      formTitle: "",
      selectedItem: 0,
      showMenu: false,
      showTooltip: false,
      tx: 0,
      ty: 0,
      x: 0,
      y: 0,
      calendarOptions: {
        plugins: [interactionPlugin, dayGridPlugin, resourceTimelinePlugin],
        // now: "2022-11-07",
        now: "",
        editable: true,
        aspectRatio: 1.8,
        scrollTime: "00:00",
        displayEventTime: false,
        selectable: true,
        initialView: "resourceTimelineMonth",
        resourceAreaWidth: "12%",
        eventResizableFromStart: true, // enables resizing from the start of the event
        slotEventOverlap: false, // allows events to overlap time slots
        selectOverlap: false, // allows events to overlap time slots
        navLinks: true,
        resourceAreaWidth: "12%",

        groupByResource: false, // display each day as a separate column
        slotDuration: { days: 1 }, // set the duration of each time slot to one day


        headerToolbar: {
          left: 'prev,next today',
          center: 'title',
          right: 'resourceTimelineMonth,resourceTimelineWeek,resourceTimelineDay' // add the resource timeline views to the header toolbar
        },


        // slotDuration: '00:30:00',
        // slotEventOverlap: true,
        // slotLabelInterval: '01:00',
        // slotLabelFormat: {
        //   hour: 'numeric',
        //   minute: '2-digit',
        //   hour12: true
        // },
        // slotMinTime: '00:00:00',
        // slotMaxTime: '24:00:00',

        resourceAreaColumns: [
          {
            headerContent: "Room",
            field: "room_no",
            width: "3%",
          },
          {
            headerContent: "Room Type",
            field: "room_type",
            width: "5%",
          },
        ],
        resources: [
          { id: "103", room_no: "103", room_type: "king", eventColor: "green" },
          { id: "104", room_no: "104", eventColor: "orange" }
        ],
        events: [

          {
            start: '2023-04-30T18:00:00',
            end: '2023-05-01T18:00:00',
            rendering: 'background',
            overlap: false,
            // display: 'inverse-background',
            // resourceIds: ['room1', 'room2', 'room3']
            resourceId: "104",
          },

          {
            id: "1",
            room_id: "1",
            resourceId: "104",
            start: '2023-04-30T18:00:00',
            end: '2023-05-01T18:00:00',
            title: "e",
            background: "linear-gradient(135deg, #23bdb8 0, #65a986 100%)",
            eventBorderColor: "red",
          },
          {
            id: "1",
            room_id: "1",
            resourceId: "104",
            start: "2023-05-20 01:00:00",
            end: "2023-05-22 23:00:00",
            title: "e",
          },
          {
            id: "1",
            room_id: "1",
            resourceId: "103", start: '2023-05-01T09:00:00', end: '2023-05-01T12:00:00',
            title: "e",
          },

          //   resources: [
          //   { id: 'a', title: 'Resource A' },
          //   { id: 'b', title: 'Resource B' },
          //   { id: 'c', title: 'Resource C' }
          // ],
          // events: [
          //   { resourceId: 'a', title: 'Event 1', start: '2023-05-01T09:00:00', end: '2023-05-01T12:00:00' },
          //   { resourceId: 'b', title: 'Event 2', start: '2023-05-03T10:00:00', end: '2023-05-03T14:00:00' },
          //   { resourceId: 'c', title: 'Event 3', start: '2023-05-05T12:00:00', end: '2023-05-05T16:00:00' }
          // ]

        ],


        eventDidMount: (arg) => {
          const eventId = arg.event.id;
          const bookingStatus = arg.event.extendedProps.status;
          if (arg.event.extendedProps.background) {
            arg.el.style.background = arg.event.extendedProps.background;
          }

          arg.el.addEventListener("contextmenu", (jsEvent) => {
            this.showTooltip = false;
            this.show(eventId, jsEvent);
            jsEvent.preventDefault();
          });

          // arg.el.addEventListener("mouseover", jsEvent => {
          //   this.get_event_by_mouse_hover(eventId, jsEvent);
          // });

          arg.el.addEventListener("dblclick", (jsEvent) => {
            this.evenIid = eventId;
            this.isDbCLick = true;
            this.get_data();
          });

          arg.el.addEventListener("mouseleave", (jsEvent) => {
            this.showTooltip = false;
          });
        },

        select: (date, jsEvent, view, resourceObj, vv) => {
          let obj = date.resource.extendedProps;
          if (date.startStr < this.currentDate) {
            this.alert(
              "Missing!",
              "Please Select Current Date or Future Date",
              "error"
            );
            return;
          }
          this.create_reservation(date, obj);
        },

        eventResize: (arg, delta) => {
          let obj = {
            eventId: arg.event.id,
            start: this.convert_date_format(arg.event.start),
            end: this.convert_date_format(arg.event.end),
            roomId: arg.event._def.resourceIds[0],
            user_id: this.$auth.user.id,
          };

          this.change_date_by_drag(obj);
        },

        eventDrop: (arg, delta) => {
          let obj = {
            eventId: arg.event.id,
            start: this.convert_date_format(arg.event.start),
            end: this.convert_end_date_format(arg.event.start, arg.event.end),
            company_id: this.$auth.user.company.id,
            roomId: arg.event._def.resourceIds[0],
          };
          if (obj.start < this.currentDate) {
            this.alert(
              "Missing!",
              "Please choose Current Date or Future Date",
              "error"
            );
            this.get_events();
            return;
          }
          this.change_room_by_drag(obj);
        },
      },
      headers: [
        { text: "#" },
        { text: "Bill Number" },
        { text: "Room No" },
        { text: "Room Type" },
        { text: "Customer" },
        { text: "Item" },
        { text: "QTY" },
        { text: "Amount" },
        { text: "Date" },
      ],
      errors: [],
      data: [],
      postings: [],
      checkData: {},
      evenIid: "",
      bookingStatus: "",
      checkInDate: "",
      reason: "",
      customerId: "",
      bookingId: "",
      cancelLoad: false,
      document: null,
      new_payment: 0,
      new_advance: 0,
      reservation: {},
      RoomList: [],
      reservation: {
        check_in: null,
        check_out: null,
        room_no: "",
        room_id: "",
        room_type: "",
        price: "",
        origin_price: "",
        room_id: "",
        isCalculate: false,
      },
      isDbCLick: false,
      totalTransactionAmount: 0,
      transactions: [],
    };
  },

  created() {
    this.currentDate;
  },

  mounted() {
    this.room_list();
    // this.get_events();
    document.querySelector(".fc-license-message").style.display = "none";
  },

  watch: {
    checkInDialog() {
      this.formTitle = "Check In";
      this.new_payment = 0;
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
      this.new_advance = 0;
      this.get_data();
    },
  },
  computed: {
    getBalance() {
      let full_pay = this.checkData.full_payment;
      let remainingPrice = this.checkData.remaining_price;
      let balance = full_pay - remainingPrice;
      if (balance >= 0) {
        return balance;
      }
    },

    currentDate() {
      return this.calendarOptions.now = "2023-05-01";
      // return (this.calendarOptions.now = new Date().toJSON().slice(0, 10));
      return this.calendarOptions.now = new Date(Date.now() - new Date().getTimezoneOffset() * 60000)
        .toISOString()
        .substr(0, 10);
    },
  },
  methods: {

    onSelect(start, end, jsEvent, view) {
      // Check if start and end are in different months
      if (start.month() !== end.month()) {
        // Add one day to end to include the last day of the selected month
        end.add(1, 'day')

        // Call the select method to select the full date range
        this.$refs.calendar.select({
          start: start.format(),
          end: end.format()
        })
      }
    },

    caps(str) {
      if (str == "" || str == null) {
        return "---";
      } else {
        let res = str.toString();
        return res.replace(/\b\w/g, (c) => c.toUpperCase());
      }
    },

    getDateOnly(date) {
      let [dateOnly] = date.split(' ');
      return dateOnly;
    },

    show(id, jsEvent) {
      this.LoadingDialog = true;
      this.evenIid = id;
      this.get_data(jsEvent);
    },

    show_context_menu(jsEvent) {
      if (!jsEvent) {
        return;
      }
      this.LoadingDialog = false;
      this.x = jsEvent.clientX;
      this.y = jsEvent.clientY;
      this.$nextTick(() => {
        this.showMenu = true;
      });
    },

    get_events() {
      let payload = {
        params: {
          company_id: this.$auth.user.company.id,
        },
      };
      this.$axios.get(`events_list`, payload).then(({ data }) => {
        this.calendarOptions.events = data;
        return;
      });
    },

    get_check_out() {
      this.checkOutDialog = true;
      this.get_transaction();
    },

    get_transaction() {
      let id = this.bookingId;
      let payload = {
        params: {
          company_id: this.$auth.user.company.id,
        },
      };
      this.$axios
        .get(`get_transaction_by_booking_id/${id}`, payload)
        .then(({ data }) => {
          this.transactions = data.transactions;
          this.totalTransactionAmount = data.totalTransactionAmount;
        });
    },

    get_data(jsEvent = null) {
      let payload = {
        params: {
          id: this.evenIid,
          company_id: this.$auth.user.company.id,
        },
      };
      this.$axios.get(`get_booking`, payload).then(({ data }) => {
        this.checkData = data;
        this.bookingId = data.id;
        this.checkData.full_payment = "";
        this.bookingStatus = data.booking_status;
        this.checkInDate = data.check_in;
        this.customerId = data.customer_id;
        this.show_context_menu(jsEvent);
        if (this.isDbCLick) {
          this.get_event_by_db_click();
        }
      });
    },

    get_event_by_mouse_hover(id, jsEvent) {
      this.evenIid = id;
      this.tx = jsEvent.clientX;
      this.ty = jsEvent.clientY;
      this.$nextTick(() => {
        this.showTooltip = true;
      });
      this.get_data();
    },

    get_event_by_db_click() {
      this.$router.push(`/customer/details/${this.bookingId}`);
    },

    create_reservation(e, obj) {
      this.get_room_types(e, obj);
    },

    get_room_types(e, obj) {
      this.reservation.isCalculate = true;
      this.reservation.room_id = this.RoomList.find(
        (e) => e.room_no == obj.room_no
      ).id;
      this.reservation.room_type = obj.room_type;
      this.reservation.room_no = obj.room_no;
      this.reservation.check_in = e.startStr;

      this.reservation.check_out = e.endStr;

      let payload = {
        params: {
          company_id: this.$auth.user.company.id,
          roomType: obj.room_type,
          room_no: obj.room_no,
          checkin: this.reservation.check_in,
          checkout: this.reservation.check_out,
        },
      };
      this.$axios
        .get(`get_data_by_select_with_tax`, payload)
        .then(({ data }) => {
          this.reservation.room_id = data.room.id;
          this.reservation.price = data.total_price;
          this.reservation.priceList = data.data;
          this.reservation.total_tax = data.total_tax;
          let commitObj = {
            ...this.reservation,
          };
          this.$store.commit("reservation", commitObj);
          this.$router.push(`/hotel/new2`);
        });
    },

    convert_checkout_date_format(val) {
      const previous = new Date(val.getTime());
      previous.setDate(val.getDate() - 1);
      const date = new Date(previous);
      const year = date.getFullYear();
      const month = String(date.getMonth() + 1).padStart(2, "0");
      const day = String(date.getDate()).padStart(2, "0");
      return [year, month, day].join("-");
    },

    viewBillingDialog() {
      let id = this.bookingId;
      this.$router.push(`/customer/details/${id}`);
    },

    get_remaining(val) {
      let total = this.checkData.remaining_price;
      let advance_price = val;
    },

    room_list() {
      let payload = {
        params: {
          company_id: this.$auth.user.company.id,
        },
      };
      this.$axios.get(`room_list`, payload).then(({ data }) => {
        this.calendarOptions.resources = data;
        this.RoomList = data;
      });
    },

    get_posting() {
      let id = this.evenIid;
      let payload = {
        params: {
          company_id: this.$auth.user.company.id,
        },
      };
      this.$axios.get(`posting/${id}`, payload).then(({ data }) => {
        this.postings = data;
      });
    },

    convert_date_format(val) {
      return new Date(val - new Date().getTimezoneOffset() * 60000)
        .toISOString()
        .substr(0, 10);
    },

    get_next_day(val) {
      const tomorrow = new Date(val);
      tomorrow.setDate(tomorrow.getDate() + 1);
      let tmrew = tomorrow.toISOString().substr(0, 10);
      return tmrew;
    },

    convert_end_date_format(start, end) {
      if (end == null) {
        return this.get_next_day(start);
      }
      return this.get_next_day(end);
    },

    store_advance(data) {
      if (this.new_advance == "") {
        alert("Enter advance amount");
        return;
      }
      let payload = {
        new_advance: this.new_advance,
        booking_id: data.id,
        remaining_price: data.remaining_price,
        payment_mode_id: data.payment_mode_id,
        company_id: this.$auth.user.company.id,
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
        .catch((e) => console.log(e));
    },

    store_document(id) {
      let payload = new FormData();
      payload.append("document", this.document);
      payload.append("booking_id", id);
      this.$axios
        .post("/store_document", payload)
        .then(({ data }) => {
          this.loading = false;
          if (!data.status) {
            this.errors = data.errors;
          }
        })
        .catch((e) => console.log(e));
    },

    setAvailable() {
      let payload = {};

      this.$axios
        .post(`set_available/${this.bookingId}`, payload)
        .then(({ data }) => {
          if (!data.status) {
            this.snackbar = data.status;
            this.response = data.message;
            return;
          }
          this.get_events();
          this.cancelDialog = false;
          this.snackbar = data.status;
          this.response = data.message;
        })
        .catch((err) => console.log(err));
    },

    setMaintenance() {
      let payload = {
        cancel_by: this.$auth.user.id,
      };
      this.$axios
        .post(`set_maintenance/${this.bookingId}`, payload)
        .then(({ data }) => {
          if (!data.status) {
            this.snackbar = data.status;
            this.response = data.message;
            return;
          }
          this.get_events();
          this.cancelDialog = false;
          this.snackbar = data.status;
          this.response = data.message;
        })
        .catch((err) => console.log(err));
    },

    cancelItem() {
      if (this.reason == "") {
        alert("Enter reason");
        return;
      }
      this.cancelLoad = true;

      let payload = {
        reason: this.reason,
        cancel_by: this.$auth.user.id,
      };
      this.$axios
        .post(`cancel_room/${this.evenIid}`, payload)
        .then(({ data }) => {
          if (!data.status) {
            this.snackbar = data.status;
            this.response = data.message;
            this.cancelLoad = false;
            return;
          }
          this.get_events();
          this.cancelLoad = false;
          this.cancelDialog = false;
          this.reason = "";
          this.snackbar = data.status;
          this.response = data.message;
        })
        .catch((err) => console.log(err));
    },

    change_room_by_drag(obj) {
      this.$axios
        .post("/change_room_by_drag", obj)
        .then(({ data }) => {
          this.loading = false;

          if (!data.status) {
            this.errors = data.errors;
          } else {
            this.errors = [];
            this.snackbar = data.message;
            this.response = data.message;
            this.get_events();
          }
        })
        .catch((e) => console.log(e));
    },

    change_date_by_drag(obj) {
      this.$axios
        .post("/change_date_by_drag", obj)
        .then(({ data }) => {
          this.loading = false;

          if (!data.status) {
            this.errors = data.errors;
          } else {
            this.errors = [];
            this.snackbar = data.message;
            this.response = data.message;
            this.get_events();
          }
        })
        .catch((e) => console.log(e));
    },

    close() {
      this.checkInDialog = false;
      this.new_payment = 0;
      this.checkOutDialog = false;
    },

    alert(title = "Success!", message = "hello", type = "error") {
      this.$swal(title, message, type);
    },
    closeDialogs(res) {
      this.succuss(res);
    },

    succuss(
      data,
      check_in = true,
      posting = true,
      check_out = true,
      advance_payment = true
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

      this.get_events();
      this.errors = [];
      this.loading = false;
      this.snackbar = true;
      this.response = data.message;
    },
  },
};
</script>

<style scoped src="@/assets/custom/calendar.css"></style>
