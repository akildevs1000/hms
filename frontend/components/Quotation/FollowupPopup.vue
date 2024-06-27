<template>
  <div>
    <v-dialog v-model="dialog" max-width="600">
      <template v-slot:activator="{ on, attrs }">
        <v-icon v-bind="attrs" v-on="on" color="blue">mdi-clock-outline</v-icon>
      </template>
      <v-card elevation="0">
        <v-toolbar flat dense
          >Follow Up<v-spacer></v-spacer
          ><v-icon @click="dialog = false" color="blue"
            >mdi-close-circle-outline</v-icon
          ></v-toolbar
        >
        <v-divider></v-divider>
        <v-container>
          <v-timeline dense>
            <v-timeline-item
              v-for="(item, index) in followups"
              :key="index"
              color=""
            >
              <span slot="icon"
                ><v-icon color="primary">mdi-clock-outline</v-icon></span
              >
              <v-card outlined>
                <v-card-text>
                  <div>
                    {{ item.date }}
                  </div>
                  <small>{{ item.description }}</small>

                  <br /><small
                    ><strong>By {{ item?.user?.email }}</strong></small
                  >
                </v-card-text>
              </v-card>
            </v-timeline-item>
          </v-timeline>
        </v-container>
      </v-card>
    </v-dialog>
  </div>
</template>
<script>
export default {
  props: ["followups"],
  data: () => ({
    dialog: false,
  }),
};
</script>
