<template>
  <v-row>
    <!-- <v-col cols="3" class="pr-0" style="max-width: 20%"></v-col> -->
    <!-- Food Card -->
     
    <v-col cols="6">
      <v-card class="elevation-2" style="height: 180px">
        <v-card-text>
          <v-row style="margin-top: -12px"
            ><v-col cols="8" style="color: black; font-size: 12px"
              ><v-icon color="pink" class="mr-2">mdi-food</v-icon>
              <span>Food</span></v-col
            >
          </v-row>
          <v-divider color="#DDD" style="margin-bottom: 10px" />
        </v-card-text>
      </v-card>
    </v-col>

    <v-col cols="6">
      <v-card class="elevation-2" style="height: 180px">
        <v-card-text>
          <v-row style="margin-top: -12px"
            ><v-col cols="8" style="color: black; font-size: 12px"
              ><v-icon color="pink" class="mr-2">mdi-food</v-icon>
              <span>Food</span></v-col
            >
          </v-row>
          <v-divider color="#DDD" style="margin-bottom: 10px" />
        </v-card-text>
      </v-card>
    </v-col>

    <!-- Occupancy Card -->
    <v-col cols="6">
      <!-- <v-card outlined class="pa-4">
        <v-card-title>
          <v-icon color="pink" class="mr-2">mdi-human-male-female-child</v-icon>
          <span>Occupancy</span>
        </v-card-title>
        <v-card-text>
          <v-list-item>
            <v-list-item-content>
              <v-list-item-title>
                <v-chip small color="green" class="mr-2" /> Adult
              </v-list-item-title>
            </v-list-item-content>
            <v-list-item-content class="text-right">
              <span>2</span>
            </v-list-item-content>
          </v-list-item>

          <v-list-item>
            <v-list-item-content>
              <v-list-item-title>
                <v-chip small color="red" class="mr-2" /> Children
              </v-list-item-title>
            </v-list-item-content>
            <v-list-item-content class="text-right">
              <span>0</span>
            </v-list-item-content>
          </v-list-item>
        </v-card-text>
      </v-card> -->
    </v-col>
  </v-row>
</template>

<script>
export default {
  // name: 'FoodOccupancyCard'
};
</script>
