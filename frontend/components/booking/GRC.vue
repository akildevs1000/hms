<template>
  <div>
    {{ data }}
  </div>
</template>
<script>
export default {
  props: ["BookingData"],
  data() {
    return {
      isDiscount: false,
      snackbar: false,
      response: "",
      preloader: false,
      loading: false,
      new_payment: 0,
      reference: "",
      totalTransactionAmount: 0,
      data: [],
      errors: [],
      checkOutDialog: false,
    };
  },

  watch: {
    BookingData() {
      this.get_booking();
    },
  },

  created() {
    this.preloader = false;
  },

  mounted() {
    this.get_booking();
  },

  computed: {},

  methods: {
    get_booking() {
      let id = this.BookingData.id;
      let payload = {
        params: {
          company_id: this.$auth.user.company.id,
        },
      };
      this.$axios
        .get(`grc_by_checkin/${id}`, payload)
        .then(({ data }) => {
          this.data = data.booking;
          console.log(this.data);
        });
    },

    store_advance(data) {
      if (this.new_payment == "") {
        alert("Enter payment amount");
        return;
      }
      this.loading = true;
      let payload = {
        new_advance: this.new_payment,
        reference_number: this.reference,
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
            this.loading = false;
          } else {
            this.new_payment = 0;
            this.get_transaction();
            this.closeDialog(data);
            this.loading = false;
          }
        })
        .catch((e) => console.log(e));
    },

    closeDialog(data) {
      this.$emit("close-dialog", data);
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

<style scoped>
* {
  box-sizing: border-box;
}

.m-1 {
  margin: 0.25rem;
}

.m-2 {
  margin: 0.5rem;
}

.m-3 {
  margin: 1rem;
}

.mt-2 {
  margin-top: 0.5rem;
}

.mt-3 {
  margin-top: 1rem;
}

.mr-1 {
  margin-right: 0.25rem;
}

.ml-3 {
  margin-left: 1rem;
}

.mx-4 {
  margin-right: 1.5rem;
  margin-left: 1.5rem;
}

.my-5 {
  margin-top: 2.5rem;
  margin-bottom: 2.5rem;
}

.pr-1 {
  padding-right: 0.25rem;
}

.pt-2 {
  padding-top: 0.5rem;
}

.pl-3 {
  padding-left: 1rem;
}

.px-4 {
  padding-right: 1.5rem;
  padding-left: 1.5rem;
}

.py-5 {
  padding-top: 2.5rem;
  padding-bottom: 2.5rem;
}

.row::after {
  content: "";
  clear: both;
  display: table;
}

.col {
  width: 5%;
  float: left;
  padding: 5px;
}


.col-1 {
  width: 8.33%;
  float: left;
  padding: 5px;
}

.col-2 {
  width: 16.66%;
  float: left;
  padding: 5px;
}

.col-3 {
  width: 24.99%;
  float: left;
  padding: 5px;
}

.col-4 {
  width: 33.32%;
  float: left;
  padding: 5px;
}

.col-5 {
  width: 41.65%;
  float: left;
  padding: 5px;
}

.col-6 {
  width: 49.98%;
  float: left;
  padding: 5px;
}

.col-7 {
  width: 58.31%;
  float: left;
  padding: 5px;
}

.col-8 {
  width: 66.64%;
  float: left;
  padding: 5px;
}

.col-9 {
  width: 74.97%;
  float: left;
  padding: 5px;
}

.col-10 {
  width: 83.3%;
  float: left;
  padding: 5px;
}

.col-11 {
  width: 91.63%;
  float: left;
  padding: 5px;
}

.col-12 {
  width: 100%;
  float: left;
  padding: 5px;
}

.form-input {
  width: 100%;
  padding: 2px 5px;
  border-radius: 0px;
  resize: vertical;
  outline: 0;
}

.label-txt {
  font-size: 14px
}

input {
  /* border: none; */
  /* border-bottom: 1px solid black; */
  padding: 5px 10px;
  outline: none;
  font-size: 13px
}

hr {
  position: relative;
  border: none;
  height: 1px;
  background: rgb(167, 164, 164);
}

.terms {
  font-size: 12px;
  font-family: Arial, Helvetica, sans-serif
}

.header-txt {
  font-size: 20px;
  font-weight: bolder;
  font-family: Arial, Helvetica, sans-serif;
  margin: 0;
  padding: 0;
}

.header-txt-span {
  font-size: 12px;
  font-weight: bolder;
  font-family: Arial, Helvetica, sans-serif;
  margin: 0;
  padding: 0;
  text-align: center;
}

.header-txt-address {
  font-size: 12px;
  font-weight: bolder;
  font-family: Arial, Helvetica, sans-serif;
  margin: 0px;
  padding: 0px;
}

table {
  font-family: arial, sans-serif;
  border-collapse: collapse;
  width: 100%;
  border: 1px solid #e9e9e9;
  width: 50%;
  margin: auto;
}

td,
th {
  font-size: 12px;
  text-align: left;
  padding: 2px 2px;
  border: 1px solid #e9e9e9;
}
</style>
