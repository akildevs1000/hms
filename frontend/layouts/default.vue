<template>
  <v-app>
    <v-app-bar :clipped-left="clipped" fixed app dense>
      <v-app-bar-nav-icon @click.stop="drawer = !drawer" />
      <img src="/login/login-logo.png" style="width: 100px" />
      <v-spacer></v-spacer>
      <v-btn
        text
        v-for="(topMenu, index) in topMenus"
        :key="index"
        :color="isActive(topMenu) ? 'blue' : ''"
        @click="setActive(topMenu)"
        >{{ topMenu.label }}</v-btn
      >
      <v-spacer></v-spacer>

      <v-spacer>
        <div>
          <span style="font-size: 30px; color: black"> {{ currentTime }}</span>
          <span style="font-size: 16px; color: black; font-weight: 200">{{
            todayDate
          }}</span>
        </div>
      </v-spacer>
      <div style="font-size: 10px">
        {{ getCOmpanyName }}
      </div>
      <v-avatar
        style="margin-right: 10px"
        color="green"
        size="30"
        :title="getCOmpanyName"
      >
        <img :src="getCompanyLogo || '/no-image.PNG'" />
      </v-avatar>
      <v-badge
        class="mt-2 mr-1"
        :color="pendingNotificationsCount > 0 ? 'red' : 'green'"
        :content="
          pendingNotificationsCount == '' ? '0' : pendingNotificationsCount
        "
        overlap
      >
        <v-icon @click="gotoReservationPage()"> mdi-bell-ring </v-icon>
      </v-badge>
      <v-menu
        nudge-bottom="50"
        nudge-left="20"
        transition="scale-transition"
        origin="center center"
        bottom
        left
        min-width="150"
      >
        <template v-slot:activator="{ on, attrs }">
          <v-avatar v-bind="attrs" v-on="on">
            <img :src="getLogo || '/no-image.PNG'" />
          </v-avatar>
        </template>

        <v-list light nav dense>
          <v-list-item-group color="primary">
            <v-list-item @click="goToCompany()">
              <v-list-item-icon>
                <v-icon>mdi-account-multiple-outline</v-icon>
              </v-list-item-icon>
              <v-list-item-content>
                <v-list-item-title class="grey--text"
                  >Profile</v-list-item-title
                >
              </v-list-item-content>
            </v-list-item>

            <!-- <v-list-item @click="goToReport()">
              <v-list-item-icon>
                <v-icon>mdi mdi-text-account</v-icon>
              </v-list-item-icon>
              <v-list-item-content>
                <v-list-item-title class="grey--text">Report</v-list-item-title>
              </v-list-item-content>
            </v-list-item> -->

            <v-list-item @click="logout">
              <v-list-item-icon>
                <v-icon>mdi-logout</v-icon>
              </v-list-item-icon>
              <v-list-item-content>
                <v-list-item-title class="grey--text">Logout</v-list-item-title>
              </v-list-item-content>
            </v-list-item>
          </v-list-item-group>
        </v-list>
      </v-menu>
    </v-app-bar>
    <v-navigation-drawer
      v-model="drawer"
      dark
      :clipped="clipped"
      fixed
      app
      :color="sideBarcolor"
      :width="100"
      expand-on-hover
      rail
    >
      <v-list v-for="(i, idx) in filteredMenu" :key="idx" :title="i.title">
        <v-list-item
          @click="$router.push(i.to)"
          :class="!miniVariant || 'pl-2'"
          router
          style="display: inline-block; padding: 0px 20px"
          vertical
        >
          <div>
            <v-icon v-if="i.icon">{{ i.icon }}</v-icon>
            <span v-else>
              <v-avatar>
                <img :src="i.image" />
              </v-avatar>
            </span>
          </div>
          <div class="text-center p-2">
            {{ i.title }}
          </div>
        </v-list-item>
      </v-list>
    </v-navigation-drawer>
    <v-main class="main_bg" style="padding-left: 75px">
      <v-container fluid>
        <nuxt />
      </v-container>
    </v-main>

    <v-navigation-drawer
      v-model="rightDrawer"
      :clipped="true"
      :right="right"
      fixed
      style="z-index: 1000"
    >
      <v-row style="margin-top: 50px">
        <v-col>
          <v-card class="pa-2" elevation="0">
            <v-col cols="12">
              <div class="mb-3">
                <Strong>Theme</Strong>
              </div>
              <div
                class="btn-group"
                role="group"
                aria-label="Basic radio toggle button group"
              >
                <input
                  type="radio"
                  class="btn-check"
                  name="theme"
                  id="light"
                  autocomplete="off"
                  @click="changeTheme('light')"
                />
                <label class="btn" :class="'btn-outline-dark'" for="light"
                  >Light</label
                >

                <input
                  type="radio"
                  class="btn-check"
                  name="theme"
                  id="dark"
                  autocomplete="off"
                  @click="changeTheme('dark')"
                />
                <label class="btn btn-outline-dark" for="dark">Dark</label>
              </div>
            </v-col>
            <v-divider></v-divider>
            <v-col cols="12">
              <div class="mb-3">
                <Strong>Top Bar</Strong>
              </div>
              <div class="d-flex">
                <v-btn
                  class="mx-2 stg-color-icon"
                  fab
                  dark
                  x-small
                  color="primary"
                  @click="changeTopBarColor('primary')"
                ></v-btn>
                <v-btn
                  class="mx-2 stg-color-icon"
                  fab
                  dark
                  x-small
                  color="error"
                  @click="changeTopBarColor('error')"
                ></v-btn>
                <v-btn
                  class="mx-2 stg-color-icon"
                  fab
                  dark
                  x-small
                  color="indigo"
                  @click="changeTopBarColor('indigo')"
                ></v-btn>
                <v-btn
                  class="mx-2 stg-color-icon"
                  fab
                  dark
                  x-small
                  color="background"
                  @click="changeTopBarColor('background')"
                ></v-btn>
              </div>
            </v-col>
            <v-divider></v-divider>
            <v-col cols="12">
              <div class="mb-3">
                <Strong>Side Bar</Strong>
              </div>
              <div class="d-flex">
                <v-btn
                  class="mx-2 stg-color-icon"
                  fab
                  dark
                  x-small
                  color="primary"
                  @click="changeSideBarColor('primary')"
                ></v-btn>
                <v-btn
                  class="mx-2 stg-color-icon"
                  fab
                  dark
                  x-small
                  color="error"
                  @click="changeSideBarColor('error')"
                ></v-btn>
                <v-btn
                  class="mx-2 stg-color-icon"
                  fab
                  dark
                  x-small
                  color="indigo"
                  @click="changeSideBarColor('indigo')"
                ></v-btn>
                <v-btn
                  class="mx-2 stg-color-icon"
                  fab
                  dark
                  x-small
                  color="background"
                  @click="changeSideBarColor('background')"
                >
                </v-btn>
              </div>
            </v-col>
          </v-card>
        </v-col>
      </v-row>
    </v-navigation-drawer>
  </v-app>
</template>

<script>
export default {
  head() {
    return {
      link: [
        {
          rel: "stylesheet",
          href: "https://fonts.googleapis.com/css2?family=Source+Sans+Pro:wght@400;600;700&display=swap",
        },
      ],
    };
  },
  data() {
    return {
      currentTime: "00:00:00",
      todayDate: "---",
      activeMenu: null, // Keep track of the active menu
      topMenus: [
        {
          label: "Dashboard",
          name: "dashboard",
        },
        {
          label: "Customer",
          name: "customer",
        },
        {
          label: "Account",
          name: "account",
        },
        {
          label: "Sales",
          name: "sales",
        },
        {
          label: "Reports",
          name: "reports",
        },
        {
          label: "Setting",
          name: "setting",
        },
        // {
        //   label: "House Keeping",
        //   name: "house_keeping",
        // },
      ],
      pendingNotificationsCount: 0,
      menuName: "",
      show: false,
      y: 0,
      x: 0,
      miniVariant: false,
      right: true,
      rightDrawer: false,
      color: "",
      sideBarcolor: "background",
      year: new Date().getFullYear(),
      dropdown_menus: [{ title: "setting" }, { title: "logout" }],
      clipped: false,
      open_menu: [],
      drawer: true,
      fixed: false,
      order_count: "",
      menus: [
        {
          topMenu: "house_keeping",
          image: "/cleaner.png",
          module: "house_keeping",
          title: "House Keeping",
          to: "/house_keeping",
          menu: "settings_permissions_access",
        },
        {
          topMenu: "dashboard",
          icon: "mdi-home",
          module: "home",
          title: "Home",
          to: "/",
          menu: "dashboard",
        },
        {
          topMenu: "dashboard",
          icon: "mdi-calendar",
          module: "calendar",
          title: "Calendar",
          to: "/hotel/calendar1",
          menu: "calendar_access",
        },
        {
          topMenu: "reports",
          icon: "mdi-chart-areaspline",
          module: "analytics",
          title: "Analytics",
          to: "/reports",
          menu: "dashboard",
        },
        {
          topMenu: "reports",
          icon: "mdi-chart-areaspline",
          module: "night_audit",
          title: "Night Audit",
          to: "/management/report/night_audit",
          menu: "night_audit_access",
        },
        {
          topMenu: "reports",
          icon: "mdi-chart-areaspline",
          module: "summary",
          title: "Summary",
          to: "/management/report/summary",
          menu: "summary_access",
        },
        {
          topMenu: "reports",
          icon: "mdi-chart-areaspline",
          module: "cash",
          title: "Cash Report",
          to: "/management/report/cash",
          menu: "cash_access",
        },
        {
          topMenu: "reports",
          icon: "mdi-chart-areaspline",
          module: "ota",
          title: "OTA Report",
          to: "/management/report/ota",
          menu: "ota_access",
        },
        {
          topMenu: "reports",
          icon: "mdi-chart-areaspline",
          module: "ota_trn",
          title: "OTA TRN",
          to: "/management/report/trn",
          menu: "ota_trn_access",
        },
        {
          topMenu: "dashboard",
          icon: "mdi-bed",
          module: "history",
          title: "History",
          to: "/history",
          menu: "history_access",
        },
        {
          topMenu: "customer",
          icon: "mdi-ticket-account",
          module: "customers",
          title: "Customers",
          to: "/source",
          menu: "source_access",
        },
        {
          topMenu: "customer",
          icon: "mdi-chat",
          module: "chat",
          title: "Chat",
          to: "/chat",
          menu: "chat_access",
        },
        {
          topMenu: "account",
          icon: "mdi-bank-transfer",
          module: "income",
          title: "Income",
          to: "/account",
          menu: "accounts_posting_access",
        },

        {
          topMenu: "account",
          icon: "mdi-bank-transfer",
          module: "city_ledger",
          title: "City Ledger",
          to: "/city_ledger",
          menu: "accounts_posting_access",
        },

        {
          topMenu: "sales",
          icon: "mdi-cash",
          module: "inquiry",
          title: "Inquiry",
          to: "/inquiry",
          menu: "accounts_posting_access",
        },
        {
          topMenu: "sales",
          icon: "mdi-cash",
          module: "quotation",
          title: "Quotation",
          to: "/sales",
          menu: "accounts_posting_access",
        },
        {
          topMenu: "sales",
          icon: "mdi-cash",
          module: "invoices",
          title: "Invoice",
          to: "/invoices",
          menu: "accounts_posting_access",
        },
        {
          topMenu: "account",
          icon: "mdi mdi-food",
          module: "posting",
          title: "Posting",
          to: "/posting",
          menu: "accounts_posting_access",
        },
        {
          topMenu: "account",
          icon: "mdi mdi-bank-transfer-out",
          module: "expense",
          title: "Expense",
          to: "/expense",
          menu: "accounts_expences_access",
        },
        {
          topMenu: "account",
          icon: "mdi-cash",
          module: "gst_bills",
          title: "GST Bills",
          to: "/taxable",
          menu: "accounts_gst_access",
        },
        // {
        //   topMenu: "account",
        //   icon: "mdi-account",
        //   module: "vendors",
        //   title: "Vendor",
        //   to: "/vendors",
        //   menu: "accounts_posting_access",
        // },

        {
          topMenu: "dashboard",
          icon: "mdi-home-search-outline",
          module: "lost_and_found_items",
          title: "Lost & Found  ",
          to: "/lost_and_found_items",
          menu: "lost_and_found_access",
        },
        {
          topMenu: "dashboard",
          icon: "mdi-lightbulb-on",
          module: "devices_permissions_access",
          title: "Rooms",
          to: "/devices",
          menu: "devices_permissions_access",
        },
        {
          topMenu: "setting",
          icon: "mdi mdi-account-details",
          module: "company",
          title: "Company",
          to: "/companies",
          menu: "settings_permissions_access",
        },
        {
          topMenu: "setting",
          icon: "mdi-email",
          module: "automation",
          title: "Automation",
          to: "/template",
          menu: "settings_rooms_category_access",
        },
        {
          topMenu: "setting",
          icon: "mdi-bed",
          module: "rooms",
          title: "Rooms",
          to: "/room_category",
          menu: "settings_rooms_category_access",
        },
        {
          topMenu: "setting",
          icon: "mdi-sofa", // give appropriate icon here
          module: "hall",
          title: "Hall",
          to: "/hall",
          menu: "settings_rooms_category_access",
        },
        {
          topMenu: "setting",
          icon: "mdi-tools",
          module: "price_setup",
          title: "Price Setup",
          to: "/manage",
          menu: "settings_room_price_access",
        },

        {
          topMenu: "setting",
          icon: "mdi mdi-account-tie",
          module: "employee",
          title: "Employee",
          to: "/employee",
          menu: "settings_users_access",
        },
        {
          topMenu: "setting",
          icon: "mdi-cellphone-lock",
          module: "device",
          title: "Devices",
          to: "/devices",
          menu: "devices_permissions_access",
        },
        {
          topMenu: "setting",
          icon: "mdi mdi-account-check-outline",
          module: "role",
          title: "Roles",
          to: "/role",
          menu: "settings_roles_access",
        },
        {
          topMenu: "setting",
          icon: "mdi-vacuum-outline",
          module: "house_keeping",
          title: "House Keeping",
          to: "/house_keeping",
          menu: "settings_room_price_access",
        },
      ],
      items: [],
      filteredMenu: [],
      modules: {
        module_ids: [],
        module_names: [],
      },
      clipped: true,
      currentTime: "",

      title: "",
      logout_btn: {
        icon: "mdi-logout",
        label: "Logout",
      },
    };
  },

  created() {
    console.log("Default Created");

    // console.log(
    //   "this.$auth.user_verified_mobileotp",
    //   this.$auth.user_verified_mobileotp
    // );

    // {
    //   if (this.$auth.user_verified_mobileotp == true) {
    //   } else {
    //     this.$router.push(`/logout`);
    //   }
    // }
    this.title = "MyHotel2Cloud"; // this.$auth.user?.company?.company_code;
    setTimeout(() => {
      this.loadNotificationMenu();
    }, 1000 * 60);
    setInterval(() => {
      this.loadNotificationMenu();
    }, 1000 * 60);
    let user = this.$auth.user;
    let permissions = user.permissions;

    this.menus.forEach((ele) => {
      this.items.push(ele);
      // if (
      //   permissions.includes(ele.menu) ||
      //   this.$auth.user.user_type == "company"
      // ) {
      //   this.items.push(ele);
      // }
    });

    this.filteredMenu = this.items;
    this.$router.push(this.filteredMenu[0].to ?? "/");

    this.setActive({
      label: "Dashboard",
      name: "dashboard",
    });
  },

  mounted() {
    document.addEventListener("mousemove", this.updateMouseLocation);
    console.log("company auth", this.$auth);
    console.log("company", this.$auth.user);

    let timezone = "Asia/Kolkata";

    if (this.$auth.user.company?.timezone) {
      timezone = this.$auth.user.company.timezone.utc_time_zone;
    }

    setInterval(() => {
      // this.currentTime = new Date().toLocaleTimeString([], { hour12: false });

      this.currentTime = new Intl.DateTimeFormat("en-US", {
        timeZone: timezone, // Specify the desired timezone
        hour: "2-digit",
        minute: "2-digit",
        second: "2-digit",
        hour12: false, // 24-hour format
      }).format(new Date());
    }, 1000);

    const now = new Date();

    // Format day, month, year, and day of the week
    const dayName = new Intl.DateTimeFormat("en-US", {
      weekday: "long",
    }).format(now);
    const day = String(now.getDate()).padStart(2, "0");
    const month = String(now.getMonth() + 1).padStart(2, "0"); // Month is zero-based, so add 1
    const year = now.getFullYear();

    // Construct the final date string
    this.todayDate = `${day}-${month}-${year}, ${dayName}`;
  },

  computed: {
    changeColor() {
      return this.$store.state.color;
    },

    getUser() {
      if (!this.$auth.user) {
        return "";
      }
      // return this.$auth.user.user_type == "employee"
      //   ? this.$auth.user.name
      //   : this.$auth.user.company.name;
      return this.$auth.user.name + " " + this.$auth.user.last_name;
    },

    getLogo() {
      return (this.$auth.user && this.$auth.user.image) || "/no-image.PNG";
    },
    getCompanyLogo() {
      return (
        (this.$auth.user && this.$auth.user.company.logo) || "/no-image.PNG"
      );
    },
    getCOmpanyName() {
      return (this.$auth.user && this.$auth.user.company.name) || "---";
    },
  },
  methods: {
    isActive(menu) {
      return this.activeMenu === menu;
    },
    setActive(menu) {
      console.log(menu);
      // return;
      this.activeMenu = menu;
      this.filteredMenu = this.items.filter((e) => e.topMenu == menu.name);
      this.$router.push(
        (this.filteredMenu[0] && this.filteredMenu[0].to) || "/"
      );
    },
    showTooltipMenu(e) {
      this.show = true;
      this.menuName = e;
    },
    gotoReservationPage() {
      this.pendingNotificationsCount = 0;
      this.$router.push("/reservation/up_coming");
    },
    loadNotificationMenu() {
      let company_id = this.$auth.user?.company?.id || 0;
      //console.log("company_id", company_id);
      if (company_id == 0) {
        return false;
      }
      let options = {
        params: {
          company_id: company_id,
        },
      };
      //this.pendingNotificationsCount = 0;
      let pendingcount = 0;
      this.$axios.get(`get-notifications-count`, options).then(({ data }) => {
        try {
          pendingcount = 0;
          //console.log("data.online_booking_count", data.online_booking_count);
          if (data.online_booking_count) {
            let storedRecords = localStorage.getItem("online_booking_count");

            //console.log("storedRecords", storedRecords);

            let nonMatching = [];

            for (let num of data.online_booking_count) {
              if (!storedRecords.includes(num) && !nonMatching.includes(num)) {
                nonMatching.push(num);
              }
            }

            //console.log("nonMatching", nonMatching);

            localStorage.setItem(
              "online_booking_count",
              data.online_booking_count
            );

            pendingcount = nonMatching.length; // += data.online_booking_count;
          }

          this.pendingNotificationsCount = pendingcount;
        } catch (Exp) {}
      });
    },
    updateMouseLocation(event) {
      this.x = event.clientX;
      this.y = event.clientY;
    },

    changeTopBarColor(color) {
      this.color = color;
      this.$store.commit("change_color", color);
    },

    changeTheme(color) {
      // alert(color);
    },

    changeSideBarColor(color) {
      this.sideBarcolor = color;
    },

    goToCompany() {
      let u = this.$auth.user.user_type;
      // if(u){
      // this.$router.push(`/empl/${this.$auth.user?.company?.id}`);
      // }
      this.$router.push(`/companies`);
    },

    goToReport() {
      this.$router.push(`/management/report/user`);
    },
    can(per) {
      let user = this.$auth.user;
      return (
        (user && user.permissions.some((e) => e == per || per == "/")) ||
        user.is_master
      );
    },

    async logout() {
      this.$axios.get(`/logout`).then(({ res }) => {
        this.$auth.logout();
      });
    },
  },
};
</script>
<style>
.my-checkbox label {
  /* background: red; */
  font-size: 11px;
}
* {
  font-family: "Source Sans Pro", sans-serif !important;
  margin: 0;
  padding: 0;
}
small {
  font-size: 11px;
}
.text-color {
  color: #8a8a8a;
}
.text-color-class {
  background: #8a8a8a;
}
table {
  border-spacing: 0;
  border-collapse: collapse; /* To ensure borders are collapsed like the effect of cellspacing="0" */
  width: 100%;
}
td {
  font-size: 12px;
}

.v-data-table th {
  font-weight: normal;
  color: #4390fc !important; /* Table cell text color */
  border-top: 1px solid #bdbdbd;
  border-bottom: 1px solid #bdbdbd !important;
}
.border-top {
  border-top: 1px solid #e0e0e0;
}
.border-bottom {
  border-bottom: 1px solid #e0e0e0;
}
.v-data-table td {
  color: #8a8a8a !important; /* Table cell text color */
}
.available {
  background-color: #139c4a;
}
.booked {
  background-color: #71de36;
}
.occupied {
  background-color: #ffc000;
}
.expected_checkout {
  background-color: #03c1ec;
}
.checked_out {
  background-color: #dc3545;
}
.blocked {
  background-color: #a6a6a6;
}
.roombox1 {
  float: left;
  width: 55px;
  height: 55px;
  margin: 5px;
  margin-top: 10px;
  margin-left: 10px;
}
.roombox {
  /* width: 50px;
  flex: 0 0 50px;
  margin: 5px; */

  width: 55px;
  height: 55px;
  font-size: 11px !important;
  line-height: 14px !important;
}
.custom-text-field textarea {
  /* color: #787878 !important; */
}

input {
  font-size: 14px !important;
  /* color: #787878 !important; */
}

/* Chrome, Safari, Edge, Opera */
input::-webkit-outer-spin-button,
input::-webkit-inner-spin-button {
  -webkit-appearance: none;
  margin: 0;
}

/* Firefox */
input[type="number"] {
  -moz-appearance: textfield;
}

.v-navigation-drawer {
  width: 80px !important;
  text-align: center;
}

.v-navigation-drawer--is-mouseover {
  width: 140px !important;
  text-align: center;
}

.v-input__slot {
  min-height: 30px !important;
}
.v-input__slot .v-label {
  line-height: 11px !important;
  font-size: 13px;
}

.v-select__slot .v-input__icon--append {
  margin-top: -4px;
}

.global-search-textbox .v-input__icon {
  height: 17px !important;
}
.global-search-textbox .v-text-field--outlined.v-input--dense .v-label {
  top: 4px !important;
}
.global-search-textbox-calender .v-input__slot {
  min-height: 26px !important;
}
.global-search-textbox-calender .v-text-field__slot {
  height: 30px !important;
}
/* .global-search-textbox-calender
  .v-text-field--outlined.v-input--dense
  .v-label {
  top: 4px !important;
} */
.global-search-textbox-calender
  .v-text-field--outlined.v-input--dense
  .v-label {
  top: 0px !important;
}
/* .global-search-textbox-calender .v-label {
  line-height: 12px !important;
} */
.global-search-textbox-calender .v-input__icon {
  height: 15px !important;
}
.global-search-textbox-calender .v-input input {
  height: 30px !important;
}
.global-search-select .v-input__slot {
  min-height: 30px !important;
}
.global-search-select .v-label {
  line-height: 11px !important;
}
.global-search-select .v-input__icon {
  height: 17px !important;
}

.global-search-select .v-autocomplete__selections {
  padding: 0px !important;
}

.global-search-select .v-label {
  top: 13px !important;
}

.global-search-date .v-input__slot {
  min-height: 30px !important;
}
.global-search-date .v-label {
  line-height: 11px !important;
}
.global-search-date .v-input__icon {
  height: 17px !important;
}

.global-search-date .v-label {
  top: 21px !important;
}
.empty-doughnut1 {
  border: 16px solid rgb(150, 150, 150);
  height: 100px;
  width: 100px;
  padding-top: 20%;
  font-size: 14px;
  margin-top: 8px;
  text-align: center;
  border-radius: 50%;
}
.zoom-on-hover {
  transition: transform 0.1s ease-in-out;
}

.zoom-on-hover:hover {
  transform: scale(1.2); /* Zoom in by 10% */
}
@keyframes zoomInOut {
  0% {
    transform: scale(1); /* Normal size */
  }
  50% {
    transform: scale(1.2); /* Zoom in */
  }
  100% {
    transform: scale(1); /* Zoom out */
  }
}

.zoom-card {
  animation: zoomInOut 2s infinite ease-in-out; /* Adjust duration as needed */
}

.small-text {
  font-size: 10px;
}
.reports-events-autocomplete .v-input__slot {
  min-height: 33px !important;
}
.reports-events-autocomplete .v-label {
  line-height: 15px !important;
}
.reports-events-autocomplete .v-input__icon {
  height: 20px !important;
}
</style>
