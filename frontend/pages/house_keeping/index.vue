<template>
  <v-container fluid>
    <!-- Dialog for large image -->
    <v-dialog v-model="imageDialog" max-width="800px">
      <AssetsIconClose left="790" @click="imageDialog = false" />
      <v-card>
        <v-container>
          <v-img :src="selectedImage" contain max-width="100%"></v-img>
        </v-container>
      </v-card>
    </v-dialog>
    <v-dialog v-model="dialog" max-width="650px" persistent>
      <AssetsIconClose left="640" @click="dialog = false" />
      <v-card v-if="selectedItem">
        <v-alert dense flat color="grey lighten-3"> Cleaning Info </v-alert>
        <v-card-text class="d-flex justify-center">
          <v-container>
            <v-row>
              <!-- Date & Time -->
              <v-col cols="4">
                <v-card outlined>
                  <v-card-title class="py-1" style="font-size: 14px"
                    >Start Time</v-card-title
                  >
                  <v-card-text class="py-1" style="font-size: 13px">
                    {{ selectedItem.start_time }}
                  </v-card-text>
                </v-card>
              </v-col>

              <v-col cols="4">
                <v-card outlined>
                  <v-card-title class="py-1" style="font-size: 14px"
                    >End Time</v-card-title
                  >
                  <v-card-text class="py-1" style="font-size: 13px">{{
                    selectedItem.end_time
                  }}</v-card-text>
                </v-card>
              </v-col>

              <!-- Total Time -->
              <v-col cols="4">
                <v-card outlined>
                  <v-card-title class="py-1" style="font-size: 14px"
                    >Total Time</v-card-title
                  >
                  <v-card-text class="py-1" style="font-size: 13px">{{
                    selectedItem.total_time
                  }}</v-card-text>
                </v-card>
              </v-col>

              <!-- Room Info -->
              <v-col cols="12">
                <v-card outlined>
                  <v-card-title class="py-1" style="font-size: 14px"
                    >Room</v-card-title
                  >
                  <v-card-text class="py-1" style="font-size: 13px">
                    {{ selectedItem.room.room_no }} -
                    {{ selectedItem.room.room_type.name }}
                  </v-card-text>
                </v-card>
              </v-col>

              <!-- Cleaned By User -->
              <v-col cols="12">
                <v-card outlined>
                  <v-card-title class="py-1" style="font-size: 14px"
                    >Cleaned By</v-card-title
                  >
                  <v-card-text class="py-1" style="font-size: 13px">{{
                    selectedItem.cleaned_by_user.name
                  }}</v-card-text>
                </v-card>
              </v-col>

              <!-- Voice Note -->
              <v-col cols="12">
                <v-card outlined>
                  <v-card-title class="py-1" style="font-size: 14px"
                    >Voice Note</v-card-title
                  >
                  <v-card-text class="py-1" style="font-size: 13px">
                    <div v-if="selectedItem.voice_note">
                      <audio :src="selectedItem.voice_note" controls></audio>
                    </div>
                    <div v-else>No voice note</div>
                  </v-card-text>
                </v-card>
              </v-col>

              <!-- Attachments -->
              <v-col cols="12">
                <v-card outlined>
                  <v-card-title class="py-1" style="font-size: 14px"
                    >Attachments</v-card-title
                  >
                  <v-card-text class="py-1" style="font-size: 13px">
                    <div v-if="selectedItem.attachments.length">
                      <v-row>
                        <v-col
                          v-for="(att, index) in selectedItem.attachments"
                          :key="index"
                          class="d-flex child-flex"
                          cols="4"
                        >
                          <v-img
                            :src="att"
                            :lazy-src="att"
                            aspect-ratio="1"
                            class="grey lighten-2"
                            @click="openImage(att)"
                          >
                            <template v-slot:placeholder>
                              <v-row
                                class="fill-height ma-0"
                                align="center"
                                justify="center"
                              >
                                <v-progress-circular
                                  indeterminate
                                  color="grey lighten-5"
                                ></v-progress-circular>
                              </v-row>
                            </template>
                          </v-img>
                        </v-col>
                      </v-row>
                    </div>
                    <div v-else>No attachments</div>
                  </v-card-text>

                  <style scoped>
                    .bordered-img {
                      border: 2px solid #1976d2; /* blue border, change color if you like */
                      padding: 2px; /* optional: small padding inside border */
                    }
                  </style>
                </v-card>
              </v-col>
            </v-row>
          </v-container>
        </v-card-text>
      </v-card>
    </v-dialog>
    <v-card>
      <v-container>
        <v-row>
          <v-col cols="10"> Room Cleaning Info </v-col>
          <v-col cols="2">
            <FilterDateRange @filter-attr="filterAttr" />
          </v-col>
          <v-col cols="12">
            <v-data-table
              dense
              :headers="headers"
              :items="data"
              :loading="loading"
              :options.sync="options"
              :footer-props="{
                itemsPerPageOptions: [100, 500, 1000],
              }"
            >
              <template v-slot:item.room_no="{ item }">
                {{ item.room.room_no }}
              </template>
              <template v-slot:item.cleaned_by_user="{ item }">
                {{ item?.cleaned_by_user?.name }}
              </template>
              <template v-slot:item.status="{ item }">
                <v-icon v-if="item.status == 'Dirty'" color="error"
                  >mdi-emoticon-sad</v-icon
                >
                <v-icon v-else-if="item.status == 'Cleaned'" color="success"
                  >mdi-emoticon-happy</v-icon
                >
                <v-icon
                  v-else-if="item.status == 'Neutral'"
                  color="yellow darken-3"
                  >mdi-emoticon-neutral</v-icon
                >
                <v-icon v-else color="gray">mdi-emoticon-neutral</v-icon>
              </template>
              <template v-slot:item.start_date_time="{ item }">
                {{ $dateFormat.dmy(item.created_at) }} {{ item.start_time }}
              </template>

              <template v-slot:item.end_date_time="{ item }">
                <span
                  v-if="
                    item.status !== 'Dirty' &&
                    item.status !== 'Cleaned' &&
                    item.status !== 'Neutral'
                  "
                >
                  {{ item.status }}
                </span>
                <span v-else>
                  {{ $dateFormat.dmy(item.created_at) }} {{ item.end_time }}
                </span>
              </template>

              <template v-slot:item.options="{ item }">
                <v-menu bottom left>
                  <template v-slot:activator="{ on, attrs }">
                    <v-btn dark-2 icon v-bind="attrs" v-on="on">
                      <v-icon>mdi-dots-vertical</v-icon>
                    </v-btn>
                  </template>
                  <v-list dense>
                    <v-list-item @click="openDialog(item)">
                      <v-list-item-title style="cursor: pointer">
                        <v-icon small color="primary"> mdi-eye </v-icon>
                        <AssetsTextLabel color="text-color" label="View" />
                      </v-list-item-title>
                    </v-list-item>
                  </v-list>
                </v-menu>
              </template>

              <!-- <template v-slot:item.attachments="{ item }">
        <div style="display: flex; gap: 8px; flex-wrap: wrap" class="pa-5">
          <v-img
            v-for="(attachment, index) in item.attachments"
            :key="index"
            :src="attachment"
            max-width="60"
            max-height="60"
            class="thumbnail pa-5"
            style="cursor: pointer; border-radius: 4px"
            @click="openDialog(attachment)"
            contain
            elevation="2"
          />
        </div>
      </template> -->
              <!-- <template v-slot:item.voice_note="{ item }">
        <v-container class="pa-1">
          <audio
            v-if="item.voice_note"
            controls
            style="width: 250px; height: 40px"
          >
            <source :src="item.voice_note" />
          </audio>
          <span v-else>---</span>
        </v-container>
      </template> -->
            </v-data-table>
          </v-col>
        </v-row>
      </v-container>
    </v-card>
  </v-container>
</template>

<script>
let date = new Date();

let d = date.getDate();
let m = (date.getMonth() + 1).toString().padStart(2, "0");
let y = date.getFullYear();
let currentDate = y + "-" + m + "-" + d;

export default {
  data: () => ({
    imageDialog: false,
    selectedImage: null,
    dialog: false,
    selectedItem: null,
    Model: "House Keeping",
    endpoint: "room-data",
    currentDate,
    filters: {
      from_date: new Date().toJSON().slice(0, 10),
      to_date: new Date().toJSON().slice(0, 10),
    },
    options: {},
    loading: false,
    response: "",
    data: [],
    errors: [],
    headers: [
      {
        text: "Room",
        value: "room_no",
      },

      {
        text: "Start DateTime",
        value: "start_date_time",
      },
      {
        text: "End DateTime",
        value: "end_date_time",
      },
      {
        text: "Clean By",
        value: "cleaned_by_user",
      },
      // {
      //   text: "Response by",
      //   value: "response_by_user.name",
      // },
      {
        text: "Status",
        value: "status",
      },
      {
        text: "Action",
        align: "center",
        sortable: false,
        value: "options",
      },
    ],
    componentKey: 1,
  }),
  watch: {
    options: {
      handler() {
        this.getDataFromApi();
      },
      deep: true,
    },
  },
  methods: {
    filterAttr(data) {
      this.filters = {
        from_date: data.from,
        to_date: data.to,
      };

      this.getDataFromApi();
    },
    openImage(img) {
      this.selectedImage = img;
      this.imageDialog = true;
    },
    openDialog(item) {
      this.selectedItem = item;
      this.dialog = true;
    },
    getRandomId() {
      return ++this.componentKey;
    },
    async getDataFromApi() {
      this.loading = true;

      try {
        const { data } = await this.$axios.get(this.endpoint, {
          params: { ...this.filters },
        });

        this.data = data?.data ?? []; // safer: fallback to empty array
      } catch (error) {
        console.error("API Error:", error);
        this.data = [];
      } finally {
        this.loading = false; // ensures loading stops even if error
      }
    },
  },
};
</script>
