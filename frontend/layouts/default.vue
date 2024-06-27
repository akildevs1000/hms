<template>
  <v-app>
    <v-navigation-drawer
      v-model="drawer"
      dark
      :mini-variant="miniVariant"
      :clipped="clipped"
      fixed
      app
      :color="sideBarcolor"
      :style="miniVariant ? 'width: 60px' : ''"
    >
      <v-list v-for="(i, idx) in filteredMenu" :key="idx" class="pt-1">
        <v-list-item
          :to="i.to"
          router
          v-if="!i.hasChildren"
          :class="!miniVariant || 'pl-2'"
        >
          <v-list-item-icon class="ma-2">
            <v-icon
              @mouseover="showTooltipMenu(i.title)"
              @mouseleave="show = false"
              >{{ i.icon }}
            </v-icon>
          </v-list-item-icon>
          <v-list-item-title> {{ i.title }}&nbsp; </v-list-item-title>
        </v-list-item>
        <v-list-item
          v-else
          :class="!miniVariant || 'pl-2'"
          @click="i.open_menu = !i.open_menu"
        >
          <v-list-item-icon class="ma-2">
            <v-icon>{{ i.icon }}</v-icon>
            <v-icon v-if="miniVariant" small
              >{{ !i.open_menu ? "mdi-chevron-down" : "mdi-chevron-up" }}
            </v-icon>
          </v-list-item-icon>
          <v-list-item-title>{{ i.title }} </v-list-item-title>
          <v-icon small
            >{{ !i.open_menu ? "mdi-chevron-down" : "mdi-chevron-up" }}
          </v-icon>
        </v-list-item>
        <div v-if="i.open_menu">
          <div
            style="margin-left: 54px"
            v-for="(j, jdx) in i.hasChildren"
            :key="jdx"
          >
            <!-- v-show="!miniVariant" -->
            <v-list-item style="min-height: 0" :to="j.to">
              <v-list-item-title v-if="!miniVariant"
                >{{ j.title }}
              </v-list-item-title>

              <v-list-item-icon
                :style="miniVariant ? 'margin-left: -54px;' : ''"
              >
                <v-icon
                  :to="j.to"
                  :style="miniVariant ? 'margin-left: 12px;' : ''"
                  @mouseover="showTooltipMenu(j.title)"
                  @mouseleave="show = false"
                >
                  {{ j.icon }}
                </v-icon>
              </v-list-item-icon>
            </v-list-item>
          </div>
        </div>
      </v-list>
    </v-navigation-drawer>

    <v-app-bar :clipped-left="clipped" fixed app dense>
      <!-- :style="$nuxt.$route.name == 'index' ? 'z-index: 100000' : ''" -->

      <v-app-bar-nav-icon @click.stop="drawer = !drawer" />
      <!-- <v-btn icon @click.stop="miniVariant = !miniVariant">
        <v-icon>mdi-{{ `chevron-${miniVariant ? "right" : "left"}` }}</v-icon>
      </v-btn> -->
      <!-- <v-btn icon @click.stop="clipped = !clipped">
        <v-icon>mdi-application</v-icon>
      </v-btn> -->
      {{ title }}
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

    <v-main class="main_bg">
      <!-- <v-container> -->
      <v-tooltip
        v-model="show"
        top
        :position-x="x"
        :position-y="y"
        absolute
        offset-y
        color="primary"
      >
        <span>{{ menuName }}</span>
      </v-tooltip>

      <div class="mx-2 my-4">
        <nuxt />
      </div>
      <!-- </v-container> -->
    </v-main>
    <!-- <v-btn
      height="50"
      width="20"
      dark
      :color="changeColor"
      class="fixed-setting"
      @click.stop="rightDrawer = !rightDrawer"
    >
      <v-icon class="spin" dark size="25">mdi-cog</v-icon>
    </v-btn> -->
    <!-- setting -->
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
          topMenu: "dashboard",
          icon: "mdi-bed",
          title: `History`,
          open_menu: false,
          menu: "history_menu",
          hasChildren: [
            {
              icon: "mdi mdi-home-import-outline",
              title: "Reservation",
              to: "/reservation/up_coming",
              menu: "reservation_access",
            },
            {
              icon: "mdi mdi-home-account",
              title: "In House",
              to: "/reservation/in_house",
              menu: "guest_access",
            },
            {
              icon: "mdi mdi-home-export-outline",
              title: "Checkout",
              to: "/reservation/check_out",
              menu: "reservation_access",
            },
          ],
        },
        {
          topMenu: "dashboard",
          icon: "mdi-bed",
          title: `Party Hall`,
          open_menu: false,
          menu: "party_hall",
          hasChildren: [
            // {
            //   icon: "mdi mdi-home-import-outline",
            //   title: "New",
            //   to: "/hall/party-hall-booking",
            //   menu: "reservation_access",
            // },
            {
              icon: "mdi mdi-home-import-outline",
              title: "Reservation",
              to: "/hall/upcoming",
              menu: "party_hall",
            },
            {
              icon: "mdi mdi-home-account",
              title: "In House",
              to: "/hall/ongoing",
              menu: "party_hall",
            },
            {
              icon: "mdi mdi-home-export-outline",
              title: "Check Out",
              to: "/hall/completed",
              menu: "party_hall",
            },
          ],
        },
        {
          topMenu: "customer",
          icon: "fas fa-male",
          title: "Guest",
          to: "/customer/list",
          menu: "guest_access",
        },
        {
          topMenu: "customer",
          icon: "mdi mdi-account-tie",
          title: "Source",
          to: "/source",
          menu: "source_access",
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
        {
          topMenu: "account",
          icon: "mdi mdi-file-document-edit-outline",
          title: "Inquiries",
          to: "/inquiry",
          menu: "inquiry_access",
        },
        {
          topMenu: "account",
          icon: "mdi mdi-cash",
          title: "Quotation",
          to: "/quotations",
          menu: "accounts_posting_access",
        },
        {
          topMenu: "account",
          icon: "mdi mdi-cash",
          title: "Invoice",
          to: "/invoices",
          menu: "accounts_posting_access",
        },
        {
          topMenu: "account",
          icon: "mdi mdi-food",
          title: "Food Plan",
          to: "/foodplans",
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
        {
          topMenu: "dashboard",
          icon: "mdi mdi-account-tie",
          title: `Management`,
          open_menu: false,
          menu: "management_access",
          hasChildren: [
            {
              icon: "mdi mdi-bank-transfer-out",
              title: "Expense",
              to: "/management/expense",
              menu: "management_expenses_access",
            },
            {
              icon: "mdi mdi-bank-transfer-in",
              title: "Income",
              to: "/account",
              menu: "management_income_access",
            },
            {
              icon: "mdi mdi-text-account",
              title: "Payment By User Report",
              to: "/management/report/user",
              menu: "management_payments_access",
            },

            {
              icon: "mdi mdi-calendar-month",
              title: "All Reports",
              to: "/management/report/monthly",
              menu: "management_soldout_access",
            },
          ],
        },
        {
          topMenu: "dashboard",
          icon: "mdi-home-search-outline",
          title: "Invenotry ",
          to: "/inventory",
          menu: "lost_and_found_access",
        },
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
          icon: "mdi-email",
          title: "Automation",
          to: "/template",
          menu: "settings_rooms_category_access",
        },
        {
          topMenu: "setting",
          icon: "mdi mdi-bed",
          title: "Room Category",
          to: "/room_category",
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
          icon: "mdi mdi-account-tie",
          title: "Users",
          to: "/users",
          menu: "settings_users_access",
        },
        {
          topMenu: "setting",
          icon: "mdi mdi-account-check-outline",
          title: "Roles",
          to: "/role",
          menu: "settings_roles_access",
        },
        {
          topMenu: "setting",
          icon: "mdi mdi-account-check-outline",
          title: "Report Emails",
          to: "/emails",
          menu: "settings_roles_access",
        },
        {
          topMenu: "setting",
          icon: "mdi mdi-account-details",
          title: "Settings",
          to: "/setting",
          menu: "settings_permissions_access",
        },
        {
          topMenu: "setting",
          icon: "mdi mdi-account-details",
          title: "Profile",
          to: "/companies",
          menu: "settings_permissions_access",
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
          icon: "mdi mdi-account-details",
          title: "Devices Logs",
          to: "/devices/devices_logs",
          menu: "devices_permissions_access",
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
      miniVariant: false,
      title: "",
      logout_btn: {
        icon: "mdi-logout",
        label: "Logout",
      },
    };
  },

  created() {
    this.title = "EZHMS"; // this.$auth.user?.company?.company_code;
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
