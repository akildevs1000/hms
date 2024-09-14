<template>
  <div class="primary--text" style="font-size: 16px">
    <span
      style="border: 1px solid #5299fc; border-radius: 50px; padding: 10px 25px"
      >{{ label }}</span
    >
  </div>
</template>
<script>
export default {
  props: ["label"],
};
</script>
