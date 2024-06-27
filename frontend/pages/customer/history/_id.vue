<template>
  <div>
    <v-row>
      <v-col md="9">
        <customer-index :customer_id="customer_id" />
      </v-col>
    </v-row>
  </div>
</template>
<script>
import CustomerIndex from "../../../components/customer/CustomerIndex.vue";
export default {
  components: { CustomerIndex },
  data: () => ({
    tab1: null,

    tab: null,
    items: ["Contact", "Personal"],
    itemsCustomer: ["History", "Customer"],
    headers: [
      {
        text: "#",
      },
      {
        text: "Type",
      },
      {
        text: "Source",
      },
      {
        text: "Rooms",
      },
      {
        text: "Booking Date",
      },
      {
        text: "Check In",
      },
      {
        text: "Check Out",
      },
      {
        text: "Total Price",
      },
    ],
    pagination: {
      current: 1,
      total: 0,
      per_page: 10,
    },
    options: {},
    Model: "Customer",
    search: "",
    snackbar: false,
    dialog: false,
    ids: [],
    loading: false,
    document: null,
    response: "",
    customer: [],
    payments: [],
    bookings: [],
    revenue: 0,
    city_ledger: 0,
    errors: [],
    idCards: [],

    id_card_type_id: 2,
    countryList: [],
    customer: {
      nationality: "",
      title: "",
      first_name: "",
      last_name: "",
      contact_no: "",
      email: "",
      id_card_type_id: 2,
      id_card_no: "",
      car_no: "",
      no_of_adult: "",
      no_of_child: "",
      no_of_baby: "",
      address: "",
      company_id: "",

      dob_menu: false,
      dob: new Date(Date.now() - new Date().getTimezoneOffset() * 60000)
        .toISOString()
        .substr(0, 10),
    },

    titleItems: [
      { id: 1, name: "Mr" },
      { id: 2, name: "Mrs" },
      { id: 3, name: "Miss" },
      { id: 4, name: "Ms" },
      { id: 5, name: "Dr" },
    ],

    imgPath: "",
    image: "",
    customer_id: "",

    upload: {
      name: "",
    },

    previewImage: null,
  }),

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
  created() {
    this.loading = true;
    this.id_card_type_id = this.customer.id_card_type_id;
    this.getData();
    this.get_id_cards();
    this.get_countries();
  },
  mounted() {},

  methods: {
    onpick_attachment() {
      this.$refs.attachment_input.click();
    },

    attachment(e) {
      // this.customer.image = e.target.files[0] || "";

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

    getDate(dataTime) {
      return dataTime;
      // return new Date(dataTime.toDateString());
    },

    can(per) {
      let u = this.$auth.user;
      return (
        (u && u.permissions.some((e) => e.name == per || per == "/")) ||
        u.is_master
      );
    },

    get_customer_by_id() {
      this.$axios.get(`customer/${this.customer_id}`).then(({ data }) => {
        this.customer = data.data;
      });
    },

    preview(file) {
      let element = document.createElement("a");
      element.setAttribute("target", "_blank");
      element.setAttribute("href", file);
      document.body.appendChild(element);
      element.click();
      // document.body.removeChild(element);
    },

    getData() {
      let id = this.$route.params.id;
      this.$axios.get(`get_customer_history/${id}`).then(({ data }) => {
        this.customer_id = data.data.id;
        this.customer = data.data;
        this.bookings = data.data.bookings;
        this.revenue = data.revenue;
        this.city_ledger = data.city_ledger;
        // this.payments = data.booking.payments;
        // this.bookedRooms = data.booking.booked_rooms;
        this.loading = false;
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
          }
        })
        .catch((e) => console.log(e));
    },

    store_customer() {
      let payload = {
        ...this.customer,
        document: document,
        company_id: this.$auth.user.company.id,
        id_card_type_id: this.id_card_type_id,
      };
      // console.log(payload);
      // return;
      this.$axios
        .post("/customer_update", payload)
        .then(({ data }) => {
          this.loading = false;
          if (!data.status) {
            this.alert("oops!", "Some fields are missing or invalid", "error");
            this.subLoad = false;
            this.errors = data.errors;
          } else {
            this.errors = [];
            this.getData();
            this.alert("Success!", "Successfully customer updated", "success");
          }
        })
        .catch((e) => console.log(e));
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

    get_countries() {
      this.$axios.get(`get_countries`).then(({ data }) => {
        this.countryList = data;
      });
    },

    alert(title = "Success!", message = "hello", type = "error") {
      this.$swal(title, message, type);
    },
  },
};
</script>

<style scoped src="@/assets/css/custom.css"></style>
