<template>
  <div style="max-height: 450px; overflow-y: auto">
    <v-card v-for="(menu, index) in topMenus" :key="index" class="mb-2" flat>
      <v-row no-gutters>
        <v-col>
          <div class="text-color mx-3 my-1">
            {{ menu.label }}
          </div>
        </v-col>
        <v-col class="text-right">
          <div class="mx-3 my-1">
            <v-icon color="text-color " small text @click="toggleExpand(index)">
              mdi-chevron-down
            </v-icon>
          </div>
        </v-col>
      </v-row>
      <v-expand-transition>
        <v-card-text
          v-if="expandedIndex === index"
          style="max-height: 250px; overflow-y: scroll"
        >
          <table>
            <tr>
              <td style="width: 30%" class="border-bottom">
                <div class="text-color"></div>
              </td>
              <td class="border-bottom">
                <div class="text-color text-center">Access</div>
              </td>
              <td class="border-bottom">
                <div class="text-color text-center">View</div>
              </td>
              <td class="border-bottom">
                <div class="text-color text-center">Create</div>
              </td>
              <td class="border-bottom">
                <div class="text-color text-center">Edit</div>
              </td>
              <td class="border-bottom">
                <div class="text-color text-center">Delete</div>
              </td>
            </tr>
            <tr>
              <td style="width: 30%" class="border-bottom">
                <div class="text-color">
                  <b>Select All</b>
                </div>
              </td>
              <td class="border-bottom">
                <div
                  class="text-color text-center d-flex align-center justify-center"
                >
                  <v-checkbox
                    class="py-1 pl-1 ma-0"
                    color="purple"
                    dense
                    hide-details
                    @change="
                      (isChecked) =>
                        selectAllByfilteredMenus('access', menu.name, isChecked)
                    "
                  ></v-checkbox>
                </div>
              </td>
              <td class="border-bottom">
                <div
                  class="text-color text-center d-flex align-center justify-center"
                >
                  <v-checkbox
                    class="py-1 pl-1 ma-0"
                    color="purple"
                    dense
                    hide-details
                    @change="
                      (isChecked) =>
                        selectAllByfilteredMenus('view', menu.name, isChecked)
                    "
                  ></v-checkbox>
                </div>
              </td>
              <td class="border-bottom">
                <div
                  class="text-color text-center d-flex align-center justify-center"
                >
                  <v-checkbox
                    class="py-1 pl-1 ma-0"
                    color="purple"
                    dense
                    hide-details
                    @change="
                      (isChecked) =>
                        selectAllByfilteredMenus('create', menu.name, isChecked)
                    "
                  ></v-checkbox>
                </div>
              </td>
              <td class="border-bottom">
                <div
                  class="text-color text-center d-flex align-center justify-center"
                >
                  <v-checkbox
                    class="py-1 pl-1 ma-0"
                    color="purple"
                    dense
                    hide-details
                    @change="
                      (isChecked) =>
                        selectAllByfilteredMenus('edit', menu.name, isChecked)
                    "
                  ></v-checkbox>
                </div>
              </td>
              <td class="border-bottom">
                <div
                  class="text-color text-center d-flex align-center justify-center"
                >
                  <v-checkbox
                    class="py-1 pl-1 ma-0"
                    color="purple"
                    dense
                    hide-details
                    @change="
                      (isChecked) =>
                        selectAllByfilteredMenus('delete', menu.name, isChecked)
                    "
                  ></v-checkbox>
                </div>
              </td>
            </tr>
            <tr v-for="(childMenu, idx) in filteredMenus(menu.name)" :key="idx">
              <td style="width: 30%" class="border-bottom">
                <div class="text-color">
                  {{ childMenu.title }}
                </div>
              </td>
              <td
                class="border-bottom"
                v-for="(perm, permIndex) in filteredPermissions(
                  childMenu.module
                )"
                :key="permIndex"
              >
                <div class="text-center d-flex align-center justify-center">
                  <v-checkbox
                    class="py-1 pl-1 ma-0"
                    color="purple"
                    :value="perm.id"
                    v-model="permission_ids"
                    dense
                    hide-details
                    @change="$emit(`selectedPermissions`, permission_ids)"
                  >
                  </v-checkbox>
                </div>
              </td>
            </tr>
          </table>
        </v-card-text>
      </v-expand-transition>
      <v-divider></v-divider>
    </v-card>
  </div>
</template>

<script>
export default {
  props: ["permissions", "defaultPermissionsIds"],
  data() {
    return {
      expandedIndex: null, // Index of the currently expanded item
      permission_ids: [],
      topMenus: [
        { label: "Dashboard", name: "dashboard" },
        { label: "Customer", name: "customer" },
        { label: "Account", name: "account" },
        { label: "Sales", name: "sales" },
        { label: "Reports", name: "reports" },
        { label: "Setting", name: "setting" },
      ],
      menus: [
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
          topMenu: "dashboard",
          icon: "mdi-bed",
          module: "history",
          title: "History",
          to: "/history",
          menu: "guest_access",
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
        {
          topMenu: "account",
          icon: "mdi-account",
          module: "vendors",
          title: "Vendor",
          to: "/vendors",
          menu: "accounts_posting_access",
        },
        {
          topMenu: "dashboard",
          icon: "mdi mdi-file-chart-outline",
          module: "night_audit",
          title: "Night Audit",
          to: "/management/report/audit",
          menu: "night_audit_access",
        },
        {
          topMenu: "dashboard",
          icon: "mdi-home-search-outline",
          module: "lost_and_found_items",
          title: "Lost & Found  ",
          to: "/lost_and_found_items",
          menu: "lost_and_found_access",
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
          icon: "mdi-cog",
          module: "setup",
          title: "Setup",
          to: "/setup",
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
          icon: "mdi mdi-account-details",
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
      ],
    };
  },
  mounted() {
    this.permission_ids = this.defaultPermissionsIds;
  },
  watch: {
    defaultPermissionsIds(newVal) {
      this.permission_ids = newVal;
    },
  },

  methods: {
    toggleExpand(index) {
      // If the index is already expanded, collapse it; otherwise, expand it
      this.expandedIndex = this.expandedIndex === index ? null : index;
    },
    filteredPermissions(module) {
      return this.permissions[module.toLocaleLowerCase()];
    },
    filteredMenus(topMenuName) {
      return this.menus.filter((menu) => menu.topMenu === topMenuName);
    },
    selectAllByfilteredMenus(action, topMenuName, isChecked) {
      const actionLower = action.toLocaleLowerCase();

      // Get all permission IDs for the given action under the specified topMenuName
      const allPermissions = this.menus
        .filter((menu) => menu.topMenu === topMenuName)
        .flatMap((menu) =>
          this.filteredPermissions(menu.module)
            .filter(
              (permission) =>
                permission.title.toLocaleLowerCase() === actionLower
            )
            .map((permission) => permission.id)
        );

      if (isChecked) {
        // If checked, add all permissions to permission_ids
        this.permission_ids = [
          ...new Set([...this.permission_ids, ...allPermissions]),
        ];
      } else {
        // If unchecked, remove all permissions for this action
        this.permission_ids = this.permission_ids.filter(
          (id) => !allPermissions.includes(id)
        );
      }

      this.$emit("selectedPermissions", this.permission_ids);
    },
  },
};
</script>
