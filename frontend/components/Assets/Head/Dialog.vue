<template>
  <v-alert flat class="grey lighten-3" dense>
    <v-row>
      <v-col><slot name="label">Model</slot></v-col>
      <v-col cols="6" class="text-right">
        <slot name="search"></slot>
        <slot name="icon"></slot
      ></v-col>
    </v-row>
  </v-alert>
</template>
