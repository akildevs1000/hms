export default ({ $axios }, inject) => {
  inject("hello", (arg, options) => {
    return $axios.$get(arg, options).then(({ data }) => {
      return data;
    });
  });
};
