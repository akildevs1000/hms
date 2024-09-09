<template>
  <v-app>
    <v-app-bar :clipped-left="clipped" fixed app dense>
      <!-- :style="$nuxt.$route.name == 'index' ? 'z-index: 100000' : ''" -->

      <v-app-bar-nav-icon @click.stop="drawer = !drawer" />
      <!-- {{ title }} -->
      <img src="/logo1.png" style="width: 130px" />
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
          <!-- <v-icon style="margin-top: -10px" color="black" size="35"
            >mdi-clock-outline</v-icon
          > -->
          <span style="font-size: 30px; color: black"> {{ currentTime }}</span>
          <span style="font-size: 16px; color: black; font-weight: 200">{{
            todayDate
          }}</span>
        </div>
      </v-spacer>
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

            <v-list-item @click="goToReport()">
              <v-list-item-icon>
                <v-icon>mdi mdi-text-account</v-icon>
              </v-list-item-icon>
              <v-list-item-content>
                <v-list-item-title class="grey--text">Report</v-list-item-title>
              </v-list-item-content>
            </v-list-item>

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
            <v-icon>{{ i.icon }}</v-icon>
          </div>
          <div class="text-center p-2">
            {{ i.title }}
          </div>
          <!-- <v-list-item-icon class="ma-2">
            <v-icon>{{ i.icon }}</v-icon>
          </v-list-item-icon>

          <v-list-item-title class="text-center p-2">
            <br />
            {{ i.title }}
          </v-list-item-title> -->
        </v-list-item>
      </v-list>
    </v-navigation-drawer>
    <!-- <v-navigation-drawer
      v-model="drawer"
      dark
      :clipped="clipped"
      fixed
      app
      :color="sideBarcolor"
      :width="150"
    >
      <v-row no-gutters>
        <v-col
          v-for="(i, idx) in filteredMenu"
          :key="idx"
          cols="12"
          class="text-center white--text"
        >
          <v-card
            v-if="i.to"
            outlined
            class="background pa-5"
            @click="$router.push(i.to)"
          >
            <v-icon @mouseover="showTooltipMenu(i.title)">{{ i.icon }} </v-icon>
            <br />
            <small>{{ i.title }}</small>
          </v-card>
        </v-col>
      </v-row>
    </v-navigation-drawer> -->
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
          topMenu: "dashboard",
          icon: "mdi-home",
          title: "Home",
          to: "/",
          menu: "dashboard",
        },
        {
          topMenu: "dashboard",
          icon: "mdi-calendar",
          title: "Calendar",
          to: "/hotel/calendar1",
          menu: "calendar_access",
        },
        {
          topMenu: "reports",
          icon: "mdi-chart-areaspline",
          title: "Analytics",
          to: "/reports",
          menu: "dashboard",
        },
        
        // {
        //   topMenu: "dashboard",
        //   icon: "mdi-bed",
        //   title: `History`,
        //   open_menu: false,
        //   menu: "history_menu",
        //   hasChildren: [
        //     {
        //       icon: "mdi mdi-home-import-outline",
        //       title: "Reservation",
        //       to: "/reservation/up_coming",
        //       menu: "reservation_access",
        //     },
        //     {
        //       icon: "mdi mdi-home-account",
        //       title: "In House",
        //       to: "/reservation/in_house",
        //       menu: "guest_access",
        //     },
        //     {
        //       icon: "mdi mdi-home-export-outline",
        //       title: "Checkout",
        //       to: "/reservation/check_out",
        //       menu: "reservation_access",
        //     },
        //   ],
        // },
        {
          topMenu: "dashboard",
          icon: "mdi-bed",
          title: "History",
          to: "/reservation/in_house",
          menu: "guest_access",
        },
        {
          topMenu: "customer",
          icon: "mdi mdi-account-tie",
          title: "Guest",
          to: "/customer/list",
          menu: "guest_access",
        },
        {
          topMenu: "customer",
          icon: "mdi-ticket-account",
          title: "Source",
          to: "/source",
          menu: "source_access",
        },
        {
          topMenu: "account",
          icon: "mdi-bank-transfer",
          title: "Income",
          to: "/account",
          menu: "accounts_posting_access",
        },
        {
          topMenu: "sales",
          icon: "mdi-cash",
          title: "Quotation",
          to: "/sales",
          menu: "accounts_posting_access",
        },
        {
          topMenu: "account",
          icon: "mdi mdi-food",
          title: "Posting",
          to: "/posting",
          menu: "accounts_posting_access",
        },
        {
          topMenu: "account",
          icon: "mdi mdi-bank-transfer-out",
          title: "Expense",
          to: "/expense",
          menu: "accounts_expences_access",
        },
        {
          topMenu: "account",
          icon: "mdi-cash",
          title: "GST Bills",
          to: "/taxable",
          menu: "accounts_gst_access",
        },
        // {
        //   topMenu: "account",
        //   icon: "mdi mdi-file-document-edit-outline",
        //   title: "Inquiries",
        //   to: "/inquiry",
        //   menu: "inquiry_access",
        // },
        //{
        // topMenu: "account",
        //  icon: "mdi mdi-cash",
        //title: "Quotation",
        // to: "/quotations",
        // menu: "accounts_posting_access",
        //},
        {
          topMenu: "account",
          icon: "mdi mdi-cash",
          title: "Invoice",
          to: "/invoices",
          menu: "accounts_posting_access",
        },
        {
          topMenu: "account",
          icon: "mdi-account",
          title: "Vendor",
          to: "/vendors",
          menu: "accounts_posting_access",
        },
        {
          topMenu: "account",
          icon: "mdi-account-cash",
          title: `Ledger`,
          open_menu: false,
          menu: "ledger_access",
          hasChildren: [
            {
              icon: "mdi mdi-human-male-board-poll",
              title: "Agents",
              to: "/agents",
              menu: "ledger_agents_access",
            },
            {
              icon: "mdi-account-cash",
              title: "Guest",
              to: "/city_ledger",
              menu: "ledger_guests_access",
            },
          ],
        },
        {
          topMenu: "dashboard",
          icon: "mdi mdi-file-chart-outline",
          title: "Night Audit",
          to: "/management/report/audit",
          menu: "night_audit_access",
        },
        {
          topMenu: "dashboard",
          icon: "mdi-home-search-outline",
          title: "Lost & Found  ",
          to: "/lost_and_found_items",
          menu: "lost_and_found_access",
        },
        // {
        //   topMenu: "dashboard",
        //   icon: "mdi mdi-account-tie",
        //   title: `Management`,
        //   open_menu: false,
        //   menu: "management_access",
        //   hasChildren: [
        //     {
        //       icon: "mdi mdi-bank-transfer-out",
        //       title: "Expense",
        //       to: "/management/expense",
        //       menu: "management_expenses_access",
        //     },
        //     {
        //       icon: "mdi mdi-bank-transfer-in",
        //       title: "Income",
        //       to: "/account",
        //       menu: "management_income_access",
        //     },
        //     {
        //       icon: "mdi mdi-text-account",
        //       title: "Payment By User Report",
        //       to: "/management/report/user",
        //       menu: "management_payments_access",
        //     },

        //     {
        //       icon: "mdi mdi-calendar-month",
        //       title: "All Reports",
        //       to: "/management/report/monthly",
        //       menu: "management_soldout_access",
        //     },
        //   ],
        // },
        // {
        //   topMenu: "dashboard",
        //   icon: "mdi-home-search-outline",
        //   title: "Invenotry ",
        //   to: "/inventory",
        //   menu: "lost_and_found_access",
        // },
        {
          topMenu: "setting",
          icon: "mdi-tools",
          title: `Hotel Settings`,
          open_menu: false,
          menu: "setting_access",
          hasChildren: [
            {
              icon: "mdi mdi-bed",
              title: "Menu Items",
              to: "/hotel_checkin/settings/menu",
              menu: "settings_rooms_category_access",
            },
            {
              icon: "mdi mdi-bed",
              title: "Laundry",
              to: "/hotel_checkin/settings/room_category",
              menu: "settings_rooms_category_access",
            },
            {
              icon: "mdi mdi-bed",
              title: "House Keeping",
              to: "/hotel_checkin/settings/room_category",
              menu: "settings_rooms_category_access",
            },
            {
              icon: "mdi mdi-bed",
              title: "Room Supplies",
              to: "/hotel_checkin/settings/room_category",
              menu: "settings_rooms_category_access",
            },
            {
              icon: "mdi mdi-bed",
              title: "Room Problems",
              to: "/hotel_checkin/settings/room_category",
              menu: "settings_rooms_category_access",
            },
            {
              icon: "mdi mdi-bed",
              title: "Phonebook",
              to: "/hotel_checkin/settings/room_category",
              menu: "settings_rooms_category_access",
            },
          ],
        },
        {
          topMenu: "setting",
          icon: "mdi-tools",
          title: `Hotel Orders`,
          open_menu: false,
          menu: "setting_access",
          hasChildren: [
            {
              icon: "mdi mdi-bed",
              title: "Food Items",
              to: "/hotel_checkin/orders/food",
              menu: "settings_rooms_category_access",
            },
            {
              icon: "mdi mdi-bed",
              title: "Laundry",
              to: "/hotel_checkin/orders/room_category",
              menu: "settings_rooms_category_access",
            },
            {
              icon: "mdi mdi-bed",
              title: "House Keeping",
              to: "/hotel_checkin/orders/room_category",
              menu: "settings_rooms_category_access",
            },
            {
              icon: "mdi mdi-bed",
              title: "Room Supplies",
              to: "/hotel_checkin/orders/room_category",
              menu: "settings_rooms_category_access",
            },
            {
              icon: "mdi mdi-bed",
              title: "Room Problems",
              to: "/hotel_checkin/orders/room_category",
              menu: "settings_rooms_category_access",
            },
            {
              icon: "mdi mdi-bed",
              title: "Phonebook",
              to: "/hotel_checkin/orders/room_category",
              menu: "settings_rooms_category_access",
            },
          ],
        },
        {
          topMenu: "setting",
          icon: "mdi mdi-account-details",
          title: "Company",
          to: "/companies",
          menu: "settings_permissions_access",
        },
        {
          topMenu: "setting",
          icon: "mdi-email",
          title: "Automation",
          to: "/template",
          menu: "settings_rooms_category_access",
        },
        {
          topMenu: "setting",
          icon: "mdi-bed",
          title: "Rooms",
          to: "/room_category",
          menu: "settings_rooms_category_access",
        },
        {
          topMenu: "setting",
          icon: "mdi-sofa", // give appropriate icon here
          title: "Hall",
          to: "/hall",
          menu: "settings_rooms_category_access",
        },
        {
          topMenu: "setting",
          icon: "mdi-tools",
          title: "Price Setup",
          to: "/manage",
          menu: "settings_room_price_access",
        },
        {
          topMenu: "setting",
          icon: "mdi-cog",
          title: "Setup",
          to: "/setup",
          menu: "settings_room_price_access",
        },
        {
          topMenu: "setting",
          icon: "mdi mdi-account-tie",
          title: "Employee",
          to: "/employee",
          menu: "settings_users_access",
        },
        {
          topMenu: "setting",
          icon: "mdi mdi-account-details",
          title: "Devices",
          to: "/devices",
          menu: "devices_permissions_access",
        },
        {
          topMenu: "setting",
          icon: "mdi mdi-account-check-outline",
          title: "Roles",
          to: "/role",
          menu: "settings_roles_access",
        },
        // {
        //   topMenu: "setting",
        //   icon: "mdi mdi-account-check-outline",
        //   title: "Report Emails",
        //   to: "/emails",
        //   menu: "settings_roles_access",
        // },
        // {
        //   topMenu: "setting",
        //   icon: "mdi mdi-account-details",
        //   title: "Settings",
        //   to: "/setting",
        //   menu: "settings_permissions_access",
        // },

        // {
        //   topMenu: "setting",
        //   icon: "mdi mdi-account-details",
        //   title: "Devices Logs",
        //   to: "/devices/devices_logs",
        //   menu: "devices_permissions_access",
        // },
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
    this.title = "MyHotel2Cloud"; // this.$auth.user?.company?.company_code;
    setTimeout(() => {
      this.loadNotificationMenu();
    }, 1000 * 60);
    setInterval(() => {
      this.loadNotificationMenu();
    }, 1000 * 60 * 5);
    let user = this.$auth.user;
    let permissions = user.permissions;

    this.menus.forEach((ele) => {
      if (
        permissions.includes(ele.menu) ||
        this.$auth.user.user_type == "company"
      ) {
        this.items.push(ele);
      }
    });

    this.getCompanyDetails();

    this.filteredMenu = this.items;
    this.$router.push(this.filteredMenu[0].to ?? "/");

    setInterval(() => {
      const now = new Date();
      // Get the day, month, year, and day of the week
      var day = now.getDate();
      var month = now.getMonth() + 1; // Month is zero-based, so add 1
      var year = now.getFullYear();

      day = (day < 10 ? "0" : "") + day;
      month = (month < 10 ? "0" : "") + month;
      const formattedDateTime = month + "-" + day + "-" + year;

      this.currentTime = now.toLocaleTimeString([], { hour12: false });
      this.todayDate =
        this.$dateFormat.format_date_with_dayname(formattedDateTime);
    }, 1000);

    this.setActive({
      label: "Dashboard",
      name: "dashboard",
    });
  },

  mounted() {
    document.addEventListener("mousemove", this.updateMouseLocation);
    // setInterval(() => {
    //   const now = new Date();
    //   this.currentTime = now.toLocaleTimeString();
    // }, 1000);

    setInterval(() => {
      const now = new Date();
      this.currentTime = now.toLocaleTimeString([], { hour12: false });
    }, 1000);
  },

  computed: {
    changeColor() {
      return this.$store.state.color;
    },

    currentDate() {
      const months = [
        "Jan",
        "Feb",
        "Mar",
        "Apr",
        "May",
        "Jun",
        "Jul",
        "Aug",
        "Sep",
        "Oct",
        "Nov",
        "Dec",
      ];

      const now = new Date();
      const day = now.getDate();
      const monthIndex = now.getMonth();
      const year = now.getFullYear();
      const formattedDate = `${day} ${months[monthIndex]} ${year}`;
      return formattedDate;
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

    caps(str) {
      return str.replace(/\b\w/g, (c) => c.toUpperCase());
    },

    goToSetting() {
      this.$router.push("/setting");
    },

    goToCompany() {
      let u = this.$auth.user.user_type;
      // if(u){
      // this.$router.push(`/empl/${this.$auth.user?.company?.id}`);
      // }
      this.$router.push(`/companies/${this.$auth.user?.company?.id}`);
    },

    goToReport() {
      this.$router.push(`/management/report/user`);
    },

    getCompanyDetails() {
      let user = this.$auth.user;

      this.$axios.get(`company/${user?.company?.id}`).then(({ data }) => {
        let { modules } = data.record;

        if (modules !== null) {
          this.modules = {
            module_ids: modules.module_ids || [],
            module_names: modules.module_names.map((e) => ({
              icon: "mdi-chart-bubble",
              title: this.caps(e),
              to: "/" + e + "_modules",
              permission: true,
            })),
          };
        }
      });
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
* {
  font-family: "Source Sans Pro", sans-serif !important;
  margin: 0;
  padding: 0;
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
</style>

<style>
.v-navigation-drawer {
  width: 80px !important;
  text-align: center;
}

.v-navigation-drawer--is-mouseover {
  width: 140px !important;
  text-align: center;
}

.global-search-textbox .v-input__slot {
  min-height: 30px !important;
}
.global-search-textbox .v-label {
  line-height: 11px !important;
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

.global-search-select .v-select__selections {
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

.boxheight {
  height: 55px;
  list-style: 5px;
}

.client {
  width: fit-content;
  block-size: fit-content;
  border: solid #000000 1px;
  padding: 15px;
}

.columns {
  display: flex;
  flex-wrap: wrap;
  gap: 2em;
}

.columns .client {
  width: auto;
  flex: 1 1 40%;
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
.small-text {
  font-size: 12px;
}
</style>
