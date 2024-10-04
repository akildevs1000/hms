<template>
  <div :class="`${color || 'primary'}--text`" style="font-size: 16px">
    <span>{{ label }}</span>
  </div>
</template>
<script>
export default {
  props: ["label","color"],
};
</script>
