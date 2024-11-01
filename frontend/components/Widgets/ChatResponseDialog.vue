<template>
  <v-dialog v-model="dialog" persistent max-width="400px">
    <AssetsIconClose left="390" @click="closeDialog" />

    <template v-slot:activator="{ on, attrs }">
      <v-icon
        v-bind="attrs"
        v-on="on"
        small
        :color="getRelatedColor(item.status)"
        >{{ getRelatedIcon(item.status) }}</v-icon
      >
    </template>

    <v-card>
      <v-alert class="grey lighten-3" flat dense>Chat</v-alert>
      <v-card-text>
        <v-form ref="form" v-model="valid" lazy-validation>
          <v-text-field
            outlined
            dense
            v-model="newMessage"
            label="Type Message..."
            :rules="textRules"
            required
          ></v-text-field>
          <v-autocomplete
            outlined
            dense
            v-model="selectedStatus"
            :items="statusses"
            label="Select Status"
            :rules="selectRules"
            required
          ></v-autocomplete>
          <div v-if="errorResponse" class="red--text mb-1">
            {{ errorResponse }}
          </div>
          <div class="text-center">
            <AssetsButton
              :options="{ icon: ``, color: `red`, label: `Cancel` }"
              @click="dialog = false"
            />
            <AssetsButton
              :options="{ icon: ``, color: `green`, label: `Submit` }"
              @click="submit"
            />
          </div>
        </v-form>
      </v-card-text>
    </v-card>
  </v-dialog>
</template>

<script>
export default {
  props: ["item"],
  data() {
    return {
      newMessage: "",
      errorResponse: null,
      dialog: false,
      valid: false,
      textInput: "",
      selectedStatus: "Open",
      statusses: ["Open", "Closed", "Forward"],
      textRules: [
        (v) => !!v || "Text is required",
        (v) => (v && v.length <= 20) || "Text must be less than 20 characters",
      ],
      selectRules: [(v) => !!v || "Selection is required"],
    };
  },
  created(){
    this.selectedStatus = this.item.status;
  },
  methods: {
    getRelatedColor(status) {
      let statusObject = {
        Open: "red",
        Forward: "purple",
        Closed: "green",
      };
      return statusObject[status];
    },
    getRelatedIcon(status) {
      let statusObject = {
        Open: "mdi-lock-outline",
        Forward: "mdi-lock-open-outline",
        Closed: "mdi-lock-outline",
      };
      return statusObject[status];
    },
    openDialog() {
      this.dialog = true;
    },
    async submit() {
      try {
        let payload = {
          message: this.newMessage,
          sender_id: this.$auth.user.id,
          receiver_id: this.item.sender_id,
          company_id: this.$auth.user.company_id,
          is_reponse: true,
        };

        await this.$axios.post(`chat`, payload); // send reply
        await this.update(); // update chat status
        this.closeDialog();
      } catch (error) {
        this.errorResponse = error?.response?.data?.message;
      }
    },
    async update() {
      let { data } = await this.$axios.post(`update-chat-status`, {
        id: this.item.id,
        status: this.selectedStatus,
      });
      console.log(data);
    },
    closeDialog() {
      this.dialog = false;
      this.resetForm();
    },
    resetForm() {
      this.errorResponse = null,
      this.newMessage = "";
      this.selectedStatus = "Open";
    },
  },
};
</script>
