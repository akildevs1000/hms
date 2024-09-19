<template>
  <nuxt />
</template>

<style scoped>
body {
  background-color: #f4feff;
  overflow: hidden;
}

.v-application .primary--text {
  color: #006a37 !important;
  caret-color: #006a37 !important;
}
.checkbox-label label {
  font-size: 12px;
}
.hr-text {
  border: 0;
  line-height: 1em;
  position: relative;
  text-align: center;
  height: 1.5em;
  font-size: 14px;
  margin: 30px 15px;
}

.hr-text::before {
  content: "";
  background: #ddd;
  position: absolute;
  left: 0;
  top: 50%;
  width: 100%;
  height: 1px;
}

.hr-text::after {
  content: attr(data-content);
  position: relative;
  padding: 0 7px;
  line-height: 1.5em;
  color: black;
  background-color: #fff;
}
</style>
