<template>
  <div>
    <v-row>
      <v-col md="3">
        <div class="profile-view">
          <div
            class="cover-image"
            style="background-image: url('https://images.lastminutes.deals/hotels/IN/534702592.jpg');"
          ></div>
          <div class="profile-view-header">
            <div class="avatar-container">
              <img
                src="https://media.istockphoto.com/id/1309328823/photo/headshot-portrait-of-smiling-male-employee-in-office.jpg?b=1&s=170667a&w=0&k=20&c=MRMqc79PuLmQfxJ99fTfGqHL07EDHqHLWg0Tb4rPXQc="
                alt="Avatar"
                class="avatar"
              />
            </div>
            <h1 class="name">
              {{ customer.title || "---" }} {{ customer.full_name || "---" }}
            </h1>
            <div class=""></div>
          </div>

          <div class="profile-view-body ">
            <v-tabs v-model="tab" align-with-title>
              <v-tabs-slider color="yellow"></v-tabs-slider>
              <v-tab v-for="item in items" :key="item">
                {{ item }}
              </v-tab>
            </v-tabs>
            <v-tabs-items v-model="tab">
              <v-tab-item>
                <v-card flat>
                  <v-card-text>
                    <div class="contact-info-container">
                      <ul class="contact-info">
                        <li>
                          <span class="label">Email:</span>
                          <a href="mailto:john.doe@example.com">
                            {{ customer.email || "---" }}
                          </a>
                        </li>
                        <li>
                          <span class="label">Phone:</span>
                          {{ customer.contact_no || "---" }}
                        </li>
                        <li>
                          <span class="label">Phone:</span>
                          {{ customer.whatsapp || "---" }}
                        </li>
                        <li>
                          <span class="label">Address:</span>
                          {{ customer.address || "---" }}
                        </li>
                      </ul>
                    </div>
                  </v-card-text>
                </v-card>
              </v-tab-item>
              <v-tab-item>
                <v-card flat>
                  <v-card-text>
                    <div class="contact-info-container">
                      <ul class="contact-info">
                        <li>
                          <span class="label">Nationality:</span>
                          {{ customer.nationality || "---" }}
                        </li>

                        <li>
                          <span class="label">Car No:</span>
                          {{ customer.car_no || "---" }}
                        </li>

                        <li>
                          <span class="label">Adults:</span>
                          {{ customer.no_of_adult || "---" }}
                        </li>
                        <li>
                          <span class="label">Child:</span>
                          {{ customer.no_of_child || "---" }}
                        </li>
                        <li>
                          <span class="label">Babies:</span>
                          {{ customer.no_of_baby || "---" }}
                        </li>
                      </ul>
                    </div>
                  </v-card-text>
                </v-card>
              </v-tab-item>
            </v-tabs-items>
          </div>
        </div>
      </v-col>

      <v-col md="9">
        <v-row>
          <div class="col-xl-3 col-lg-6 text-uppercase">
            <div class="card px-2 available">
              <div class="card-statistic-3">
                <div class="card-icon card-icon-large">
                  <i class="fas fa-door-open"></i>
                </div>
                <div class="card-content">
                  <h4 class="card-title text-capitalize">Total Revenue</h4>
                  <span class="data-1"> Rs {{ revenue }} </span>
                  <p class="mb-0 text-sm">
                    <span class="mr-2">
                      <v-icon dark small>mdi-arrow-right</v-icon>
                    </span>
                    <a class="text-nowrap text-white" target="_blank">
                      <span class="text-nowrap">View Report</span>
                    </a>
                  </p>
                </div>
              </div>
            </div>
          </div>
          <div class="col-xl-3 col-lg-6 text-uppercase">
            <div class="card px-2 available">
              <div class="card-statistic-3">
                <div class="card-icon card-icon-large">
                  <i class="fas fa-door-open"></i>
                </div>
                <div class="card-content">
                  <h4 class="card-title text-capitalize">
                    Number of Time Visit
                  </h4>
                  <span class="data-1">
                    {{ bookings.length || "---" }}
                  </span>
                  <p class="mb-0 text-sm">
                    <span class="mr-2">
                      <v-icon dark small>mdi-arrow-right</v-icon>
                    </span>
                    <a class="text-nowrap text-white" target="_blank">
                      <span class="text-nowrap">View Report</span>
                    </a>
                  </p>
                </div>
              </div>
            </div>
          </div>
        </v-row>

        <table class="responsive-table">
          <thead>
            <tr>
              <th scope="col" v-for="(item, index) in headers" :key="index">
                {{ item.text }}
              </th>
            </tr>
          </thead>

          <tbody>
            <tr v-for="(item, index) in bookings" :key="index">
              <th scope="row">
                <b>{{ ++index }}</b>
              </th>
              <td scope="row">{{ item.type || "---" }}</td>
              <td data-title="Released">{{ item.source || "---" }}</td>
              <td data-title="Studio">{{ item.rooms || "---" }}</td>
              <td data-title="Worldwide Gross" data-type="currency">
                {{ item.booking_date || "---" }}
              </td>
              <td data-title="Domestic Gross" data-type="currency">
                {{ item.check_in || "---" }}
              </td>
              <td data-title="International Gross" data-type="currency">
                {{ item.check_out || "---" }}
              </td>
              <td data-title="Budget" data-type="currency">
                {{ item.total_price || "---" }}.00
              </td>
            </tr>
          </tbody>
        </table>
      </v-col>
    </v-row>
  </div>
</template>
<script>
export default {
  data: () => ({
    tab: null,
    items: ["Contact", "Personal"],
    headers: [
      {
        text: "#"
      },
      {
        text: "Type"
      },
      {
        text: "Source"
      },
      {
        text: "Rooms"
      },
      {
        text: "Booking Date"
      },
      {
        text: "Check In"
      },
      {
        text: "Check Out"
      },
      {
        text: "Total Price"
      }
    ],
    pagination: {
      current: 1,
      total: 0,
      per_page: 10
    },
    options: {},
    Model: "Customer",
    search: "",
    snackbar: false,
    dialog: false,
    ids: [],
    loading: false,
    response: "",
    customer: [],
    payments: [],
    bookings: [],
    revenue: "",
    errors: []
  }),

  computed: {},
  created() {
    this.loading = true;
    this.getData();
  },
  mounted() {},

  methods: {
    getDate(dataTime) {
      return dataTime;
      // return new Date(dataTime.toDateString());
    },
    can(per) {
      let u = this.$auth.user;
      return (
        (u && u.permissions.some(e => e.name == per || per == "/")) ||
        u.is_master
      );
    },

    getData() {
      let id = this.$route.params.id;
      this.$axios.get(`get_customer_history/${id}`).then(({ data }) => {
        this.customer = data.data;
        this.bookings = data.data.bookings;
        this.revenue = data.revenue;
        this.payments = data.booking.payments;
        this.bookedRooms = data.booking.booked_rooms;
        this.loading = false;
      });
    }
  }
};
</script>

<style scoped src="@/assets/dashtem.css"></style>

<style scoped>
.label {
  color: black;
}
/* CSS for the profile view template */
.profile-view {
  display: flex;
  flex-direction: column;
  align-items: center;
  width: 350px;
  /* border: 1px solid #ccc; */
  border-radius: 4px;
  /* box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); */
  overflow: hidden;
  background-color: #fff;
}

.cover-image {
  width: 100%;
  height: 300px;
  background-size: cover;
  background-position: center;
  position: relative;
}

.cover-image-overlay {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.2);
  display: flex;
  align-items: flex-end;
}

.profile-menu {
  display: flex;
  margin: 0 20px;
}

.profile-menu-item {
  font-size: 14px;
  color: #fff;
  text-decoration: none;
  margin-right: 20px;
  transition: color 0.2s;
}

.profile-menu-item:hover {
  color: #eee;
}

.profile-view-header {
  display: flex;
  align-items: center;
  padding: 20px;
  position: absolute;
  top: 264px;
}

.avatar-container {
  position: relative;
}

.avatar {
  width: 120px;
  height: 120px;
  border-radius: 50%;
  border: 2px solid #fff;
}

.name {
  font-size: 17px;
  font-weight: bold;
  margin: 0;
  margin-left: 20px;
  color: #777;
  text-transform: uppercase;
}

.location {
  font-size: 14px;
  color: #777;
  margin: 10px 0 0;
  position: fixed;
}

.profile-view-body {
  margin-top: 100px;
}

.bio-container {
  margin-bottom: 20px;
}

.bio-title {
  font-size: 18px;
  font-weight: bold;
  margin: 0;
}

.bio {
  font-size: 16px;
  line-height: 1.5;
  margin: 10px 0 0;
}

.contact-info-container {
  width: 300px;
  margin-bottom: 20px;
}

.contact-info-title {
  font-size: 18px;
  font-weight: bold;
  margin: 0;
}

.contact-info {
  list-style: none;
  margin: 10px 0 0;
  padding: 0;
}

.contact-info li {
  display: flex;
  align-items: center;
  margin-bottom: 10px;
}

/* Add some styling for the table container */
.table-container {
  width: 80%;
  margin: 0 auto;
  border-collapse: collapse;
}

/* Add some styling for the table cells */
.table-container td,
.table-container th {
  border: 1px solid #ccc;
  padding: 10px;
}

/* Add some styling for the table headings */
.table-container th {
  background-color: #00b8d4;
  color: #fff;
  font-weight: bold;
}

/* Add some styling for the table rows */
.table-container tr:nth-child(even) {
  background-color: #f2f2f2;
}

/* Add some styling for the table cells with data */
.table-container td {
  font-weight: bold;
  text-align: center;
}

/* ===================== */

a {
  color: #26890d;
}
a:hover,
a:focus {
  color: #046a38;
}

.container {
  margin: 5% 3%;
}
@media (min-width: 48em) {
  .container {
    margin: 2%;
  }
}
@media (min-width: 75em) {
  .container {
    margin: 2em auto;
    max-width: 75em;
  }
}

.responsive-table {
  width: 100%;
  margin-bottom: 1.5em;
  border-spacing: 0;
}
@media (min-width: 48em) {
  .responsive-table {
    font-size: 0.9em;
  }
}
@media (min-width: 62em) {
  .responsive-table {
    font-size: 1em;
  }
}
.responsive-table thead {
  position: absolute;
  clip: rect(1px 1px 1px 1px);
  /* IE6, IE7 */
  padding: 0;
  border: 0;
  height: 1px;
  width: 1px;
  overflow: hidden;
}
@media (min-width: 48em) {
  .responsive-table thead {
    position: relative;
    clip: auto;
    height: auto;
    width: auto;
    overflow: auto;
  }
}
.responsive-table thead th {
  background-color: #26890d;
  border: 1px solid #86bc25;
  font-weight: normal;
  text-align: center;
  color: white;
}
.responsive-table thead th:first-of-type {
  text-align: left;
}
.responsive-table tbody,
.responsive-table tr,
.responsive-table th,
.responsive-table td {
  display: block;
  padding: 0;
  text-align: left;
  white-space: normal;
}
@media (min-width: 48em) {
  .responsive-table tr {
    display: table-row;
  }
}
.responsive-table th,
.responsive-table td {
  padding: 0.5em;
  vertical-align: middle;
}
@media (min-width: 30em) {
  .responsive-table th,
  .responsive-table td {
    padding: 0.75em 0.5em;
  }
}
@media (min-width: 48em) {
  .responsive-table th,
  .responsive-table td {
    display: table-cell;
    padding: 0.5em;
  }
}
@media (min-width: 62em) {
  .responsive-table th,
  .responsive-table td {
    padding: 0.75em 0.5em;
  }
}
@media (min-width: 75em) {
  .responsive-table th,
  .responsive-table td {
    padding: 0.75em;
  }
}
.responsive-table caption {
  margin-bottom: 1em;
  font-size: 1em;
  font-weight: bold;
  text-align: center;
}
@media (min-width: 48em) {
  .responsive-table caption {
    font-size: 1.5em;
  }
}
.responsive-table tfoot {
  font-size: 0.8em;
  font-style: italic;
}
@media (min-width: 62em) {
  .responsive-table tfoot {
    font-size: 0.9em;
  }
}
@media (min-width: 48em) {
  .responsive-table tbody {
    display: table-row-group;
  }
}
.responsive-table tbody tr {
  margin-bottom: 1em;
}
@media (min-width: 48em) {
  .responsive-table tbody tr {
    display: table-row;
    border-width: 1px;
  }
}
.responsive-table tbody tr:last-of-type {
  margin-bottom: 0;
}
@media (min-width: 48em) {
  .responsive-table tbody tr:nth-of-type(even) {
    background-color: rgba(0, 0, 0, 0.12);
  }
}
.responsive-table tbody th[scope="row"] {
  background-color: #26890d;
  color: white;
}
@media (min-width: 30em) {
  .responsive-table tbody th[scope="row"] {
    border-left: 1px solid #86bc25;
    border-bottom: 1px solid #86bc25;
  }
}
@media (min-width: 48em) {
  .responsive-table tbody th[scope="row"] {
    background-color: transparent;
    color: #000001;
    text-align: left;
  }
}
.responsive-table tbody td {
  text-align: right;
}
@media (min-width: 48em) {
  .responsive-table tbody td {
    border-left: 1px solid #86bc25;
    border-bottom: 1px solid #86bc25;
    text-align: center;
  }
}
@media (min-width: 48em) {
  .responsive-table tbody td:last-of-type {
    border-right: 1px solid #86bc25;
  }
}
.responsive-table tbody td[data-type="currency"] {
  text-align: right;
}
.responsive-table tbody td[data-title]:before {
  content: attr(data-title);
  float: left;
  font-size: 0.8em;
  color: rgba(0, 0, 0, 0.54);
}
@media (min-width: 30em) {
  .responsive-table tbody td[data-title]:before {
    font-size: 0.9em;
  }
}
@media (min-width: 48em) {
  .responsive-table tbody td[data-title]:before {
    content: none;
  }
}
/* ===================== */
</style>
