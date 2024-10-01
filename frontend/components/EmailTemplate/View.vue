<template>
  <v-dialog persistent v-model="dialog" width="600">
    <template v-slot:activator="{ on, attrs }">
      <div v-bind="attrs" v-on="on">
        <v-icon color="blue" small> mdi-eye </v-icon>
        View
      </div>
    </template>

    <v-card>
      <v-toolbar flat class="grey lighten-3" dense>
        View {{ model }} <v-spacer></v-spacer
        ><AssetsButtonClose @close="close" /></v-toolbar
      >

      <v-card-text class="py-5">
        <v-container>
          <v-row>
            <v-col cols="6">
              <v-text-field
                readonly
                outlined
                dense
                hide-details
                v-model="payload.name"
                label="Name"
              ></v-text-field>
            </v-col>
            <v-col cols="6">
              <v-autocomplete
                readonly
                outlined
                dense
                hide-details
                :items="templateTypes"
                item-value="id"
                item-text="name"
                v-model="payload.action_id"
                label="Action"
              ></v-autocomplete>
            </v-col>
            <v-col cols="12">
              <span v-html="payload.body"> </span>
            </v-col>
            <v-col cols="12" v-if="errorResponse">
              <span class="red--text">{{ errorResponse }}</span>
            </v-col>
            <v-col cols="12" class="text-right">
              <v-btn small color="grey" class="white--text" dark @click="close">
                Close
              </v-btn>
              <v-btn
                :loading="loading"
                small
                color="blue"
                class="white--text"
                dark
                @click="submit"
              >
                Submit
              </v-btn>
            </v-col>
          </v-row>
        </v-container>
      </v-card-text>
    </v-card>
  </v-dialog>
</template>
<script>
import {
  TiptapVuetify,
  Image,
  Heading,
  Bold,
  Italic,
  Strike,
  Underline,
  Code,
  Paragraph,
  BulletList,
  OrderedList,
  ListItem,
  Link,
  Blockquote,
  HardBreak,
  HorizontalRule,
  History,
} from "tiptap-vuetify";

export default {
  props: ["item", "endpoint", "model"],
  components: { TiptapVuetify },
  data() {
    return {
      extensions: [
        History,
        Blockquote,
        Link,
        Image,
        Underline,
        Strike,
        Italic,
        ListItem,
        BulletList,
        OrderedList,
        [
          Heading,
          {
            options: {
              levels: [1, 2, 3],
            },
          },
        ],
        Bold,
        Link,
        Code,
        HorizontalRule,
        Paragraph,
        HardBreak,
      ],
      payload: {
        name: "",
        salutation: "",
        body: "",
        action_id: 0,
        company_id: 0,
      },
      dialog: false,
      loading: false,
      successResponse: null,
      errorResponse: null,
      templateTypes:[],
    };
  },
  async created() {
    try {
      let { data } = await this.$axios.get(`template-types`);
      this.templateTypes = data;
    } catch (error) {
      console.log(error);
    }
    this.payload = this.item;
  },
  methods: {
    close() {
      this.dialog = false;
      this.loading = false;
      this.errorResponse = null;
    },
    async submit() {
      this.loading = true;
      try {
        await this.$axios.put(
          `${this.endpoint}/${this.payload.id}`,
          this.payload
        );
        this.close();
        this.$emit("response", "Record has been inserted");
      } catch (error) {
        this.errorResponse = error?.response?.data?.message || "Unknown error";
        this.loading = false;
      }
    },
  },
};
</script>
