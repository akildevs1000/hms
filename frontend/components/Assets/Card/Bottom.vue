<template>
  <v-card rounded="lg" outlined class="pa-2">
    <!-- :style="{ borderColor: options.color }"   -->
    <v-row no-gutter class="px-3">
      <v-col :cols="options.col || '4'" class="text-left">
        <v-row>
          <v-col cols="12" class="">
            <div class="">
              <div :class="`${options.color}--text`">
                {{ options.label }}
              </div>
            </div>
          </v-col>
        </v-row>
      </v-col>
      <v-col class="text-right">
        <span :class="`${options.color}--text`">
          {{ options.value }}
        </span>
      </v-col>
    </v-row>
  </v-card>
</template>
<script>
export default {
  props: ["options"],
};
</script>
