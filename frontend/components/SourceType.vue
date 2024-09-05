<template>
  <v-row>
    <v-col>
      <v-autocomplete
        v-model="sourceType.type"
        label="Source Type *"
        :items="types"
        dense
        outlined
        @change="getType(sourceType.type)"
        hide-details
      ></v-autocomplete>
    </v-col>
    <v-col>
      <v-text-field
        small
        readonly
        v-model="displayObject.name"
        label="Source Name"
        dense
        outlined
        hide-details
      >
        <template v-slot:append>
          <v-icon small color="primary" right @click="sourceDialog = true"
            >mdi-eye</v-icon
          >
        </template>
      </v-text-field>
    </v-col>
    <v-dialog persistent v-model="sourceDialog" width="550">
      <v-card>
        <v-toolbar dense flat>
          Source Type <v-spacer></v-spacer
          ><v-icon @click="close" color="primary">mdi-close</v-icon></v-toolbar
        >
        <v-card-text>
          <v-container>
            <v-row>
              <v-col cols="12" v-if="isAgent">
                <v-autocomplete
                  dense
                  label="Agent Name"
                  outlined
                  :items="agentList"
                  type="text"
                  @change="searchByName(agentList, sourceType.source)"
                  item-value="name"
                  item-text="name"
                  v-model="sourceType.source"
                  hide-details
                ></v-autocomplete>
              </v-col>
              <v-col cols="12" dense v-if="isOnline">
                <v-autocomplete
                  v-model="sourceType.source"
                  label="Source"
                  :items="sources"
                  dense
                  @change="searchByName(sources, sourceType.source)"
                  outlined
                  item-value="name"
                  item-text="name"
                  hide-details
                ></v-autocomplete>
              </v-col>
              <v-col md="12" sm="12" cols="12" dense v-if="isCorporate">
                <v-autocomplete
                  v-model="sourceType.source"
                  label="Corporate"
                  :items="CorporateList"
                  dense
                  outlined
                  @change="searchByName(CorporateList, sourceType.source)"
                  item-value="name"
                  item-text="name"
                  hide-details
                ></v-autocomplete>
              </v-col>

              <v-col cols="12" v-if="isAgent || isOnline || isCorporate">
                <v-row>
                  <v-col cols="6">
                    <v-text-field
                      v-model="response.name"
                      label="Name"
                      dense
                      outlined
                      hide-details
                    ></v-text-field>
                  </v-col>
                  <v-col cols="6">
                    <v-text-field
                      v-model="response.type"
                      label="Type"
                      dense
                      outlined
                      hide-details
                    ></v-text-field>
                  </v-col>
                  <v-col cols="6">
                    <v-text-field
                      v-model="response.mobile"
                      label="Mobile"
                      dense
                      outlined
                      hide-details
                    ></v-text-field>
                  </v-col>
                  <v-col cols="6">
                    <v-text-field
                      v-model="response.landline"
                      label="Landline"
                      dense
                      outlined
                      hide-details
                    ></v-text-field>
                  </v-col>
                  <v-col cols="6">
                    <v-text-field
                      v-model="response.email"
                      label="Email"
                      dense
                      outlined
                      hide-details
                    ></v-text-field>
                  </v-col>
                  <v-col cols="6">
                    <v-text-field
                      v-model="response.gst"
                      label="GST"
                      dense
                      outlined
                      hide-details
                    ></v-text-field>
                  </v-col>
                  <v-col cols="12">
                    <v-textarea
                      v-model="response.address"
                      rows="2"
                      label="Address"
                      dense
                      outlined
                      hide-details
                    ></v-textarea>
                  </v-col>
                  <v-col cols="6">
                    <v-text-field
                      label="Reference Number"
                      dense
                      outlined
                      hide-details
                      v-model="sourceType.reference_no"
                    ></v-text-field>
                  </v-col>
                  <v-col cols="6">
                    <v-autocomplete
                      v-model="sourceType.paid_by"
                      label="Paid Type"
                      :items="[
                        { name: 'Paid at Hotel', value: '1' },
                        { name: 'Paid by Agents', value: '2' },
                      ]"
                      dense
                      outlined
                      item-value="value"
                      item-text="name"
                      hide-details
                    ></v-autocomplete>
                  </v-col>
                  <v-col cols="12" class="text-right">
                    <v-btn small color="primary" @click="submit">Submit</v-btn>
                  </v-col>
                </v-row>
              </v-col>
            </v-row>
          </v-container>
        </v-card-text>
      </v-card>
    </v-dialog>
  </v-row>
</template>
<script>
export default {
  props: {
    defaultSource: {
      type: Object,
      default: () => ({
        type: null,
        source: null,
        reference_no: null,
        paid_by: null,
      }),
    },
    isOverride: {
      type: Boolean,
      default: () => false,
    },
  },
  data: () => ({
    types: ["Online", "Walking", "Travel Agency", "Complimentary", "Corporate"],
    filteredSearch: [],
    sourceDialog: false,
    displayObject: {},
    response: {
      name: null,
      type: null,
      mobile: null,
      landline: null,
      email: null,
      gst: null,
      address: null,
    },
    sourceType: {},
    isOnline: false,
    isCorporate: false,
    isAgent: false,
    gst_number: null,
  }),
  async created() {
    if (this.isOverride) {
      this.sourceType.type = this.defaultSource.type;
      this.displayObject.name = this.defaultSource.source;
      this.$emit("sourceObject", this.defaultSource);
      return;
    }
    this.defaultSource.type = this.defaultSource.type;
    this.displayObject.name = this.defaultSource.source;
    this.$emit("sourceObject", this.defaultSource);
    this.get_agents();
    this.get_online();
    this.get_Corporate();
  },
  methods: {
    submit() {
      let payload = {
        type: this.sourceType.type,
        source: this.response.name,
        reference_no: this.sourceType.reference_no,
        paid_by: this.sourceType.paid_by,
      };

      this.$emit("sourceObject", payload);
      this.displayObject = this.response;
      this.sourceDialog = false;
    },
    close() {
      this.sourceDialog = false;
      //   this.sourceType = this.defaultSourceType;
      //   this.isOnline = false;
      //   this.isCorporate = false;
      //   this.isAgent = false;
      //   this.gst_number = null;
    },
    get_gst(item, type) {
      switch (type) {
        case "agent":
          this.gst_number = this.agentList.find((e) => e.name == item).gst;
          break;
        case "online":
          this.gst_number = this.sources.find((e) => e.name == item).gst;
          break;
        case "corporate":
          this.gst_number = this.CorporateList.find((e) => e.name == item).gst;
          break;
        default:
          break;
      }
    },
    get_agents() {
      let payload = {
        params: {
          company_id: this.$auth.user.company.id,
        },
      };
      this.$axios.get(`get_agent`, payload).then(({ data }) => {
        this.agentList = data;
      });
    },
    get_online() {
      let payload = {
        params: {
          company_id: this.$auth.user.company.id,
        },
      };
      this.$axios.get(`get_online`, payload).then(({ data }) => {
        this.sources = data;
      });
    },
    get_Corporate() {
      let payload = {
        params: {
          company_id: this.$auth.user.company.id,
        },
      };
      this.$axios.get(`get_corporate`, payload).then(({ data }) => {
        this.CorporateList = data;
      });
    },
    getType(val) {
      if (val == "Walking" || val == "Complimentary") {
        let payload = {
          type: this.sourceType.type,
          source: "---",
          reference_no: "---",
          paid_by: "---",
        };

        this.$emit("sourceObject", payload);
        return;
      }

      this.sourceDialog = true;

      if (val == "Online") {
        this.isOnline = true;
        this.isCorporate = false;
        this.isAgent = false;
        return;
      }
      if (val == "Travel Agency") {
        this.isCorporate = false;
        this.isOnline = false;
        this.isAgent = true;

        return;
      }
      if (val == "Corporate") {
        this.isOnline = false;
        this.isAgent = false;
        this.isCorporate = true;
        return;
      }
    },
    searchByName(items, search) {
      if (!search) {
        this.response = {
          name: null,
          type: null,
          mobile: null,
          landline: null,
          email: null,
          gst: null,
          address: null,
        };

        return;
      }

      let result = items.find(({ name }) => name && name.includes(search));

      this.response = {
        name: result.name,
        type: result.type,
        mobile: result.mobile,
        landline: result.landline,
        email: result.email,
        gst: result.gst,
        address: result.address,
      };
    },
  },
};
</script>
