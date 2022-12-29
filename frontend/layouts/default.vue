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
      <br />
      <v-list v-for="(i, idx) in items" :key="idx" style="padding: 5px 0 0 0px">
        <v-list-item
          :to="i.to"
          router
          v-if="!i.hasChildren"
          :class="!miniVariant || 'pl-2'"
        >
          <v-list-item-icon class="ma-2">
            <v-icon>{{ i.icon }}</v-icon>
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
                >
                  {{ j.icon }}
                </v-icon>
              </v-list-item-icon>
            </v-list-item>
          </div>
        </div>
      </v-list>
    </v-navigation-drawer>

    <!-- style="
    margin-left: -49px;
" -->

    <v-app-bar
      elevation="0"
      :color="changeColor"
      dark
      :clipped-left="clipped"
      fixed
      app
      :style="$nuxt.$route.name == 'index' ? 'z-index: 100000' : ''"
    >
      <v-app-bar-nav-icon @click.stop="drawer = !drawer" />
      <v-btn icon @click.stop="miniVariant = !miniVariant">
        <v-icon>mdi-{{ `chevron-${miniVariant ? "right" : "left"}` }}</v-icon>
      </v-btn>
      <v-btn icon @click.stop="clipped = !clipped">
        <v-icon>mdi-application</v-icon>
      </v-btn>
      {{ title }}
      <v-spacer></v-spacer>

      <v-menu
        nudge-bottom="50"
        transition="scale-transition"
        origin="center center"
        bottom
        left
        min-width="200"
        nudge-left="20"
      >
        <template v-slot:activator="{ on, attrs }">
          <label class="px-2" v-bind="attrs" v-on="on">
            {{ getUser }}
          </label>

          <v-btn icon color="yellow" v-bind="attrs" v-on="on">
            <v-avatar>
              <img :src="getLogo || '/no-image.PNG'" />
            </v-avatar>
          </v-btn>
        </template>

        <v-list light nav dense>
          <v-list-item-group color="primary">
            <v-list-item @click="goToCompany()">
              <v-list-item-icon>
                <v-icon>mdi-account-multiple-outline</v-icon>
              </v-list-item-icon>
              <v-list-item-content>
                <v-list-item-title class="black--text"
                  >Profile</v-list-item-title
                >
              </v-list-item-content>
            </v-list-item>

            <v-list-item @click="goToSetting()">
              <v-list-item-icon>
                <v-icon>mdi-cog</v-icon>
              </v-list-item-icon>
              <v-list-item-content>
                <v-list-item-title class="black--text"
                  >Setting</v-list-item-title
                >
              </v-list-item-content>
            </v-list-item>

            <v-list-item @click="logout">
              <v-list-item-icon>
                <v-icon>mdi-logout</v-icon>
              </v-list-item-icon>
              <v-list-item-content>
                <v-list-item-title class="black--text"
                  >Logout</v-list-item-title
                >
              </v-list-item-content>
            </v-list-item>
          </v-list-item-group>
        </v-list>
      </v-menu>
    </v-app-bar>

    <v-main class="main_bg">
      <!-- <v-container> -->
      <div class="mx-2 my-4">
        <nuxt />
      </div>
      <!-- </v-container> -->
    </v-main>
    <v-btn
      height="50"
      width="20"
      dark
      :color="changeColor"
      class="fixed-setting"
      @click.stop="rightDrawer = !rightDrawer"
    >
      <v-icon class="spin" dark size="25">mdi-cog</v-icon>
    </v-btn>
    <!-- setting -->
    <v-navigation-drawer
      v-model="rightDrawer"
      :clipped="true"
      :right="right"
      fixed
      style="z-index:1000"
    >
      <v-row style="margin-top: 50px;">
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
  mounted() {},
  data() {
    return {
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
          icon: "mdi-calendar",
          title: "Calendar",
          to: "/hotel/calendar1",
          menu: "reservation_access"
        },
        {
          icon: "mdi-bed",
          title: `Reservation`,
          open_menu: false,
          menu: "reservation_access",
          hasChildren: [
            // {
            //   icon: "mdi-bookmark ",
            //   title: "Bulk Reservation",
            //   to: "/reservation/bulk",
            //   menu: "reservation_access"
            // },
            // {
            //   icon: "mdi-bookmark ",
            //   title: "Add Reservation",
            //   to: "/reservation/create",
            //   menu: "reservation_access"
            // },
            {
              icon: "mdi-bed",
              title: "List Reservation",
              to: "/reservation/list1",
              menu: "reservation_access"
            }
          ]
        },
        // {
        //   icon: "mdi-bed",
        //   title: "Manage Room",
        //   to: "/room",
        //   menu: "room_access"
        // },
        {
          icon: "mdi-account",
          title: "Customers",
          to: "/customer/list",
          menu: "reservation_access"
        },

        {
          icon: "mdi-cash",
          title: "Posting",
          to: "/posting",
          menu: "posting_access"
        },

        {
          icon: "mdi-account",
          title: "Agents",
          to: "/agents",
          menu: "agent_access"
        },

        {
          icon: "mdi-account",
          title: "City Ledger",
          to: "/city_ledger",
          menu: "city_ledger_access"
        },

        {
          icon: "mdi-currency-usd",
          title: `Accounts`,
          open_menu: false,
          menu: "accounts_access",
          hasChildren: [
            // {
            //   icon: "mdi-account",
            //   title: "Customers Bill  ",
            //   to: "/customer/bill",
            //   menu: "customer_bill_access"
            // },
            {
              icon: "mdi-cash-multiple",
              title: "Income",
              to: "/account",
              menu: "income_access"
            },
            {
              icon: "mdi-cash",
              title: "Expense",
              to: "/expense",
              menu: "expense_access"
            }
          ]
        }
      ],

      // menus: [
      //   {
      //     icon: "mdi-briefcase-outline",
      //     title: `Organization`,
      //     open_menu: false,
      //     menu: "company_access",
      //     hasChildren: [
      //       {
      //         icon: "mdi-domain",
      //         title: "Department",
      //         to: "/department",
      //         menu: "department_access"
      //       },
      //       {
      //         icon: "mdi-lan",
      //         title: "Sub Department",
      //         to: "/sub-department",
      //         menu: "sub_department_access"
      //       },

      //       {
      //         icon: "mdi-treasure-chest ",
      //         title: "Designation",
      //         to: "/designation",
      //         menu: "designation_access"
      //       }
      //     ]
      //   },
      //   {
      //     icon: "mdi-door",
      //     title: "Devices",
      //     to: "/device",
      //     menu: "device_access"
      //   },
      //   {
      //     icon: "mdi-clipboard-edit-outline",
      //     title: "Policy",
      //     to: "/policy",
      //     menu: "policy_access"
      //   },
      //   {
      //     icon: "mdi-bullhorn-variant-outline",
      //     title: "Announcement",
      //     to: "/announcement",
      //     menu: "announcement_access"
      //   },
      //   {
      //     icon: "mdi-account",
      //     title: "Employees",
      //     to: "/employees",
      //     menu: "employee_access"
      //   },
      //   {
      //     icon: "mdi-clock",
      //     title: "Shift",
      //     to: "/shift",
      //     menu: "shift_access"
      //   },
      //   {
      //     icon: "mdi-account",
      //     title: "Employee Schedule",
      //     to: "/employee_schedule",
      //     menu: "employee_schedule_access"
      //   },

      //   {
      //     icon: "mdi-cash-multiple",
      //     title: `Payroll`,
      //     open_menu: false,
      //     menu: "payroll_access",
      //     hasChildren: [
      //       // {
      //       //   icon: "mdi-cash-multiple ",
      //       //   title: "Generate Month",
      //       //   to: "/payroll/month",
      //       //   menu:("payroll_generate_month_access")
      //       // },

      //       // {
      //       //   icon: "mdi-cash-multiple ",
      //       //   title: "Generate Payroll",
      //       //   to: "/employees",
      //       //   menu:("employee_schedule_access")
      //       // },
      //       {
      //         icon: "mdi-cash ",
      //         title: "Salary",
      //         to: "/payroll/salary",
      //         menu: "payroll_generate_access"
      //       }
      //       // {
      //       //   icon: "mdi-cash-multiple ",
      //       //   title: "Salary Advance",
      //       //   to: "/employee_schedule",
      //       //   menu:("employee_schedule_access")
      //       // }
      //     ]
      //   },

      //   {
      //     icon: "mdi-account",
      //     title: `User Management`,
      //     open_menu: false,
      //     menu: "payroll_access",
      //     hasChildren: [
      //       {
      //         icon: "mdi-account-plus",
      //         title: "Roles",
      //         to: "/role",
      //         menu: "role_access"
      //       },
      //       {
      //         icon: "mdi-lock",
      //         title: "Assign Permissions",
      //         to: "/assign_permission",
      //         menu: "assign_permission_access"
      //       }
      //     ]
      //   },

      //   {
      //     icon: "mdi-clipboard-text-clock",
      //     title: "Reports",
      //     to: "/attendance_report",
      //     menu: "attendance_report_access"
      //   },

      //   {
      //     icon: "mdi-email",
      //     title: "Notification",
      //     to: "/report_notifications",
      //     menu: "notifications_access"
      //   },
      //   {
      //     icon: "mdi-cog",
      //     title: "Setting",
      //     to: "/setting",
      //     menu: "setting_access"
      //   }
      // ],
      items: [],
      modules: {
        module_ids: [],
        module_names: []
      },
      clipped: true,

      miniVariant: false,
      title: "Hyder Park",
      logout_btn: {
        icon: "mdi-logout",
        label: "Logout"
      }
    };
  },
  created() {
    let das = {
      icon: "mdi-home",
      title: "Dashboard",
      to: "/",
      menu: "dashboard"
    };
    let user = this.$auth.user;
    let permissions = user.permissions;

    if (user && user.is_master) {
      this.items = this.menus;
      this.items.unshift(das);
      return;
    }

    this.menus.forEach(ele => {
      if (permissions.includes(ele.menu)) {
        this.items.push(ele);
      }
    });

    this.getCompanyDetails();
  },
  computed: {
    changeColor() {
      return this.$store.state.color;
    },

    getUser() {
      return this.$auth.user && this.$auth.user.company.name;
    },

    getLogo() {
      return this.$auth.user && this.$auth.user.company.logo;
    }
  },
  methods: {
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
      return str.replace(/\b\w/g, c => c.toUpperCase());
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
    getCompanyDetails() {
      let user = this.$auth.user;

      this.$axios.get(`company/${user?.company?.id}`).then(({ data }) => {
        let { modules } = data.record;

        if (modules !== null) {
          this.modules = {
            module_ids: modules.module_ids || [],
            module_names: modules.module_names.map(e => ({
              icon: "mdi-chart-bubble",
              title: this.caps(e),
              to: "/" + e + "_modules",
              permission: true
            }))
          };
        }
      });
    },
    can(per) {
      let user = this.$auth.user;
      return (
        (user && user.permissions.some(e => e == per || per == "/")) ||
        user.is_master
      );
    },

    async logout() {
      this.$axios.get(`/logout`).then(({ res }) => {
        this.$auth.logout();
      });
    }
  }
};
</script>
<style scoped>
.fixed-setting {
  position: fixed !important;
  top: 500px;
  z-index: 100000;
  transition: right 1000ms !important;
  right: -15px !important;
}

.v-btn__content {
  margin: 0 12px 0 0px !important;
  padding: 0 !important;
}

.setting-drawer-open {
  right: 250px !important;
}

.setting-drawer-close {
  right: -15px !important;
}

.spin {
  -webkit-animation: spin 4s linear infinite;
  -moz-animation: spin 4s linear infinite;
  animation: spin 4s linear infinite;

  margin: 0 12px 0 0px !important;
  padding: 0 !important;
}

@-moz-keyframes spin {
  100% {
    -moz-transform: rotate(360deg);
  }
}

@-webkit-keyframes spin {
  100% {
    -webkit-transform: rotate(360deg);
  }
}

@keyframes spin {
  100% {
    -webkit-transform: rotate(360deg);
    transform: rotate(360deg);
  }
}

.stg-color-icon {
  width: 30px !important;
  height: 30px !important;
}
</style>

<style>
.available {
  background: linear-gradient(135deg, #23bdb8 0, #65a986 100%) !important;
}

.booked {
  background: linear-gradient(135deg, #f48665 0, #d68e41 100%) !important;
}

.checkedIn {
  background: linear-gradient(135deg, #8e4cf1 0, #c554bc 100%) !important;
}

.checkedOut {
  background: linear-gradient(135deg, #289cf5, #4f8bb7) !important;
}

.dirty {
  background: linear-gradient(135deg, #34444c 0, #657177 100%) !important;
}

.maintenance {
  background: linear-gradient(135deg, #23bdb8 0, #65a986 100%) !important;
}
</style>
