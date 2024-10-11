<template>
  <v-card rounded="lg" outlined class="pa-2">
    <!-- :style="{ borderColor: options.color }"   -->
    <v-row no-gutter>
      <v-col :cols="options.col || '4'" class="pt-5 text-left">
        <v-row>
          <v-col cols="12" class="tex-center">
            <div class="text-center">
              <v-icon size="40" class="" :color="options.color">{{
                options.icon
              }}</v-icon>
              <div :class="`${options.color}--text`">
                <small>{{ options.label }}</small>
              </div>
            </div>
          </v-col>
        </v-row>
      </v-col>
      <v-col class="text-center mt-7">
        <h3 :class="`${options.color}--text`">
          {{ options.value }}
        </h3>
      </v-col>
    </v-row>
  </v-card>
</template>
<script>
export default {
  props: ["options"],
};
</script>
